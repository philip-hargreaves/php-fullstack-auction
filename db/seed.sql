USE auction_db;

-- Insert roles (if they don't exist)
INSERT IGNORE INTO roles (id, role_name) VALUES
    (1, 'buyer'),
    (2, 'seller');

-- Insert test users with hashed passwords
-- Password: password123
INSERT IGNORE INTO users (id, username, email, password, is_active) VALUES
    (101, 'john_buyer', 'john@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1),
    (102, 'jane_seller', 'jane@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1),
    (201, 'seller_alice', 'alice@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1),
    (202, 'seller_bob', 'bob@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1),
    (203, 'seller_charlie', 'charlie@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1),
    (204, 'seller_diana', 'diana@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1),
    (205, 'seller_edward', 'edward@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1),
    (206, 'seller_fiona', 'fiona@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1),
    (207, 'seller_george', 'george@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1),
    (208, 'seller_helen', 'helen@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1);

-- Assign roles to users
INSERT IGNORE INTO user_roles (user_id, role_id) VALUES
    (101, 1),  -- john is a buyer
    (102, 2),  -- jane is a seller
    (201, 1), (201, 2),
    (202, 1), (202, 2),
    (203, 1), (203, 2),
    (204, 1), (204, 2),
    (205, 1), (205, 2),
    (206, 1), (206, 2),
    (207, 1), (207, 2),
    (208, 1), (208, 2);

INSERT IGNORE INTO categories (id, name, parent_category_id) VALUES
    -- ROOT CATEGORIES
    (1, 'Computers & Office', NULL),
    (2, 'Phones & Tablets', NULL),
    (3, 'Audio', NULL),
    (4, 'Cameras & Photography', NULL),
    (5, 'Wearables', NULL),
    (6, 'Gaming', NULL),

    -- 1. COMPUTER SUBCATEGORIES
    (11, 'Laptops', 1),                -- For ASUS ROG, MacBook Pro, ThinkPad
    (12, 'Desktops & All-in-Ones', 1), -- For Custom PC, Mac Studio, iMac
    (13, 'Monitors', 1),               -- For LG UltraGear, Dell UltraSharp
    (14, 'Keyboards & Mice', 1),       -- For Keychron, Logitech MX Master
    (15, 'Storage & Components', 1),   -- For Samsung T7 SSD

    -- 2. PHONE & TABLET SUBCATEGORIES
    (21, 'Apple iPhone', 2),           -- For iPhone 15/14/13
    (22, 'Samsung Galaxy Phones', 2),  -- For Galaxy S24/S23
    (23, 'Google Pixel Phones', 2),    -- For Pixel 8/7a
    (24, 'Other Smartphones', 2),      -- For OnePlus 12
    (25, 'Tablets', 2),                -- For iPads, Galaxy Tabs, Surface Pro

    -- 3. AUDIO SUBCATEGORIES
    (31, 'Headphones (Over-Ear)', 3),  -- For Sony WH-1000XM5, AirPods Max
    (32, 'Earbuds (In-Ear)', 3),       -- For AirPods Pro, Galaxy Buds
    (33, 'Speakers', 3),               -- For Sonos, Echo, Bose SoundLink

    -- 4. CAMERA SUBCATEGORIES
    (41, 'DSLR & Mirrorless', 4),      -- For Canon EOS, Sony Alpha, Nikon
    (42, 'Action Cameras', 4),         -- For GoPro, DJI Osmo

    -- 5. WEARABLE SUBCATEGORIES
    (51, 'Smartwatches', 5),           -- For Apple Watch, Galaxy Watch
    (52, 'Fitness Trackers', 5),       -- For Fitbit

    -- 6. GAMING SUBCATEGORIES
    (61, 'Consoles', 6),               -- For PS5, Xbox, Switch
    (62, 'Gaming Accessories', 6);     -- For Controllers, Headsets

-- ITEMS (simple: id, seller_id, item_name only)
-- Description and condition are now on auctions table
INSERT IGNORE INTO items (id, seller_id, item_name) VALUES
    (2001, 102, 'ASUS ROG Strix G16 Gaming Laptop RTX 4070'),
    (2002, 201, 'Alienware m18 R2 Gaming Laptop RTX 4080'),
    (2003, 202, 'Razer Blade 17 Gaming Laptop RTX 4060'),
    (2004, 203, 'Lenovo ThinkPad X1 Carbon Gen 11 Intel i7'),
    (2005, 204, 'Dell Latitude 9440 2-in-1 Intel i7'),
    (2006, 205, 'HP EliteBook 840 G10 Intel i7'),
    (2007, 206, 'Apple MacBook Pro 14" M2 Chip 16GB 512GB'),
    (2008, 207, 'Dell XPS 13 Plus Intel i7 16GB 512GB'),
    (2009, 208, 'LG Gram 17 Intel i7 16GB 1TB'),
    (2010, 102, 'Custom Gaming PC RTX 4090 Intel i9'),
    (2011, 201, 'Alienware Aurora R15 Gaming Desktop RTX 4080'),
    (2012, 202, 'Corsair One i300 Compact Gaming PC'),
    (2013, 203, 'Apple Mac Studio M2 Ultra 64GB 1TB'),
    (2014, 204, 'Dell Precision 7865 Tower AMD Threadripper'),
    (2016, 206, 'Apple iMac 24" M1 Chip 8GB 256GB'),
    (2018, 208, 'LG UltraGear 27" 4K 144Hz Gaming Monitor'),
    (2019, 102, 'Samsung Odyssey G9 49" Curved Gaming Monitor'),
    (2020, 201, 'ASUS ROG Swift PG32UCDM 32" 4K OLED'),
    (2021, 202, 'Apple Studio Display 27" 5K Retina'),
    (2022, 203, 'Dell UltraSharp U2723DE 27" 4K'),
    (2024, 205, 'LG 38WN95C-W 38" Ultrawide 4K'),
    (2026, 207, 'Apple iPhone 15 Pro Max 256GB Titanium Blue'),
    (2027, 208, 'Apple iPhone 15 Pro 128GB Natural Titanium'),
    (2028, 102, 'Apple iPhone 14 Pro Max 256GB Deep Purple'),
    (2029, 201, 'Apple iPhone 13 Pro 256GB Sierra Blue'),
    (2030, 202, 'Samsung Galaxy S24 Ultra 512GB Phantom Black'),
    (2031, 203, 'Google Pixel 8 Pro 256GB Obsidian'),
    (2032, 204, 'OnePlus 12 256GB Flowy Emerald'),
    (2033, 205, 'Samsung Galaxy S23 Ultra 256GB Green'),
    (2034, 206, 'Google Pixel 7a 128GB Sea'),
    (2037, 102, 'Apple iPad Pro 12.9" M2 256GB Space Grey'),
    (2038, 201, 'Apple iPad Air 5th Gen M1 256GB Space Grey'),
    (2039, 202, 'Apple iPad Mini 6 256GB Starlight'),
    (2040, 203, 'Samsung Galaxy Tab S9 Ultra 512GB'),
    (2041, 204, 'Samsung Galaxy Tab S9+ 256GB'),
    (2043, 206, 'Microsoft Surface Pro 9 Intel i7 16GB 256GB'),
    (2046, 102, 'Apple Watch Series 9 45mm GPS Cellular'),
    (2047, 201, 'Apple Watch Ultra 2 49mm Titanium'),
    (2048, 202, 'Apple Watch SE 2nd Gen 44mm GPS'),
    (2049, 203, 'Samsung Galaxy Watch 6 Classic 47mm'),
    (2050, 204, 'Google Pixel Watch 2 41mm'),
    (2052, 206, 'Fitbit Charge 6 Fitness Tracker'),
    (2055, 102, 'Canon EOS 5D Mark IV Body'),
    (2056, 201, 'Canon EOS 90D Body'),
    (2057, 202, 'Canon EOS R6 Mark II Body'),
    (2058, 203, 'Nikon D850 Body'),
    (2059, 204, 'Nikon D7500 Body'),
    (2063, 208, 'Sony Alpha A7III Mirrorless Camera Body'),
    (2064, 102, 'Sony Alpha A7IV Body'),
    (2065, 201, 'Sony Alpha A7R V Body'),
    (2066, 202, 'Fujifilm X-T5 Body'),
    (2067, 203, 'Fujifilm X-Pro3 Body'),
    (2069, 205, 'Canon EOS R6 Mark II Body'),
    (2072, 208, 'GoPro Hero 12 Black'),
    (2073, 102, 'GoPro Hero 11 Black'),
    (2075, 202, 'DJI Osmo Action 4'),
    (2077, 204, 'Sony WH-1000XM5 Noise Cancelling Headphones'),
    (2078, 205, 'Apple AirPods Max Space Grey'),
    (2079, 206, 'Bose QuietComfort 45 Headphones'),
    (2080, 207, 'Sennheiser Momentum 4 Wireless'),
    (2081, 208, 'Beats Solo3 Wireless On-Ear Headphones'),
    (2085, 203, 'Apple AirPods Pro 2nd Generation'),
    (2086, 204, 'Sony WF-1000XM5 Earbuds'),
    (2087, 205, 'Samsung Galaxy Buds2 Pro'),
    (2093, 202, 'Sonos Era 300 Smart Speaker'),
    (2094, 203, 'Amazon Echo Studio'),
    (2097, 206, 'Bose SoundLink Flex Portable Speaker'),
    (2101, 201, 'Sony PlayStation 5 Console'),
    (2104, 204, 'Microsoft Xbox Series X Console'),
    (2107, 207, 'Nintendo Switch OLED Model'),
    (2110, 201, 'Sony DualSense Edge Wireless Controller'),
    (2113, 204, 'SteelSeries Arctis Nova Pro Wireless Headset'),
    (2116, 207, 'Razer BlackWidow V4 Pro Mechanical Keyboard'),
    (2119, 201, 'Logitech G Pro X Superlight Gaming Mouse'),
    (2122, 204, 'Keychron Q1 Pro Mechanical Keyboard'),
    (2130, 203, 'Finalmouse Starlight-12 Small Gaming Mouse'),
    (2133, 206, 'Logitech MX Master 3S Wireless Mouse'),
    (2138, 202, 'Samsung T7 Shield 2TB External SSD');

-- AUCTIONS (now includes auction_description, auction_condition)
-- Active Auctions - Ending Soon (1-3 days)
-- Active Auctions - Short Term (< 1 week)
INSERT IGNORE INTO auctions (id, item_id, auction_description, auction_condition, start_datetime, end_datetime, starting_price, reserve_price, auction_status, category_id) VALUES
    (3001, 2001, 'Powerful gaming laptop with Intel i9, 32GB RAM, 1TB SSD, RTX 4070 graphics. 16 inch 165Hz display. Excellent condition, barely used. Perfect for gaming and content creation.', 'Like New', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 1 DAY), 1299.99, 1600.00, 'Active', 11), -- Laptops
    (3002, 2007, 'Excellent condition MacBook Pro with M2 chip, 16GB RAM, 512GB SSD. Perfect for professionals and creatives. Includes original charger and box. Barely used, like new condition.', 'Like New', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 2 DAY), 1899.99, 2200.00, 'Active', 11), -- Laptops
    (3003, 2026, 'Brand new iPhone 15 Pro Max in Titanium Blue. Still sealed in original packaging. Includes all accessories. Unlocked for all carriers.', 'New', DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_ADD(NOW(), INTERVAL 1 DAY), 1099.99, 1400.00, 'Active', 21), -- iPhone
    (3004, 2030, 'Flagship smartphone with S Pen. Excellent condition, barely used. Includes original box and charger. Unlocked.', 'Like New', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 3 DAY), 899.99, 1200.00, 'Active', 22), -- Samsung Phone
    (3005, 2063, 'Professional full-frame mirrorless camera with 24.2MP sensor. Includes body, battery, charger, and 2 memory cards. Excellent condition, well maintained.', 'Like New', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 2 DAY), 1299.99, 1600.00, 'Active', 41), -- DSLR/Mirrorless
    (3006, 2077, 'Premium noise-cancelling wireless headphones. Excellent sound quality and battery life. Includes carrying case and charging cable. Lightly used.', 'Like New', DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_ADD(NOW(), INTERVAL 1 DAY), 249.99, 300.00, 'Active', 31), -- Headphones
    (3007, 2085, 'Premium wireless earbuds with active noise cancellation. Excellent condition, includes charging case and all accessories.', 'Like New', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 3 DAY), 199.99, 280.00, 'Active', 32), -- Earbuds
    (3008, 2101, 'PS5 console with one DualSense controller. Includes all original cables and packaging. Excellent condition, barely used. Comes with 3 games included.', 'Like New', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 2 DAY), 449.99, 550.00, 'Active', 61), -- Consoles
    (3009, 2119, 'Professional gaming mouse with high DPI sensor. Excellent condition, barely used. Perfect for competitive gaming.', 'Like New', DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_ADD(NOW(), INTERVAL 1 DAY), 79.99, 110.00, 'Active', 14), -- Keyboards & Mice
    (3010, 2138, 'Rugged external SSD with excellent performance. Excellent condition, includes USB-C cable. Perfect for backups.', 'Like New', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 3 DAY), 149.99, 220.00, 'Active', 15); -- Storage

-- Active Auctions - Medium Term (1-2 weeks)
INSERT IGNORE INTO auctions (id, item_id, auction_description, auction_condition, start_datetime, end_datetime, starting_price, reserve_price, auction_status, category_id) VALUES
    (3011, 2002, 'High-end gaming laptop with Intel i9, 32GB RAM, 2TB SSD, RTX 4080 graphics. 18 inch 165Hz display. Excellent condition, well maintained. Perfect for serious gamers.', 'Like New', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY), 1999.99, 2400.00, 'Active', 11), -- Laptops
    (3012, 2008, 'Ultrabook with Intel i7, 16GB RAM, 512GB SSD. 13.3 inch OLED display. Lightweight and portable. Good condition, some minor scratches on lid.', 'Used', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 12 DAY), 1299.99, 1600.00, 'Active', 11), -- Laptops
    (3013, 2010, 'High-end custom gaming PC with Intel i9, 64GB RAM, 2TB SSD, RTX 4090 graphics. Excellent condition, perfect for 4K gaming and streaming.', 'Like New', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 11 DAY), 2499.99, 3000.00, 'Active', 12), -- Desktops
    (3014, 2018, '4K gaming monitor with 144Hz refresh rate. Excellent color accuracy and response time. Perfect for gaming and design work. Excellent condition, includes stand.', 'Like New', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 13 DAY), 349.99, 480.00, 'Active', 13), -- Monitors
    (3015, 2027, 'Excellent condition iPhone 15 Pro. Barely used, includes original box and charger. Unlocked for all carriers.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY), 999.99, 1300.00, 'Active', 21), -- iPhone
    (3016, 2031, 'Premium Android phone with excellent camera. Good condition with minor wear. Includes charger and case.', 'Used', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 12 DAY), 699.99, 900.00, 'Active', 23), -- Pixel Phone
    (3017, 2037, 'Large iPad Pro with M2 chip. Excellent condition, includes Apple Pencil 2nd generation and Magic Keyboard. Perfect for professionals.', 'Like New', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 11 DAY), 999.99, 1300.00, 'Active', 25), -- Tablets
    (3018, 2046, 'Latest Apple Watch with fitness tracking. Good condition with some minor wear. Includes charger and band.', 'Used', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 13 DAY), 299.99, 400.00, 'Active', 51), -- Smartwatches
    (3019, 2055, 'Professional full-frame DSLR camera body. Excellent condition with low shutter count. Includes battery, charger, and body cap.', 'Like New', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY), 1899.99, 2200.00, 'Active', 41), -- DSLR/Mirrorless
    (3020, 2064, 'Professional full-frame mirrorless camera. Excellent condition, barely used. Perfect for photography and videography.', 'Like New', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 12 DAY), 1999.99, 2400.00, 'Active', 41), -- DSLR/Mirrorless
    (3021, 2078, 'Premium over-ear headphones with active noise cancellation. Excellent condition, includes case and cable.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 11 DAY), 449.99, 600.00, 'Active', 31), -- Headphones
    (3022, 2086, 'Premium wireless earbuds with noise cancellation. Excellent condition, includes charging case. Great sound quality.', 'Like New', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 13 DAY), 199.99, 280.00, 'Active', 32), -- Earbuds
    (3023, 2093, 'Premium smart speaker with spatial audio. Excellent condition, includes power cable. Perfect for home audio setup.', 'Like New', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY), 399.99, 550.00, 'Active', 33), -- Speakers
    (3024, 2104, 'Next-gen gaming console with 1TB SSD. Excellent condition, includes controller and all cables. Perfect for gaming enthusiasts.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 12 DAY), 399.99, 500.00, 'Active', 61), -- Consoles
    (3025, 2110, 'Premium PS5 controller with customizable buttons. Excellent condition, includes case and accessories.', 'Like New', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 11 DAY), 179.99, 250.00, 'Active', 62), -- Gaming Accessories
    (3026, 2116, 'RGB mechanical keyboard with cherry switches. Excellent condition, includes wrist rest. Perfect for gaming and typing.', 'Like New', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 13 DAY), 199.99, 280.00, 'Active', 14), -- Keyboards & Mice
    (3027, 2122, 'Premium mechanical keyboard with excellent build quality. Excellent condition, includes keycap puller and cable.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY), 149.99, 220.00, 'Active', 14), -- Keyboards & Mice
    (3028, 2130, 'Ultra-lightweight gaming mouse with excellent sensor. Excellent condition, includes accessories. Highly sought after.', 'Like New', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 12 DAY), 99.99, 140.00, 'Active', 14), -- Keyboards & Mice
    (3029, 2133, 'Premium productivity mouse with excellent ergonomics. Excellent condition, includes USB receiver.', 'Like New', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 11 DAY), 79.99, 110.00, 'Active', 14), -- Keyboards & Mice
    (3030, 2004, 'Business laptop with Intel i7, 16GB RAM, 1TB SSD. 14 inch display. Excellent condition, well maintained. Perfect for professionals.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY), 1299.99, 1600.00, 'Active', 11); -- Laptops

-- Active Auctions - Longer Term (3-4 weeks)
INSERT IGNORE INTO auctions (id, item_id, auction_description, auction_condition, start_datetime, end_datetime, starting_price, reserve_price, auction_status, category_id) VALUES
    (3031, 2003, 'Premium gaming laptop with Intel i7, 16GB RAM, 1TB SSD, RTX 4060 graphics. 17.3 inch QHD display. Good condition with minor wear.', 'Used', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 21 DAY), 1499.99, 1800.00, 'Active', 11), -- Laptops
    (3032, 2009, 'Ultra-lightweight laptop with Intel i7, 16GB RAM, 1TB SSD. 17 inch display. Excellent condition, perfect for portability.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 25 DAY), 1199.99, 1500.00, 'Active', 11), -- Laptops
    (3033, 2013, 'Professional workstation with M2 Ultra chip, 64GB RAM, 1TB SSD. Excellent condition, perfect for video editing and 3D rendering.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 22 DAY), 2999.99, 3600.00, 'Active', 12), -- Desktops
    (3034, 2019, 'Ultrawide curved gaming monitor with 240Hz refresh rate. Excellent condition, includes stand and all cables. Perfect for immersive gaming.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 24 DAY), 899.99, 1200.00, 'Active', 13), -- Monitors
    (3035, 2021, 'Professional 5K display with excellent color accuracy. Perfect for creative professionals. Excellent condition, includes stand.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 23 DAY), 1299.99, 1600.00, 'Active', 13), -- Monitors
    (3036, 2028, 'Good condition iPhone 14 Pro Max with minor wear. Includes charger and case. Unlocked for all carriers.', 'Used', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 21 DAY), 799.99, 1000.00, 'Active', 21), -- iPhone
    (3037, 2032, 'High-performance smartphone with fast charging. Excellent condition, includes original accessories.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 25 DAY), 599.99, 800.00, 'Active', 24), -- Other Phones
    (3038, 2038, 'iPad Air with M1 chip, 256GB storage. Space Grey color. Includes Apple Pencil 2nd generation and smart folio case. Perfect condition.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 22 DAY), 599.99, 750.00, 'Active', 25), -- Tablets
    (3039, 2047, 'Premium Apple Watch with titanium case. Excellent condition, includes charger and band. Perfect for outdoor activities.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 24 DAY), 699.99, 900.00, 'Active', 51), -- Smartwatches
    (3040, 2049, 'Premium smartwatch with rotating bezel. Excellent condition, includes charger and band.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 23 DAY), 299.99, 400.00, 'Active', 51), -- Smartwatches
    (3041, 2058, 'High-resolution full-frame DSLR. Excellent condition, well maintained. Perfect for professional photography.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 21 DAY), 1999.99, 2400.00, 'Active', 41), -- DSLR/Mirrorless
    (3042, 2065, 'High-resolution full-frame mirrorless camera. Excellent condition, perfect for landscape and studio photography.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 25 DAY), 2999.99, 3600.00, 'Active', 41), -- DSLR/Mirrorless
    (3043, 2072, 'Latest action camera with excellent stabilization. Excellent condition, includes mounts and accessories.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 22 DAY), 299.99, 400.00, 'Active', 42), -- Action Cameras
    (3044, 2080, 'Premium wireless headphones with excellent sound quality. Excellent condition, includes case and cable.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 24 DAY), 299.99, 400.00, 'Active', 31), -- Headphones
    (3045, 2087, 'High-quality wireless earbuds with active noise cancellation. Good condition, includes charging case.', 'Used', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 23 DAY), 149.99, 220.00, 'Active', 32), -- Earbuds
    (3046, 2094, 'High-fidelity smart speaker with Alexa. Excellent condition, includes power adapter. Perfect for smart home setup.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 21 DAY), 79.99, 110.00, 'Active', 33), -- Speakers
    (3047, 2107, 'Nintendo Switch OLED model with red and blue joy-cons. Includes dock, charger, and 5 games. Excellent condition, well cared for.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 25 DAY), 299.99, 380.00, 'Active', 61), -- Consoles
    (3048, 2113, 'Premium gaming headset with dual battery system. Excellent condition, includes all accessories.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 22 DAY), 249.99, 350.00, 'Active', 62), -- Gaming Accessories
    (3049, 2005, 'Convertible business laptop with Intel i7, 16GB RAM, 512GB SSD. 14 inch touchscreen. Excellent condition, includes stylus.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 24 DAY), 1199.99, 1500.00, 'Active', 11), -- Laptops
    (3050, 2006, 'Professional business laptop with Intel i7, 16GB RAM, 512GB SSD. 14 inch display. Good condition, well maintained.', 'Used', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 23 DAY), 999.99, 1300.00, 'Active', 11), -- Laptops
    (3051, 2011, 'Gaming desktop with Intel i9, 32GB RAM, 1TB SSD, RTX 4080 graphics. Excellent condition, includes keyboard and mouse.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 21 DAY), 1999.99, 2400.00, 'Active', 12), -- Desktops
    (3052, 2012, 'Compact gaming PC with Intel i9, 32GB RAM, 1TB SSD, RTX 4080. Excellent condition, space-saving design.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 25 DAY), 2499.99, 3000.00, 'Active', 12), -- Desktops
    (3053, 2014, 'Professional workstation with AMD Threadripper, 64GB RAM, 2TB SSD, RTX A5000 graphics. Excellent condition, perfect for CAD and rendering.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 22 DAY), 3499.99, 4200.00, 'Active', 12), -- Desktops
    (3054, 2016, 'All-in-one desktop with M1 chip. 24 inch 4.5K Retina display. Excellent condition, includes Magic Mouse and Keyboard.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 24 DAY), 999.99, 1300.00, 'Active', 12), -- Desktops
    (3055, 2020, '4K OLED gaming monitor with 240Hz refresh rate. Excellent picture quality. Perfect for gaming and content creation. Excellent condition.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 23 DAY), 1299.99, 1600.00, 'Active', 13); -- Monitors

-- Active Auctions - Longer Term (continued)
INSERT IGNORE INTO auctions (id, item_id, auction_description, auction_condition, start_datetime, end_datetime, starting_price, reserve_price, auction_status, category_id) VALUES
    (3056, 2022, 'Professional 4K monitor with excellent color accuracy. Perfect for design work. Excellent condition, includes stand.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 21 DAY), 599.99, 800.00, 'Active', 13), -- Monitors
    (3057, 2024, 'Ultrawide curved monitor with excellent color accuracy. Perfect for productivity and gaming. Excellent condition, includes stand.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 25 DAY), 1499.99, 1800.00, 'Active', 13), -- Monitors
    (3058, 2029, 'Excellent condition iPhone 13 Pro. Well maintained, includes original box and charger. Unlocked.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 22 DAY), 699.99, 900.00, 'Active', 21), -- iPhone
    (3059, 2033, 'Flagship smartphone in excellent condition. Includes S Pen, charger, and case. Unlocked.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 24 DAY), 899.99, 1200.00, 'Active', 22), -- Samsung Phone
    (3060, 2034, 'Mid-range smartphone with excellent camera. Good condition, includes charger. Great value for money.', 'Used', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 23 DAY), 299.99, 400.00, 'Active', 23); -- Pixel Phone

-- Scheduled Auctions
INSERT IGNORE INTO auctions (id, item_id, auction_description, auction_condition, start_datetime, end_datetime, starting_price, reserve_price, auction_status, category_id) VALUES
    (3061, 2039, 'Compact iPad Mini in excellent condition. Includes Apple Pencil 2nd generation and case. Perfect for portability.', 'Like New', DATE_ADD(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 8 DAY), 449.99, 600.00, 'Scheduled', 25), -- Tablets
    (3062, 2040, 'Large tablet with S Pen included. Excellent condition, barely used. Perfect for productivity and creativity.', 'Like New', DATE_ADD(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 9 DAY), 799.99, 1000.00, 'Scheduled', 25), -- Tablets
    (3063, 2041, 'Premium Android tablet in excellent condition. Includes S Pen and keyboard cover. Perfect for work and entertainment.', 'Like New', DATE_ADD(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY), 599.99, 800.00, 'Scheduled', 25); -- Tablets

-- Finished Auctions (previously Sold)
INSERT IGNORE INTO auctions (id, item_id, auction_description, auction_condition, start_datetime, end_datetime, starting_price, reserve_price, auction_status, category_id) VALUES
    (3064, 2043, '2-in-1 tablet with detachable keyboard. Excellent condition, includes Surface Pen and keyboard cover.', 'Like New', '2024-12-01 10:00:00', '2024-12-15 12:00:00', 999.99, 1300.00, 'Finished', 25), -- Tablets
    (3065, 2048, 'Budget-friendly Apple Watch in excellent condition. Includes charger and band. Perfect for fitness tracking.', 'Like New', '2024-12-02 14:00:00', '2024-12-16 15:00:00', 199.99, 280.00, 'Finished', 51); -- Smartwatches

-- BIDS
# INSERT IGNORE INTO bids (id, buyer_id, auction_id, bid_amount, bid_datetime) VALUES
#     (4001, 201, 3001, 1350.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4002, 203, 3001, 1420.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4003, 205, 3001, 1480.00, DATE_SUB(NOW(), INTERVAL 12 HOUR)),
#     (4004, 202, 3002, 1950.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (4005, 204, 3002, 2050.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4006, 201, 3003, 1150.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
#     (4007, 203, 3003, 1200.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (4008, 205, 3003, 1250.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4009, 207, 3003, 1300.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4010, 202, 3004, 950.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4011, 206, 3004, 1000.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4012, 201, 3005, 1350.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (4013, 204, 3005, 1400.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4014, 208, 3005, 1450.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4015, 202, 3006, 270.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4016, 203, 3007, 220.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4017, 205, 3007, 240.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4018, 201, 3008, 480.00, DATE_SUB(NOW(), INTERVAL 5 DAY)),
#     (4019, 202, 3008, 500.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
#     (4020, 204, 3008, 520.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (4021, 206, 3008, 530.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4022, 208, 3008, 540.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4023, 203, 3009, 90.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4024, 201, 3010, 170.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4025, 205, 3010, 190.00, DATE_SUB(NOW(), INTERVAL 1 DAY));
#
# -- More bids (high interest auctions)
# INSERT IGNORE INTO bids (id, buyer_id, auction_id, bid_amount, bid_datetime) VALUES
#     (4026, 201, 3011, 2100.00, DATE_SUB(NOW(), INTERVAL 8 DAY)),
#     (4027, 202, 3011, 2150.00, DATE_SUB(NOW(), INTERVAL 7 DAY)),
#     (4028, 203, 3011, 2200.00, DATE_SUB(NOW(), INTERVAL 6 DAY)),
#     (4029, 204, 3011, 2250.00, DATE_SUB(NOW(), INTERVAL 5 DAY)),
#     (4030, 205, 3011, 2280.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
#     (4031, 206, 3011, 2300.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (4032, 207, 3011, 2320.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4033, 208, 3011, 2350.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4034, 201, 3012, 1350.00, DATE_SUB(NOW(), INTERVAL 6 DAY)),
#     (4035, 203, 3012, 1400.00, DATE_SUB(NOW(), INTERVAL 5 DAY)),
#     (4036, 205, 3012, 1450.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
#     (4037, 206, 3012, 1480.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (4038, 207, 3012, 1500.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4039, 208, 3012, 1520.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4040, 201, 3013, 2600.00, DATE_SUB(NOW(), INTERVAL 10 DAY)),
#     (4041, 202, 3013, 2650.00, DATE_SUB(NOW(), INTERVAL 9 DAY)),
#     (4042, 203, 3013, 2700.00, DATE_SUB(NOW(), INTERVAL 8 DAY)),
#     (4043, 204, 3013, 2750.00, DATE_SUB(NOW(), INTERVAL 7 DAY)),
#     (4044, 205, 3013, 2800.00, DATE_SUB(NOW(), INTERVAL 6 DAY)),
#     (4045, 206, 3013, 2820.00, DATE_SUB(NOW(), INTERVAL 5 DAY)),
#     (4046, 207, 3013, 2850.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
#     (4047, 208, 3013, 2880.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (4048, 201, 3013, 2900.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4049, 202, 3013, 2920.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4050, 203, 3014, 400.00, DATE_SUB(NOW(), INTERVAL 5 DAY)),
#     (4051, 205, 3014, 420.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
#     (4052, 206, 3014, 440.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (4053, 207, 3014, 450.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4054, 208, 3014, 460.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4055, 201, 3015, 1050.00, DATE_SUB(NOW(), INTERVAL 7 DAY)),
#     (4056, 202, 3015, 1100.00, DATE_SUB(NOW(), INTERVAL 6 DAY)),
#     (4057, 204, 3015, 1150.00, DATE_SUB(NOW(), INTERVAL 5 DAY)),
#     (4058, 205, 3015, 1180.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
#     (4059, 206, 3015, 1200.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (4060, 207, 3015, 1220.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4061, 208, 3015, 1250.00, DATE_SUB(NOW(), INTERVAL 1 DAY));
#
# -- More bids on various auctions
# INSERT IGNORE INTO bids (id, buyer_id, auction_id, bid_amount, bid_datetime) VALUES
#     (4062, 201, 3016, 750.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
#     (4063, 203, 3016, 780.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (4064, 205, 3016, 820.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4065, 207, 3016, 850.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4066, 202, 3017, 1050.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (4067, 204, 3017, 1100.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4068, 206, 3017, 1150.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4069, 201, 3018, 320.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4070, 203, 3018, 350.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4071, 201, 3019, 2000.00, DATE_SUB(NOW(), INTERVAL 6 DAY)),
#     (4072, 202, 3019, 2050.00, DATE_SUB(NOW(), INTERVAL 5 DAY)),
#     (4073, 204, 3019, 2100.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
#     (4074, 205, 3019, 2120.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (4075, 206, 3019, 2150.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4076, 208, 3019, 2180.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4077, 201, 3020, 2100.00, DATE_SUB(NOW(), INTERVAL 5 DAY)),
#     (4078, 203, 3020, 2150.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
#     (4079, 205, 3020, 2200.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (4080, 207, 3020, 2250.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4081, 208, 3020, 2300.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4082, 202, 3021, 500.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (4083, 204, 3021, 550.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4084, 206, 3021, 580.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4085, 201, 3022, 220.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4086, 203, 3022, 250.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4087, 202, 3023, 420.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
#     (4088, 204, 3023, 450.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (4089, 206, 3023, 480.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4090, 208, 3023, 520.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4091, 201, 3024, 420.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (4092, 203, 3024, 450.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4093, 205, 3024, 480.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (4094, 202, 3025, 200.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (4095, 204, 3025, 230.00, DATE_SUB(NOW(), INTERVAL 1 DAY));
#
# -- Bids on finished auctions
# INSERT IGNORE INTO bids (id, buyer_id, auction_id, bid_amount, bid_datetime) VALUES
#     (4096, 201, 3064, 1050.00, '2024-12-10 10:00:00'),
#     (4097, 203, 3064, 1100.00, '2024-12-11 14:00:00'),
#     (4098, 205, 3064, 1200.00, '2024-12-12 16:00:00'),
#     (4099, 202, 3065, 220.00, '2024-12-11 11:00:00'),
#     (4100, 204, 3065, 250.00, '2024-12-13 15:00:00');
#
# -- Update winning_bid_id for finished auctions
# UPDATE auctions SET winning_bid_id = 4098 WHERE id = 3064;
# UPDATE auctions SET winning_bid_id = 4100 WHERE id = 3065;
#
# -- WATCHLISTS
# INSERT IGNORE INTO watchlists (user_id, auction_id, watched_datetime) VALUES
#     (101, 3001, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (101, 3003, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (101, 3008, DATE_SUB(NOW(), INTERVAL 5 DAY)),
#     (101, 3011, DATE_SUB(NOW(), INTERVAL 4 DAY)),
#     (101, 3015, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (101, 3020, DATE_SUB(NOW(), INTERVAL 6 DAY));
#
# INSERT IGNORE INTO watchlists (user_id, auction_id, watched_datetime) VALUES
#     (201, 3002, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (201, 3004, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (201, 3006, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (201, 3012, DATE_SUB(NOW(), INTERVAL 4 DAY)),
#     (201, 3016, DATE_SUB(NOW(), INTERVAL 5 DAY)),
#     (201, 3021, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (201, 3023, DATE_SUB(NOW(), INTERVAL 3 DAY));
#
# INSERT IGNORE INTO watchlists (user_id, auction_id, watched_datetime) VALUES
#     (202, 3001, DATE_SUB(NOW(), INTERVAL 4 DAY)),
#     (202, 3005, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (202, 3007, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (202, 3013, DATE_SUB(NOW(), INTERVAL 5 DAY)),
#     (202, 3017, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (202, 3019, DATE_SUB(NOW(), INTERVAL 6 DAY));
#
# INSERT IGNORE INTO watchlists (user_id, auction_id, watched_datetime) VALUES
#     (203, 3002, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (203, 3010, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (203, 3014, DATE_SUB(NOW(), INTERVAL 4 DAY)),
#     (203, 3020, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (203, 3024, DATE_SUB(NOW(), INTERVAL 5 DAY));
#
# INSERT IGNORE INTO watchlists (user_id, auction_id, watched_datetime) VALUES
#     (204, 3001, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (204, 3003, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (204, 3009, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (204, 3015, DATE_SUB(NOW(), INTERVAL 4 DAY));
#
# INSERT IGNORE INTO watchlists (user_id, auction_id, watched_datetime) VALUES
#     (205, 3008, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (205, 3011, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (205, 3024, DATE_SUB(NOW(), INTERVAL 4 DAY)),
#     (205, 3025, DATE_SUB(NOW(), INTERVAL 1 DAY));
#
# INSERT IGNORE INTO watchlists (user_id, auction_id, watched_datetime) VALUES
#     (206, 3005, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (206, 3019, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (206, 3020, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (206, 3021, DATE_SUB(NOW(), INTERVAL 4 DAY)),
#     (206, 3022, DATE_SUB(NOW(), INTERVAL 5 DAY));
#
# INSERT IGNORE INTO watchlists (user_id, auction_id, watched_datetime) VALUES
#     (207, 3004, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (207, 3006, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (207, 3012, DATE_SUB(NOW(), INTERVAL 4 DAY)),
#     (207, 3018, DATE_SUB(NOW(), INTERVAL 1 DAY)),
#     (207, 3023, DATE_SUB(NOW(), INTERVAL 5 DAY));
#
# INSERT IGNORE INTO watchlists (user_id, auction_id, watched_datetime) VALUES
#     (208, 3002, DATE_SUB(NOW(), INTERVAL 4 DAY)),
#     (208, 3010, DATE_SUB(NOW(), INTERVAL 3 DAY)),
#     (208, 3013, DATE_SUB(NOW(), INTERVAL 2 DAY)),
#     (208, 3019, DATE_SUB(NOW(), INTERVAL 1 DAY));
