import re
import os
import requests
import base64
import random
import time
import tempfile
from datetime import datetime

# Selenium Imports
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

# CONFIGURATION
IMGBB_API_KEY = ''
UPLOAD_URL = 'https://api.imgbb.com/1/upload'
INPUT_SQL_FILE = r'C:\Repository\fullstack-auction-app\db\seed_1.0.sql'
OUTPUT_SQL_FILE = r'C:\Repository\fullstack-auction-app\db\auction_images_seed.sql'

# IMGBB UPLOADER
def upload_to_imgbb(file_path):
    if not os.path.exists(file_path): return None
    try:
        with open(file_path, "rb") as file:
            encoded_string = base64.b64encode(file.read())
        payload = {
            "key": IMGBB_API_KEY,
            "image": encoded_string,
            "name": os.path.basename(file_path)
        }
        response = requests.post(UPLOAD_URL, data=payload)
        response.raise_for_status()
        result = response.json()
        if result.get('success'):
            print(f"  [OK] Uploaded to ImgBB")
            return result['data']['url']
    except Exception as e:
        print(f"  [ERR] Upload failed: {e}")
    return None

# GOOGLE SCRAPER
def get_images_from_google_robust(query, count):
    print(f"--- Searching Google for: {query} (Target: {count}) ---")

    chrome_options = Options()
    chrome_options.add_argument("--no-sandbox")
    chrome_options.add_argument("--disable-dev-shm-usage")
    chrome_options.add_argument("--disable-blink-features=AutomationControlled")
    chrome_options.add_experimental_option("excludeSwitches", ["enable-automation"])
    chrome_options.add_experimental_option('useAutomationExtension', False)

    driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=chrome_options)

    # 1. Go to Google Images
    search_url = f"https://www.google.com/search?q={query}&tbm=isch"
    driver.get(search_url)
    time.sleep(2)

    # 2. Handle "Before you continue" Cookie Popup
    try:
        # Look for buttons with text "Reject all", "Accept all", etc.
        buttons = driver.find_elements(By.TAG_NAME, "button")
        for btn in buttons:
            if "reject" in btn.text.lower() or "accept" in btn.text.lower():
                btn.click()
                time.sleep(2)
                break
    except:
        pass

    # 3. Scroll down
    driver.execute_script("window.scrollTo(0, 1000);")
    time.sleep(1)

    # 4. Find Images
    print("   [INFO] Locating thumbnails...")

    # Wait for thumbnails to load
    time.sleep(1)

    # Find all thumbnail elements
    thumbnails = driver.find_elements(By.CLASS_NAME, "eA0Zlc")

    downloaded_files = []
    found_count = 0

    # Fake User-Agent for downloading
    headers = {'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0'}

    # Loop through the thumbnails we found
    for i, thumb in enumerate(thumbnails):
        if found_count >= count:
            break

        try:
            print(f"   [INFO] Clicking thumbnail {i+1}...")

            # Click the thumbnail to open the side panel
            thumb.click()
            time.sleep(1)

            # Find the Large Image in the side panel
            large_images = driver.find_elements(By.CSS_SELECTOR, ".sFlh5c.FyHeAf.iPVvYb")

            # Usually there is only one "active" large image, but find_elements returns a list
            for img in large_images:
                # Check dimensions (High Res check)
                width = img.get_attribute('width')
                height = img.get_attribute('height')

                # Skip if too small (width < 300)
                if width and int(width) < 250: continue
                if height and int(height) < 250: continue

                src = img.get_attribute('src')

                # If src is empty, check data-src (sometimes used for lazy loading)
                if not src: src = img.get_attribute('data-src')
                if not src: continue

                # Skip Google generic icons
                if "google" in src and "logo" in src: continue

                # --- DOWNLOAD LOGIC ---
                temp_file = tempfile.NamedTemporaryFile(delete=False, suffix=".jpg")

                try:
                    if "data:image" in src:
                        # Handle Base64
                        header, encoded = src.split(",", 1)
                        data = base64.b64decode(encoded)
                        temp_file.write(data)
                    else:
                        # Handle URL
                        img_data = requests.get(src, headers=headers, timeout=5).content
                        temp_file.write(img_data)

                    temp_file.close()

                    # Final validation: File size > 1KB
                    if os.path.getsize(temp_file.name) > 1000:
                        downloaded_files.append(temp_file.name)
                        found_count += 1
                        print(f"   [SUCCESS] Found image {found_count}: {src[:30]}...")
                        # Break inner loop (we only need one large image per thumbnail click)
                        break
                    else:
                        os.remove(temp_file.name)
                except Exception as e:
                    print(f"   [ERR] Download error: {e}")
                    continue

        except Exception as e:
            print(f"   [ERR] Could not click thumbnail {i}: {e}")
            continue

    driver.quit()
    return downloaded_files

