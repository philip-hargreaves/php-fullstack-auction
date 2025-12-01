# pip install requests

import requests
import base64
import os

# --- CONFIGURATION ---
# Same API Key as your PHP script
IMGBB_API_KEY = ''
UPLOAD_URL = 'https://api.imgbb.com/1/upload'

def upload_to_imgbb(file_path):
    """
    Uploads an image to ImgBB and returns the URL.
    """

    # 1. Basic Validation (Check if file exists)
    if not os.path.exists(file_path):
        print(f"Error: File not found at {file_path}")
        return None

    try:
        # 2. Read and Encode file
        # PHP equivalent: $image_data = file_get_contents(...); base64_encode($image_data);
        with open(file_path, "rb") as file:
            encoded_string = base64.b64encode(file.read())

        # 3. Prepare Payload
        payload = {
            "key": IMGBB_API_KEY,
            "image": encoded_string,
            "name": os.path.basename(file_path) # Optional: passes the filename
        }

        # 4. Execute Request
        # PHP equivalent: curl_exec($curl);
        response = requests.post(UPLOAD_URL, data=payload)

        # Raise error for HTTP codes (400, 404, 500 etc)
        response.raise_for_status()

        # 5. Decode Response
        # PHP equivalent: json_decode($response, true);
        result = response.json()

        # 6. Check Success flag and return URL
        if result.get('success'):
            return result['data']['url']
        else:
            error_msg = result.get('error', {}).get('message', 'Unknown Error')
            print(f"ImgBB API Failed: {error_msg}")
            return None

    except requests.exceptions.RequestException as e:
        print(f"Network or HTTP Error: {e}")
        return None
    except Exception as e:
        print(f"Error: {e}")
        return None

# --- Main Execution Block ---
if __name__ == "__main__":
    # Check if user provided a file path in command line
    image_paths = [
        # your image path
        r"C:\Users\user\Downloads\th.jpg"
    ]

    for image_path in image_paths:
        url = upload_to_imgbb(image_path)
        print("Image URL:", url)


    print("\nSUCCESS!")