# PARSING LOGIC
def parse_seeds(file_path):
    if not os.path.exists(file_path):
        print(f"Error: {file_path} not found.")
        return {}

    items_map = {}
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # Regex 1: Items
    item_matches = re.findall(r"INSERT.*?INTO items.*?VALUES\s*(.*?;)", content, re.DOTALL)
    for block in item_matches:
        # Flexible matching for (id, seller, ..., 'name')
        # Looks for the LAST quoted string in the parenthesis usually
        rows = re.findall(r"\((.*?)\)", block)
        for row in rows:
            parts = row.split(',')
            try:
                vid = parts[0].strip()
                # Find the item name (usually the string part)
                vname = re.findall(r"'([^']*)'", row)[-1]
                items_map[int(vid)] = {'name': vname, 'auction_ids': []}
            except:
                continue

    # Regex 2: Auctions
    auction_matches = re.findall(r"INSERT.*?INTO auctions.*?VALUES\s*(.*?;)", content, re.DOTALL)
    for block in auction_matches:
        rows = re.findall(r"\((.*?)\)", block)
        for row in rows:
            parts = row.split(',')
            try:
                aid = parts[0].strip()
                item_id = parts[1].strip()
                if int(item_id) in items_map:
                    items_map[int(item_id)]['auction_ids'].append(int(aid))
            except:
                continue

    return items_map

# --- 4. MAIN ---
def main():
    print("1. Parsing SQL Seeds...")
    items_data = parse_seeds(INPUT_SQL_FILE)

    if not items_data:
        print("No items parsed.")
        return

    print(f"Found {len(items_data)} items.")
    sql_statements = []

    for item_id, data in items_data.items():
        if not data['auction_ids']: continue

        item_name = data['name']
        print(f"\nProcessing Item {item_id}: {item_name}")

        # Determine Count (1 to 5 images)
        img_count = random.choice([1, 2, 2, 2, 3, 3, 3, 4, 4, 5])

        # Get Images
        temp_files = get_images_from_google_robust(item_name, img_count)

        if not temp_files:
            print("  [WARN] No images found. Trying broader search...")
            # Fallback: try searching just the first 3 words of the item name
            short_name = " ".join(item_name.split()[:3])
            temp_files = get_images_from_google_robust(short_name, img_count)

        valid_urls = []
        for tmp_path in temp_files:
            url = upload_to_imgbb(tmp_path)
            if url: valid_urls.append(url)
            try: os.remove(tmp_path)
            except: pass

        # Generate SQL
        for auction_id in data['auction_ids']:
            for index, img_url in enumerate(valid_urls):
                is_main = 1 if index == 0 else 0
                val = f"({auction_id}, '{img_url}', {is_main}, NOW())"
                sql_statements.append(val)

    if sql_statements:
        full_sql = "INSERT IGNORE INTO auction_images (auction_id, image_url, is_main, uploaded_datetime) VALUES\n"
        full_sql += ",\n".join(sql_statements) + ";"
        with open(OUTPUT_SQL_FILE, "w", encoding='utf-8') as f:
            f.write(full_sql)
        print(f"\nSUCCESS! SQL generated at {OUTPUT_SQL_FILE}")
    else:
        print("\nNo data generated.")

if __name__ == "__main__":
    main()