USE auction_db;

-- Insert roles (if they don't exist)
INSERT IGNORE INTO roles (id, role_name) VALUES
    (1, 'buyer'),
    (2, 'seller'),
    (3, 'admin');

-- Insert test users with hashed passwords
-- Password: password123 (hashed with bcrypt)
INSERT IGNORE INTO users (id, username, email, password, is_active, created_datetime) VALUES
    (101, 'john_buyer', 'john@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-01-15 10:00:00'),
    (102, 'jane_seller', 'jane@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-02-20 14:30:00'),
    (201, 'seller_alice', 'alice@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-03-10 09:15:00'),
    (202, 'seller_bob', 'bob@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-03-25 16:45:00'),
    (203, 'seller_charlie', 'charlie@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-04-05 11:20:00'),
    (204, 'seller_diana', 'diana@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-04-18 13:10:00'),
    (205, 'seller_edward', 'edward@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-05-12 08:30:00'),
    (206, 'seller_fiona', 'fiona@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-05-28 15:00:00'),
    (207, 'seller_george', 'george@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-06-10 10:45:00'),
    (208, 'seller_helen', 'helen@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-06-22 12:15:00'),
    (209, 'buyer_ivan', 'ivan@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-07-05 09:20:00'),
    (210, 'buyer_julia', 'julia@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-07-12 14:30:00'),
    (211, 'buyer_kevin', 'kevin@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-07-18 11:15:00'),
    (212, 'buyer_lisa', 'lisa@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-07-25 16:45:00'),
    (213, 'buyer_mike', 'mike@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-08-01 10:00:00'),
    (214, 'seller_nina', 'nina@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-08-08 13:20:00'),
    (215, 'seller_oscar', 'oscar@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-08-15 08:30:00'),
    (216, 'seller_paula', 'paula@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-08-22 15:10:00'),
    (217, 'seller_quinn', 'quinn@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-08-29 11:40:00'),
    (218, 'seller_rachel', 'rachel@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-09-05 14:25:00'),
    (219, 'user_sam', 'sam@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-09-12 09:50:00'),
    (220, 'user_tina', 'tina@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-09-19 12:15:00'),
    (221, 'user_umar', 'umar@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-09-26 10:30:00'),
    (222, 'user_victor', 'victor@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-10-03 16:20:00'),
    (223, 'user_wendy', 'wendy@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-10-10 13:45:00'),
    (224, 'user_xavier', 'xavier@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-10-17 11:00:00'),
    (225, 'user_yara', 'yara@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-10-24 14:35:00'),
    (226, 'user_zack', 'zack@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-10-31 09:25:00'),
    (227, 'buyer_anna', 'anna@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-11-07 15:50:00'),
    (228, 'buyer_ben', 'ben@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-11-14 10:15:00'),
    (229, 'buyer_cara', 'cara@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-11-21 12:40:00'),
    (230, 'buyer_dave', 'dave@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-11-28 08:55:00'),
    (231, 'buyer_ella', 'ella@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-12-05 13:20:00'),
    (232, 'seller_frank', 'frank@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-12-12 11:10:00'),
    (233, 'seller_grace', 'grace@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-12-19 16:30:00'),
    (301, 'admin', 'admin@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1, '2024-01-01 00:00:00');

-- Assign roles to users
-- User 101 (john_buyer) - buyer only
-- User 102 (jane_seller) - seller only
-- Users 201-208 - buyer + seller (both roles)
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
    (208, 1), (208, 2),
    (209, 1),  -- ivan is a buyer
    (210, 1),  -- julia is a buyer
    (211, 1),  -- kevin is a buyer
    (212, 1),  -- lisa is a buyer
    (213, 1),  -- mike is a buyer
    (214, 2),  -- nina is a seller
    (215, 2),  -- oscar is a seller
    (216, 2),  -- paula is a seller
    (217, 2),  -- quinn is a seller
    (218, 2),  -- rachel is a seller
    (219, 1), (219, 2),  -- sam is both
    (220, 1), (220, 2),  -- tina is both
    (221, 1), (221, 2),  -- umar is both
    (222, 1), (222, 2),  -- victor is both
    (223, 1), (223, 2),  -- wendy is both
    (224, 1), (224, 2),  -- xavier is both
    (225, 1), (225, 2),  -- yara is both
    (226, 1), (226, 2),  -- zack is both
    (227, 1),  -- anna is a buyer
    (228, 1),  -- ben is a buyer
    (229, 1),  -- cara is a buyer
    (230, 1),  -- dave is a buyer
    (231, 1),  -- ella is a buyer
    (232, 2),  -- frank is a seller
    (233, 2),  -- grace is a seller
    (301, 1), (301, 3);  -- admin is both buyer and admin

-- CATEGORIES (based on comments in old seed)
-- TOP-LEVEL CATEGORY: Computers
INSERT IGNORE INTO categories (id, name, parent_category_id) VALUES
    (1, 'Computers', NULL),
    -- Subcategory Level 1: Laptops
    (11, 'Laptops', 1),
    --   Subcategory Level 2: Gaming Laptops
    (111, 'Gaming Laptops', 11),
    --   Subcategory Level 2: Business Laptops
    (112, 'Business Laptops', 11),
    --   Subcategory Level 2: Ultrabooks
    (113, 'Ultrabooks', 11),
    -- Subcategory Level 1: Desktops
    (12, 'Desktops', 1),
    --   Subcategory Level 2: Gaming PCs
    (121, 'Gaming PCs', 12),
    --   Subcategory Level 2: Workstations
    (122, 'Workstations', 12),
    --   Subcategory Level 2: All-in-Ones
    (123, 'All-in-Ones', 12),
    -- Subcategory Level 1: Monitors
    (13, 'Monitors', 1),
    --   Subcategory Level 2: Gaming Monitors
    (131, 'Gaming Monitors', 13),
    --   Subcategory Level 2: Professional Displays
    (132, 'Professional Displays', 13),
    --   Subcategory Level 2: Ultrawide Monitors
    (133, 'Ultrawide Monitors', 13);

-- TOP-LEVEL CATEGORY: Mobile Devices
INSERT IGNORE INTO categories (id, name, parent_category_id) VALUES
    (2, 'Mobile Devices', NULL),
    -- Subcategory Level 1: Smartphones
    (21, 'Smartphones', 2),
    --   Subcategory Level 2: iPhone
    (211, 'iPhone', 21),
    --   Subcategory Level 2: Android Flagship
    (212, 'Android Flagship', 21),
    --   Subcategory Level 2: Mid-Range
    (213, 'Mid-Range', 21),
    -- Subcategory Level 1: Tablets
    (22, 'Tablets', 2),
    --   Subcategory Level 2: iPad
    (221, 'iPad', 22),
    --   Subcategory Level 2: Android Tablets
    (222, 'Android Tablets', 22),
    --   Subcategory Level 2: 2-in-1
    (223, '2-in-1', 22),
    -- Subcategory Level 1: Smartwatches
    (23, 'Smartwatches', 2),
    --   Subcategory Level 2: Apple Watch
    (231, 'Apple Watch', 23),
    --   Subcategory Level 2: Wear OS
    (232, 'Wear OS', 23),
    --   Subcategory Level 2: Fitness Trackers
    (233, 'Fitness Trackers', 23);

-- TOP-LEVEL CATEGORY: Cameras
INSERT IGNORE INTO categories (id, name, parent_category_id) VALUES
    (3, 'Cameras', NULL),
    -- Subcategory Level 1: DSLR
    (31, 'DSLR', 3),
    --   Subcategory Level 2: Canon DSLR
    (311, 'Canon DSLR', 31),
    --   Subcategory Level 2: Nikon DSLR
    (312, 'Nikon DSLR', 31),
    -- Subcategory Level 1: Mirrorless
    (32, 'Mirrorless', 3),
    --   Subcategory Level 2: Sony Mirrorless
    (321, 'Sony Mirrorless', 32),
    --   Subcategory Level 2: Fujifilm Mirrorless
    (322, 'Fujifilm Mirrorless', 32),
    --   Subcategory Level 2: Canon Mirrorless
    (323, 'Canon Mirrorless', 32),
    -- Subcategory Level 1: Action Cameras
    (33, 'Action Cameras', 3),
    --   Subcategory Level 2: GoPro
    (331, 'GoPro', 33),
    --   Subcategory Level 2: DJI
    (332, 'DJI', 33);

-- TOP-LEVEL CATEGORY: Audio
INSERT IGNORE INTO categories (id, name, parent_category_id) VALUES
    (4, 'Audio', NULL),
    -- Subcategory Level 1: Headphones
    (41, 'Headphones', 4),
    --   Subcategory Level 2: Over-Ear
    (411, 'Over-Ear', 41),
    --   Subcategory Level 2: On-Ear
    (412, 'On-Ear', 41),
    -- Subcategory Level 1: Earbuds
    (42, 'Earbuds', 4),
    --   Subcategory Level 2: True Wireless
    (421, 'True Wireless', 42),
    -- Subcategory Level 1: Speakers
    (43, 'Speakers', 4),
    --   Subcategory Level 2: Smart Speakers
    (431, 'Smart Speakers', 43),
    --   Subcategory Level 2: Portable
    (432, 'Portable', 43);

-- TOP-LEVEL CATEGORY: Gaming
INSERT IGNORE INTO categories (id, name, parent_category_id) VALUES
    (5, 'Gaming', NULL),
    -- Subcategory Level 1: Consoles
    (51, 'Consoles', 5),
    --   Subcategory Level 2: PlayStation
    (511, 'PlayStation', 51),
    --   Subcategory Level 2: Xbox
    (512, 'Xbox', 51),
    --   Subcategory Level 2: Nintendo
    (513, 'Nintendo', 51),
    -- Subcategory Level 1: Accessories
    (52, 'Accessories', 5),
    --   Subcategory Level 2: Controllers
    (521, 'Controllers', 52),
    --   Subcategory Level 2: Headsets
    (522, 'Headsets', 52),
    --   Subcategory Level 2: Keyboards
    (523, 'Keyboards', 52),
    --   Subcategory Level 2: Mice
    (524, 'Mice', 52);

-- TOP-LEVEL CATEGORY: Accessories
INSERT IGNORE INTO categories (id, name, parent_category_id) VALUES
    (6, 'Accessories', NULL),
    -- Subcategory Level 1: Keyboards
    (61, 'Keyboards', 6),
    --   Subcategory Level 2: Mechanical
    (611, 'Mechanical', 61),
    -- Subcategory Level 1: Mice
    (62, 'Mice', 6),
    --   Subcategory Level 2: Gaming
    (621, 'Gaming', 62),
    --   Subcategory Level 2: Wireless
    (622, 'Wireless', 62),
    -- Subcategory Level 1: Storage
    (63, 'Storage', 6),
    --   Subcategory Level 2: External Drives
    (631, 'External Drives', 63);

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
    (2138, 202, 'Samsung T7 Shield 2TB External SSD'),
    (2139, 201, 'Alienware m18 R2 Gaming Laptop RTX 4090'),
    -- Additional items for more sold auctions
    (2140, 201, 'MSI Raider GE78 HX Gaming Laptop RTX 4090'),
    (2141, 202, 'ASUS ROG Zephyrus M16 Gaming Laptop RTX 4080'),
    (2142, 203, 'HP Omen 17 Gaming Laptop RTX 4070'),
    (2143, 204, 'Acer Predator Helios 16 Gaming Laptop RTX 4060'),
    (2144, 205, 'Lenovo Legion Pro 7i Gaming Laptop RTX 4080'),
    (2145, 206, 'MSI Aegis RS Gaming Desktop RTX 4070'),
    (2146, 207, 'HP Omen 45L Gaming Desktop RTX 4080'),
    (2147, 208, 'Apple iPhone 14 Pro 256GB Space Black'),
    (2148, 102, 'Apple iPhone 13 Pro Max 256GB Gold'),
    (2149, 201, 'Samsung Galaxy S23 Ultra 512GB Phantom Black'),
    (2150, 202, 'Google Pixel 7 Pro 256GB Snow'),
    (2151, 203, 'Canon EOS R5 Body'),
    (2152, 204, 'Canon EOS R8 Body'),
    (2153, 205, 'Canon EOS 6D Mark II Body'),
    (2154, 206, 'Sony Alpha A7C Body'),
    (2155, 207, 'Sony Alpha A6600 Body'),
    (2156, 208, 'Fujifilm X-H2 Body'),
    (2157, 102, 'Nikon Z6 II Body'),
    (2158, 201, 'Nikon Z7 II Body');

-- AUCTIONS (now includes auction_description, auction_condition, category_id)
-- Active Auctions - Ending Soon (1-3 days) - 10 auctions
INSERT IGNORE INTO auctions (id, item_id, category_id, auction_description, auction_condition, start_datetime, end_datetime, starting_price, reserve_price, auction_status) VALUES
    (3001, 2001, 111, 'Powerful gaming laptop with Intel i9, 32GB RAM, 1TB SSD, RTX 4070 graphics. 16 inch 165Hz display. Excellent condition, barely used. Perfect for gaming and content creation.', 'Like New', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 1 DAY), 1299.99, 1600.00, 'Active'),
    (3002, 2007, 113, 'Excellent condition MacBook Pro with M2 chip, 16GB RAM, 512GB SSD. Perfect for professionals and creatives. Includes original charger and box. Barely used, like new condition.', 'Like New', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 2 DAY), 1899.99, 2200.00, 'Active'),
    (3003, 2026, 211, 'Brand new iPhone 15 Pro Max in Titanium Blue. Still sealed in original packaging. Includes all accessories. Unlocked for all carriers.', 'New', DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_ADD(NOW(), INTERVAL 1 DAY), 1099.99, 1400.00, 'Active'),
    (3004, 2030, 212, 'Flagship smartphone with S Pen. Excellent condition, barely used. Includes original box and charger. Unlocked.', 'Like New', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 3 DAY), 899.99, 1200.00, 'Active'),
    (3005, 2063, 321, 'Professional full-frame mirrorless camera with 24.2MP sensor. Includes body, battery, charger, and 2 memory cards. Excellent condition, well maintained.', 'Like New', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 2 DAY), 1299.99, 1600.00, 'Active'),
    (3006, 2077, 411, 'Premium noise-cancelling wireless headphones. Excellent sound quality and battery life. Includes carrying case and charging cable. Lightly used.', 'Like New', DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_ADD(NOW(), INTERVAL 1 DAY), 249.99, 300.00, 'Active'),
    (3007, 2085, 421, 'Premium wireless earbuds with active noise cancellation. Excellent condition, includes charging case and all accessories.', 'Like New', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 3 DAY), 199.99, 280.00, 'Active'),
    (3008, 2101, 511, 'PS5 console with one DualSense controller. Includes all original cables and packaging. Excellent condition, barely used. Comes with 3 games included.', 'Like New', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 2 DAY), 449.99, 550.00, 'Active'),
    (3009, 2119, 524, 'Professional gaming mouse with high DPI sensor. Excellent condition, barely used. Perfect for competitive gaming.', 'Like New', DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_ADD(NOW(), INTERVAL 1 DAY), 79.99, 110.00, 'Active'),
    (3010, 2138, 631, 'Rugged external SSD with excellent performance. Excellent condition, includes USB-C cable. Perfect for backups.', 'Like New', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 3 DAY), 149.99, 220.00, 'Active');

-- Active Auctions - Medium Term (1-2 weeks) - 20 auctions
INSERT IGNORE INTO auctions (id, item_id, category_id, auction_description, auction_condition, start_datetime, end_datetime, starting_price, reserve_price, auction_status) VALUES
    (3011, 2002, 111, 'High-end gaming laptop with Intel i9, 32GB RAM, 2TB SSD, RTX 4080 graphics. 18 inch 165Hz display. Excellent condition, well maintained. Perfect for serious gamers.', 'Like New', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY), 1999.99, 2400.00, 'Active'),
    (3012, 2008, 113, 'Ultrabook with Intel i7, 16GB RAM, 512GB SSD. 13.3 inch OLED display. Lightweight and portable. Good condition, some minor scratches on lid.', 'Used', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 12 DAY), 1299.99, 1600.00, 'Active'),
    (3013, 2010, 121, 'High-end custom gaming PC with Intel i9, 64GB RAM, 2TB SSD, RTX 4090 graphics. Excellent condition, perfect for 4K gaming and streaming.', 'Like New', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 11 DAY), 2499.99, 3000.00, 'Active'),
    (3014, 2018, 131, '4K gaming monitor with 144Hz refresh rate. Excellent color accuracy and response time. Perfect for gaming and design work. Excellent condition, includes stand.', 'Like New', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 13 DAY), 349.99, 480.00, 'Active'),
    (3015, 2027, 211, 'Excellent condition iPhone 15 Pro. Barely used, includes original box and charger. Unlocked for all carriers.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY), 999.99, 1300.00, 'Active'),
    (3016, 2031, 212, 'Premium Android phone with excellent camera. Good condition with minor wear. Includes charger and case.', 'Used', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 12 DAY), 699.99, 900.00, 'Active'),
    (3017, 2037, 221, 'Large iPad Pro with M2 chip. Excellent condition, includes Apple Pencil 2nd generation and Magic Keyboard. Perfect for professionals.', 'Like New', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 11 DAY), 999.99, 1300.00, 'Active'),
    (3018, 2046, 231, 'Latest Apple Watch with fitness tracking. Good condition with some minor wear. Includes charger and band.', 'Used', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 13 DAY), 299.99, 400.00, 'Active'),
    (3019, 2055, 311, 'Professional full-frame DSLR camera body. Excellent condition with low shutter count. Includes battery, charger, and body cap.', 'Like New', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY), 1899.99, 2200.00, 'Active'),
    (3020, 2064, 321, 'Professional full-frame mirrorless camera. Excellent condition, barely used. Perfect for photography and videography.', 'Like New', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 12 DAY), 1999.99, 2400.00, 'Active'),
    (3021, 2078, 411, 'Premium over-ear headphones with active noise cancellation. Excellent condition, includes case and cable.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 11 DAY), 449.99, 600.00, 'Active'),
    (3022, 2086, 421, 'Premium wireless earbuds with noise cancellation. Excellent condition, includes charging case. Great sound quality.', 'Like New', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 13 DAY), 199.99, 280.00, 'Active'),
    (3023, 2093, 431, 'Premium smart speaker with spatial audio. Excellent condition, includes power cable. Perfect for home audio setup.', 'Like New', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY), 399.99, 550.00, 'Active'),
    (3024, 2104, 512, 'Next-gen gaming console with 1TB SSD. Excellent condition, includes controller and all cables. Perfect for gaming enthusiasts.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 12 DAY), 399.99, 500.00, 'Active'),
    (3025, 2110, 521, 'Premium PS5 controller with customizable buttons. Excellent condition, includes case and accessories.', 'Like New', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 11 DAY), 179.99, 250.00, 'Active'),
    (3026, 2116, 523, 'RGB mechanical keyboard with cherry switches. Excellent condition, includes wrist rest. Perfect for gaming and typing.', 'Like New', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 13 DAY), 199.99, 280.00, 'Active'),
    (3027, 2122, 611, 'Premium mechanical keyboard with excellent build quality. Excellent condition, includes keycap puller and cable.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY), 149.99, 220.00, 'Active'),
    (3028, 2130, 621, 'Ultra-lightweight gaming mouse with excellent sensor. Excellent condition, includes accessories. Highly sought after.', 'Like New', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 12 DAY), 99.99, 140.00, 'Active'),
    (3029, 2133, 622, 'Premium productivity mouse with excellent ergonomics. Excellent condition, includes USB receiver.', 'Like New', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 11 DAY), 79.99, 110.00, 'Active'),
    (3030, 2004, 112, 'Business laptop with Intel i7, 16GB RAM, 1TB SSD. 14 inch display. Excellent condition, well maintained. Perfect for professionals.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY), 1299.99, 1600.00, 'Active');

-- Active Auctions - Longer Term (3-4 weeks) - 25 auctions
INSERT IGNORE INTO auctions (id, item_id, category_id, auction_description, auction_condition, start_datetime, end_datetime, starting_price, reserve_price, auction_status) VALUES
    (3031, 2003, 111, 'Premium gaming laptop with Intel i7, 16GB RAM, 1TB SSD, RTX 4060 graphics. 17.3 inch QHD display. Good condition with minor wear.', 'Used', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 21 DAY), 1499.99, 1800.00, 'Active'),
    (3032, 2009, 113, 'Ultra-lightweight laptop with Intel i7, 16GB RAM, 1TB SSD. 17 inch display. Excellent condition, perfect for portability.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 25 DAY), 1199.99, 1500.00, 'Active'),
    (3033, 2013, 122, 'Professional workstation with M2 Ultra chip, 64GB RAM, 1TB SSD. Excellent condition, perfect for video editing and 3D rendering.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 22 DAY), 2999.99, 3600.00, 'Active'),
    (3034, 2019, 131, 'Ultrawide curved gaming monitor with 240Hz refresh rate. Excellent condition, includes stand and all cables. Perfect for immersive gaming.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 24 DAY), 899.99, 1200.00, 'Active'),
    (3035, 2021, 132, 'Professional 5K display with excellent color accuracy. Perfect for creative professionals. Excellent condition, includes stand.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 23 DAY), 1299.99, 1600.00, 'Active'),
    (3036, 2028, 211, 'Good condition iPhone 14 Pro Max with minor wear. Includes charger and case. Unlocked for all carriers.', 'Used', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 21 DAY), 799.99, 1000.00, 'Active'),
    (3037, 2032, 212, 'High-performance smartphone with fast charging. Excellent condition, includes original accessories.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 25 DAY), 599.99, 800.00, 'Active'),
    (3038, 2038, 221, 'iPad Air with M1 chip, 256GB storage. Space Grey color. Includes Apple Pencil 2nd generation and smart folio case. Perfect condition.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 22 DAY), 599.99, 750.00, 'Active'),
    (3039, 2047, 231, 'Premium Apple Watch with titanium case. Excellent condition, includes charger and band. Perfect for outdoor activities.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 24 DAY), 699.99, 900.00, 'Active'),
    (3040, 2049, 232, 'Premium smartwatch with rotating bezel. Excellent condition, includes charger and band.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 23 DAY), 299.99, 400.00, 'Active'),
    (3041, 2058, 312, 'High-resolution full-frame DSLR. Excellent condition, well maintained. Perfect for professional photography.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 21 DAY), 1999.99, 2400.00, 'Active'),
    (3042, 2065, 321, 'High-resolution full-frame mirrorless camera. Excellent condition, perfect for landscape and studio photography.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 25 DAY), 2999.99, 3600.00, 'Active'),
    (3043, 2072, 331, 'Latest action camera with excellent stabilization. Excellent condition, includes mounts and accessories.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 22 DAY), 299.99, 400.00, 'Active'),
    (3044, 2080, 411, 'Premium wireless headphones with excellent sound quality. Excellent condition, includes case and cable.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 24 DAY), 299.99, 400.00, 'Active'),
    (3045, 2087, 421, 'High-quality wireless earbuds with active noise cancellation. Good condition, includes charging case.', 'Used', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 23 DAY), 149.99, 220.00, 'Active'),
    (3046, 2094, 431, 'High-fidelity smart speaker with Alexa. Excellent condition, includes power adapter. Perfect for smart home setup.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 21 DAY), 79.99, 110.00, 'Active'),
    (3047, 2107, 513, 'Nintendo Switch OLED model with red and blue joy-cons. Includes dock, charger, and 5 games. Excellent condition, well cared for.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 25 DAY), 299.99, 380.00, 'Active'),
    (3048, 2113, 522, 'Premium gaming headset with dual battery system. Excellent condition, includes all accessories.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 22 DAY), 249.99, 350.00, 'Active'),
    (3049, 2005, 112, 'Convertible business laptop with Intel i7, 16GB RAM, 512GB SSD. 14 inch touchscreen. Excellent condition, includes stylus.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 24 DAY), 1199.99, 1500.00, 'Active'),
    (3050, 2006, 112, 'Professional business laptop with Intel i7, 16GB RAM, 512GB SSD. 14 inch display. Good condition, well maintained.', 'Used', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 23 DAY), 999.99, 1300.00, 'Active'),
    (3051, 2011, 121, 'Gaming desktop with Intel i9, 32GB RAM, 1TB SSD, RTX 4080 graphics. Excellent condition, includes keyboard and mouse.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 21 DAY), 1999.99, 2400.00, 'Active'),
    (3052, 2012, 121, 'Compact gaming PC with Intel i9, 32GB RAM, 1TB SSD, RTX 4080. Excellent condition, space-saving design.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 25 DAY), 2499.99, 3000.00, 'Active'),
    (3053, 2014, 122, 'Professional workstation with AMD Threadripper, 64GB RAM, 2TB SSD, RTX A5000 graphics. Excellent condition, perfect for CAD and rendering.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 22 DAY), 3499.99, 4200.00, 'Active'),
    (3054, 2016, 123, 'All-in-one desktop with M1 chip. 24 inch 4.5K Retina display. Excellent condition, includes Magic Mouse and Keyboard.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 24 DAY), 999.99, 1300.00, 'Active'),
    (3055, 2020, 131, '4K OLED gaming monitor with 240Hz refresh rate. Excellent picture quality. Perfect for gaming and content creation. Excellent condition.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 23 DAY), 1299.99, 1600.00, 'Active');

-- Active Auctions - Longer Term (continued) - 5 more auctions
INSERT IGNORE INTO auctions (id, item_id, category_id, auction_description, auction_condition, start_datetime, end_datetime, starting_price, reserve_price, auction_status) VALUES
    (3056, 2022, 132, 'Professional 4K monitor with excellent color accuracy. Perfect for design work. Excellent condition, includes stand.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 21 DAY), 599.99, 800.00, 'Active'),
    (3057, 2024, 133, 'Ultrawide curved monitor with excellent color accuracy. Perfect for productivity and gaming. Excellent condition, includes stand.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 25 DAY), 1499.99, 1800.00, 'Active'),
    (3058, 2029, 211, 'Excellent condition iPhone 13 Pro. Well maintained, includes original box and charger. Unlocked.', 'Like New', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 22 DAY), 699.99, 900.00, 'Active'),
    (3059, 2033, 212, 'Flagship smartphone in excellent condition. Includes S Pen, charger, and case. Unlocked.', 'Like New', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 24 DAY), 899.99, 1200.00, 'Active'),
    (3060, 2034, 213, 'Mid-range smartphone with excellent camera. Good condition, includes charger. Great value for money.', 'Used', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 23 DAY), 299.99, 400.00, 'Active');

-- Scheduled Auctions - 3 auctions
INSERT IGNORE INTO auctions (id, item_id, category_id, auction_description, auction_condition, start_datetime, end_datetime, starting_price, reserve_price, auction_status) VALUES
    (3061, 2039, 221, 'Compact iPad Mini in excellent condition. Includes Apple Pencil 2nd generation and case. Perfect for portability.', 'Like New', DATE_ADD(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 8 DAY), 449.99, 600.00, 'Scheduled'),
    (3062, 2040, 222, 'Large tablet with S Pen included. Excellent condition, barely used. Perfect for productivity and creativity.', 'Like New', DATE_ADD(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 9 DAY), 799.99, 1000.00, 'Scheduled'),
    (3063, 2041, 222, 'Premium Android tablet in excellent condition. Includes S Pen and keyboard cover. Perfect for work and entertainment.', 'Like New', DATE_ADD(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY), 599.99, 800.00, 'Scheduled');

-- Finished Auctions - SOLD (with winning bids) - 15 auctions for revenue stats
INSERT IGNORE INTO auctions (id, item_id, category_id, auction_description, auction_condition, start_datetime, end_datetime, starting_price, reserve_price, auction_status) VALUES
    (3064, 2043, 223, '2-in-1 tablet with detachable keyboard. Excellent condition, includes Surface Pen and keyboard cover.', 'Like New', '2024-11-20 10:00:00', '2024-12-05 12:00:00', 999.99, 1300.00, 'Finished'),
    (3067, 2056, 311, 'Canon DSLR camera body. Excellent condition, well maintained. Perfect for photography enthusiasts.', 'Like New', '2024-11-15 09:00:00', '2024-11-30 15:00:00', 899.99, 1100.00, 'Finished'),
    (3068, 2057, 323, 'Canon RF mirrorless camera. Excellent condition, barely used. Perfect for professional photography.', 'Like New', '2024-11-10 11:00:00', '2024-11-25 14:00:00', 1899.99, 2200.00, 'Finished'),
    (3069, 2069, 323, 'Canon EOS R6 Mark II mirrorless. Excellent condition, includes battery and charger.', 'Like New', '2024-11-05 10:00:00', '2024-11-20 16:00:00', 1999.99, 2400.00, 'Finished'),
    (3070, 2073, 331, 'GoPro Hero 11 action camera. Excellent condition, includes mounts and accessories.', 'Like New', '2024-11-12 08:00:00', '2024-11-27 13:00:00', 299.99, 400.00, 'Finished'),
    (3071, 2079, 411, 'Bose QuietComfort headphones. Excellent condition, includes case and cable.', 'Like New', '2024-11-08 14:00:00', '2024-11-23 17:00:00', 249.99, 320.00, 'Finished'),
    (3072, 2081, 412, 'Beats Solo3 wireless headphones. Good condition with minor wear.', 'Used', '2024-11-18 09:00:00', '2024-12-03 12:00:00', 149.99, 200.00, 'Finished'),
    (3073, 2087, 421, 'Samsung Galaxy Buds2 Pro. Excellent condition, includes charging case.', 'Like New', '2024-11-14 11:00:00', '2024-11-29 15:00:00', 149.99, 220.00, 'Finished'),
    (3074, 2097, 432, 'Bose SoundLink portable speaker. Excellent condition, includes charging cable.', 'Like New', '2024-11-16 10:00:00', '2024-12-01 14:00:00', 99.99, 150.00, 'Finished'),
    (3075, 2110, 521, 'Sony DualSense Edge controller. Excellent condition, includes case.', 'Like New', '2024-11-22 08:00:00', '2024-12-07 11:00:00', 179.99, 250.00, 'Finished'),
    (3076, 2113, 522, 'SteelSeries gaming headset. Excellent condition, includes all accessories.', 'Like New', '2024-11-19 13:00:00', '2024-12-04 16:00:00', 249.99, 350.00, 'Finished'),
    (3077, 2116, 523, 'Razer mechanical keyboard. Excellent condition, includes wrist rest.', 'Like New', '2024-11-11 09:00:00', '2024-11-26 12:00:00', 199.99, 280.00, 'Finished'),
    (3078, 2119, 524, 'Logitech gaming mouse. Excellent condition, barely used.', 'Like New', '2024-11-13 10:00:00', '2024-11-28 14:00:00', 79.99, 110.00, 'Finished'),
    (3079, 2122, 611, 'Keychron mechanical keyboard. Excellent condition, includes keycap puller.', 'Like New', '2024-11-17 11:00:00', '2024-12-02 15:00:00', 149.99, 220.00, 'Finished'),
    (3080, 2130, 621, 'Finalmouse gaming mouse. Excellent condition, highly sought after.', 'Like New', '2024-11-21 08:00:00', '2024-12-06 13:00:00', 99.99, 140.00, 'Finished');

-- Finished Auctions - SOLD (with winning bids) - Additional 20 auctions for better stats
INSERT IGNORE INTO auctions (id, item_id, category_id, auction_description, auction_condition, start_datetime, end_datetime, starting_price, reserve_price, auction_status) VALUES
    (3082, 2002, 111, 'Alienware gaming laptop. Excellent condition, well maintained. Perfect for serious gamers.', 'Like New', '2024-10-15 10:00:00', '2024-10-30 15:00:00', 1999.99, 2400.00, 'Finished'),
    (3083, 2007, 113, 'MacBook Pro with M2 chip. Excellent condition, includes original charger and box.', 'Like New', '2024-10-20 09:00:00', '2024-11-04 14:00:00', 1899.99, 2200.00, 'Finished'),
    (3084, 2010, 121, 'Custom gaming PC with RTX 4090. Excellent condition, perfect for 4K gaming.', 'Like New', '2024-10-10 11:00:00', '2024-10-25 16:00:00', 2499.99, 3000.00, 'Finished'),
    (3085, 2011, 121, 'Alienware gaming desktop. Excellent condition, includes keyboard and mouse.', 'Like New', '2024-10-18 08:00:00', '2024-11-02 13:00:00', 1999.99, 2400.00, 'Finished'),
    (3086, 2018, 131, 'LG gaming monitor. Excellent condition, includes stand and all cables.', 'Like New', '2024-10-22 10:00:00', '2024-11-06 15:00:00', 349.99, 480.00, 'Finished'),
    (3087, 2020, 131, 'ASUS gaming monitor. Excellent picture quality. Perfect for gaming.', 'Like New', '2024-10-12 09:00:00', '2024-10-27 14:00:00', 1299.99, 1600.00, 'Finished'),
    (3088, 2026, 211, 'iPhone 15 Pro Max. Brand new, still sealed. Unlocked for all carriers.', 'New', '2024-10-25 11:00:00', '2024-11-09 16:00:00', 1099.99, 1400.00, 'Finished'),
    (3089, 2027, 211, 'iPhone 15 Pro. Excellent condition, includes original box and charger.', 'Like New', '2024-10-28 08:00:00', '2024-11-12 13:00:00', 999.99, 1300.00, 'Finished'),
    (3090, 2030, 212, 'Samsung Galaxy S24 Ultra. Excellent condition, includes S Pen and charger.', 'Like New', '2024-10-14 10:00:00', '2024-10-29 15:00:00', 899.99, 1200.00, 'Finished'),
    (3091, 2031, 212, 'Google Pixel 8 Pro. Excellent condition, includes charger and case.', 'Like New', '2024-10-16 09:00:00', '2024-10-31 14:00:00', 699.99, 900.00, 'Finished'),
    (3092, 2037, 221, 'iPad Pro with M2 chip. Excellent condition, includes Apple Pencil and Magic Keyboard.', 'Like New', '2024-10-19 11:00:00', '2024-11-03 16:00:00', 999.99, 1300.00, 'Finished'),
    (3093, 2046, 231, 'Apple Watch Series 9. Excellent condition, includes charger and band.', 'Like New', '2024-10-21 08:00:00', '2024-11-05 13:00:00', 299.99, 400.00, 'Finished'),
    (3094, 2055, 311, 'Canon EOS 5D Mark IV. Professional DSLR. Excellent condition, well maintained.', 'Like New', '2024-10-11 10:00:00', '2024-10-26 15:00:00', 1899.99, 2200.00, 'Finished'),
    (3095, 2058, 312, 'Nikon D850. High-resolution DSLR. Excellent condition, perfect for professional photography.', 'Like New', '2024-10-13 09:00:00', '2024-10-28 14:00:00', 1999.99, 2400.00, 'Finished'),
    (3096, 2064, 321, 'Sony Alpha A7IV. Professional mirrorless camera. Excellent condition, barely used.', 'Like New', '2024-10-17 11:00:00', '2024-11-01 16:00:00', 1999.99, 2400.00, 'Finished'),
    (3097, 2065, 321, 'Sony Alpha A7R V. High-resolution mirrorless. Excellent condition, perfect for landscape photography.', 'Like New', '2024-10-23 08:00:00', '2024-11-07 13:00:00', 2999.99, 3600.00, 'Finished'),
    (3098, 2072, 331, 'GoPro Hero 12. Latest action camera. Excellent condition, includes mounts and accessories.', 'Like New', '2024-10-24 10:00:00', '2024-11-08 15:00:00', 299.99, 400.00, 'Finished'),
    (3099, 2078, 411, 'Apple AirPods Max. Premium headphones. Excellent condition, includes case and cable.', 'Like New', '2024-10-26 09:00:00', '2024-11-10 14:00:00', 449.99, 600.00, 'Finished'),
    (3100, 2080, 411, 'Sennheiser Momentum 4. Premium wireless headphones. Excellent condition, includes case.', 'Like New', '2024-10-27 11:00:00', '2024-11-11 16:00:00', 299.99, 400.00, 'Finished'),
    (3101, 2101, 511, 'PlayStation 5 console. Excellent condition, includes controller and all cables.', 'Like New', '2024-10-29 08:00:00', '2024-11-13 13:00:00', 449.99, 550.00, 'Finished'),
    (3102, 2104, 512, 'Xbox Series X console. Excellent condition, includes controller and all cables.', 'Like New', '2024-10-30 10:00:00', '2024-11-14 15:00:00', 399.99, 500.00, 'Finished'),
    -- More sold auctions for variety - Gaming Laptops (111) - add 4 more
    (3103, 2140, 111, 'MSI Raider gaming laptop. Excellent condition, perfect for gaming enthusiasts.', 'Like New', '2024-09-20 10:00:00', '2024-10-05 15:00:00', 2499.99, 3000.00, 'Finished'),
    (3104, 2141, 111, 'ASUS Zephyrus gaming laptop. Excellent condition, well maintained.', 'Like New', '2024-09-25 09:00:00', '2024-10-10 14:00:00', 1999.99, 2400.00, 'Finished'),
    (3105, 2142, 111, 'HP Omen gaming laptop. Good condition with minor wear.', 'Used', '2024-10-01 11:00:00', '2024-10-16 16:00:00', 1499.99, 1800.00, 'Finished'),
    (3106, 2143, 111, 'Acer Predator gaming laptop. Excellent condition, barely used.', 'Like New', '2024-10-05 08:00:00', '2024-10-20 13:00:00', 1299.99, 1600.00, 'Finished'),
    -- Gaming PCs (121) - add 2 more
    (3107, 2145, 121, 'MSI Aegis gaming desktop. Excellent condition, includes keyboard and mouse.', 'Like New', '2024-09-28 10:00:00', '2024-10-13 15:00:00', 1799.99, 2200.00, 'Finished'),
    (3108, 2146, 121, 'HP Omen gaming desktop. Excellent condition, well maintained.', 'Like New', '2024-10-03 09:00:00', '2024-10-18 14:00:00', 1899.99, 2300.00, 'Finished'),
    -- iPhone (211) - add 3 more
    (3109, 2147, 211, 'iPhone 14 Pro. Excellent condition, includes original box and charger.', 'Like New', '2024-09-22 11:00:00', '2024-10-07 16:00:00', 899.99, 1100.00, 'Finished'),
    (3110, 2148, 211, 'iPhone 13 Pro Max. Good condition, includes charger and case.', 'Used', '2024-09-30 08:00:00', '2024-10-15 13:00:00', 799.99, 1000.00, 'Finished'),
    (3111, 2029, 211, 'iPhone 13 Pro. Excellent condition, well maintained.', 'Like New', '2024-10-08 10:00:00', '2024-10-23 15:00:00', 699.99, 900.00, 'Finished'),
    -- Android Flagship (212) - add 2 more
    (3112, 2149, 212, 'Samsung Galaxy S23 Ultra. Excellent condition, includes S Pen and charger.', 'Like New', '2024-09-26 09:00:00', '2024-10-11 14:00:00', 899.99, 1200.00, 'Finished'),
    (3113, 2150, 212, 'Google Pixel 7 Pro. Excellent condition, includes charger.', 'Like New', '2024-10-02 11:00:00', '2024-10-17 16:00:00', 649.99, 850.00, 'Finished'),
    -- Canon DSLR (311) - add 2 more
    (3114, 2151, 311, 'Canon EOS R5. Professional mirrorless camera. Excellent condition.', 'Like New', '2024-09-24 10:00:00', '2024-10-09 15:00:00', 3299.99, 4000.00, 'Finished'),
    (3115, 2152, 311, 'Canon EOS R8. Excellent mirrorless camera. Excellent condition.', 'Like New', '2024-10-06 08:00:00', '2024-10-21 13:00:00', 1499.99, 1800.00, 'Finished'),
    (3116, 2153, 311, 'Canon EOS 6D Mark II. Full-frame DSLR. Excellent condition.', 'Like New', '2024-09-27 11:00:00', '2024-10-12 16:00:00', 1299.99, 1600.00, 'Finished'),
    -- Canon Mirrorless (323) - add 1 more
    (3117, 2057, 323, 'Canon EOS R6 Mark II. Professional mirrorless. Excellent condition.', 'Like New', '2024-10-04 09:00:00', '2024-10-19 14:00:00', 1999.99, 2400.00, 'Finished'),
    -- Sony Mirrorless (321) - add 2 more
    (3118, 2154, 321, 'Sony Alpha A7C. Compact full-frame mirrorless. Excellent condition.', 'Like New', '2024-09-29 10:00:00', '2024-10-14 15:00:00', 1499.99, 1800.00, 'Finished'),
    (3119, 2155, 321, 'Sony Alpha A6600. APS-C mirrorless. Excellent condition.', 'Like New', '2024-10-07 08:00:00', '2024-10-22 13:00:00', 999.99, 1300.00, 'Finished'),
    -- Nikon DSLR (312) - add 1 more
    (3120, 2157, 312, 'Nikon Z6 II. Full-frame mirrorless. Excellent condition.', 'Like New', '2024-10-09 11:00:00', '2024-10-24 16:00:00', 1799.99, 2200.00, 'Finished'),
    (3121, 2158, 312, 'Nikon Z7 II. High-resolution mirrorless. Excellent condition.', 'Like New', '2024-09-23 09:00:00', '2024-10-08 14:00:00', 2499.99, 3000.00, 'Finished'),
    -- Fujifilm Mirrorless (322) - add 1 more
    (3122, 2156, 322, 'Fujifilm X-H2. Professional mirrorless. Excellent condition.', 'Like New', '2024-10-10 10:00:00', '2024-10-25 15:00:00', 1699.99, 2100.00, 'Finished');

-- Finished Auctions - NOT SOLD (no winning bid) - 3 auctions
INSERT IGNORE INTO auctions (id, item_id, category_id, auction_description, auction_condition, start_datetime, end_datetime, starting_price, reserve_price, auction_status) VALUES
    (3065, 2048, 231, 'Budget-friendly Apple Watch in excellent condition. Includes charger and band. Perfect for fitness tracking.', 'Like New', '2024-12-02 14:00:00', '2024-12-16 15:00:00', 199.99, 280.00, 'Finished'),
    (3066, 2139, 111, 'High-end gaming laptop that ended with no bids. Excellent condition, barely used. Perfect for gaming enthusiasts.', 'Like New', '2024-11-01 10:00:00', '2024-11-15 12:00:00', 1999.99, 2500.00, 'Finished'),
    (3081, 2059, 312, 'Nikon DSLR camera. Good condition but reserve not met.', 'Used', '2024-11-25 10:00:00', '2024-12-10 12:00:00', 599.99, 800.00, 'Finished');

-- Update items to link to their auctions
UPDATE items SET current_auction_id = 3001 WHERE id = 2001;
UPDATE items SET current_auction_id = 3002 WHERE id = 2007;
UPDATE items SET current_auction_id = 3003 WHERE id = 2026;
UPDATE items SET current_auction_id = 3004 WHERE id = 2030;
UPDATE items SET current_auction_id = 3005 WHERE id = 2063;
UPDATE items SET current_auction_id = 3006 WHERE id = 2077;
UPDATE items SET current_auction_id = 3007 WHERE id = 2085;
UPDATE items SET current_auction_id = 3008 WHERE id = 2101;
UPDATE items SET current_auction_id = 3009 WHERE id = 2119;
UPDATE items SET current_auction_id = 3010 WHERE id = 2138;
UPDATE items SET current_auction_id = 3011 WHERE id = 2002;
UPDATE items SET current_auction_id = 3012 WHERE id = 2008;
UPDATE items SET current_auction_id = 3013 WHERE id = 2010;
UPDATE items SET current_auction_id = 3014 WHERE id = 2018;
UPDATE items SET current_auction_id = 3015 WHERE id = 2027;
UPDATE items SET current_auction_id = 3016 WHERE id = 2031;
UPDATE items SET current_auction_id = 3017 WHERE id = 2037;
UPDATE items SET current_auction_id = 3018 WHERE id = 2046;
UPDATE items SET current_auction_id = 3019 WHERE id = 2055;
UPDATE items SET current_auction_id = 3020 WHERE id = 2064;
UPDATE items SET current_auction_id = 3021 WHERE id = 2078;
UPDATE items SET current_auction_id = 3022 WHERE id = 2086;
UPDATE items SET current_auction_id = 3023 WHERE id = 2093;
UPDATE items SET current_auction_id = 3024 WHERE id = 2104;
UPDATE items SET current_auction_id = 3025 WHERE id = 2110;
UPDATE items SET current_auction_id = 3026 WHERE id = 2116;
UPDATE items SET current_auction_id = 3027 WHERE id = 2122;
UPDATE items SET current_auction_id = 3028 WHERE id = 2130;
UPDATE items SET current_auction_id = 3029 WHERE id = 2133;
UPDATE items SET current_auction_id = 3030 WHERE id = 2004;
UPDATE items SET current_auction_id = 3031 WHERE id = 2003;
UPDATE items SET current_auction_id = 3032 WHERE id = 2009;
UPDATE items SET current_auction_id = 3033 WHERE id = 2013;
UPDATE items SET current_auction_id = 3034 WHERE id = 2019;
UPDATE items SET current_auction_id = 3035 WHERE id = 2021;
UPDATE items SET current_auction_id = 3036 WHERE id = 2028;
UPDATE items SET current_auction_id = 3037 WHERE id = 2032;
UPDATE items SET current_auction_id = 3038 WHERE id = 2038;
UPDATE items SET current_auction_id = 3039 WHERE id = 2047;
UPDATE items SET current_auction_id = 3040 WHERE id = 2049;
UPDATE items SET current_auction_id = 3041 WHERE id = 2058;
UPDATE items SET current_auction_id = 3042 WHERE id = 2065;
UPDATE items SET current_auction_id = 3043 WHERE id = 2072;
UPDATE items SET current_auction_id = 3044 WHERE id = 2080;
UPDATE items SET current_auction_id = 3045 WHERE id = 2087;
UPDATE items SET current_auction_id = 3046 WHERE id = 2094;
UPDATE items SET current_auction_id = 3047 WHERE id = 2107;
UPDATE items SET current_auction_id = 3048 WHERE id = 2113;
UPDATE items SET current_auction_id = 3049 WHERE id = 2005;
UPDATE items SET current_auction_id = 3050 WHERE id = 2006;
UPDATE items SET current_auction_id = 3051 WHERE id = 2011;
UPDATE items SET current_auction_id = 3052 WHERE id = 2012;
UPDATE items SET current_auction_id = 3053 WHERE id = 2014;
UPDATE items SET current_auction_id = 3054 WHERE id = 2016;
UPDATE items SET current_auction_id = 3055 WHERE id = 2020;
UPDATE items SET current_auction_id = 3056 WHERE id = 2022;
UPDATE items SET current_auction_id = 3057 WHERE id = 2024;
UPDATE items SET current_auction_id = 3058 WHERE id = 2029;
UPDATE items SET current_auction_id = 3059 WHERE id = 2033;
UPDATE items SET current_auction_id = 3060 WHERE id = 2034;
UPDATE items SET current_auction_id = 3061 WHERE id = 2039;
UPDATE items SET current_auction_id = 3062 WHERE id = 2040;
UPDATE items SET current_auction_id = 3063 WHERE id = 2041;
UPDATE items SET current_auction_id = 3064 WHERE id = 2043;
UPDATE items SET current_auction_id = 3065 WHERE id = 2048;
UPDATE items SET current_auction_id = 3066 WHERE id = 2139;
    UPDATE items SET current_auction_id = 3067 WHERE id = 2056;
    UPDATE items SET current_auction_id = 3068 WHERE id = 2057;
    UPDATE items SET current_auction_id = 3069 WHERE id = 2069;
    UPDATE items SET current_auction_id = 3070 WHERE id = 2073;
    UPDATE items SET current_auction_id = 3071 WHERE id = 2079;
    UPDATE items SET current_auction_id = 3072 WHERE id = 2081;
    UPDATE items SET current_auction_id = 3073 WHERE id = 2087;
    UPDATE items SET current_auction_id = 3074 WHERE id = 2097;
    UPDATE items SET current_auction_id = 3075 WHERE id = 2110;
    UPDATE items SET current_auction_id = 3076 WHERE id = 2113;
    UPDATE items SET current_auction_id = 3077 WHERE id = 2116;
    UPDATE items SET current_auction_id = 3078 WHERE id = 2119;
    UPDATE items SET current_auction_id = 3079 WHERE id = 2122;
    UPDATE items SET current_auction_id = 3080 WHERE id = 2130;
    UPDATE items SET current_auction_id = 3081 WHERE id = 2059;
    UPDATE items SET current_auction_id = 3082 WHERE id = 2002;
    UPDATE items SET current_auction_id = 3083 WHERE id = 2007;
    UPDATE items SET current_auction_id = 3084 WHERE id = 2010;
    UPDATE items SET current_auction_id = 3085 WHERE id = 2011;
    UPDATE items SET current_auction_id = 3086 WHERE id = 2018;
    UPDATE items SET current_auction_id = 3087 WHERE id = 2020;
    UPDATE items SET current_auction_id = 3088 WHERE id = 2026;
    UPDATE items SET current_auction_id = 3089 WHERE id = 2027;
    UPDATE items SET current_auction_id = 3090 WHERE id = 2030;
    UPDATE items SET current_auction_id = 3091 WHERE id = 2031;
    UPDATE items SET current_auction_id = 3092 WHERE id = 2037;
    UPDATE items SET current_auction_id = 3093 WHERE id = 2046;
    UPDATE items SET current_auction_id = 3094 WHERE id = 2055;
    UPDATE items SET current_auction_id = 3095 WHERE id = 2058;
    UPDATE items SET current_auction_id = 3096 WHERE id = 2064;
    UPDATE items SET current_auction_id = 3097 WHERE id = 2065;
    UPDATE items SET current_auction_id = 3098 WHERE id = 2072;
    UPDATE items SET current_auction_id = 3099 WHERE id = 2078;
    UPDATE items SET current_auction_id = 3100 WHERE id = 2080;
    UPDATE items SET current_auction_id = 3101 WHERE id = 2101;
    UPDATE items SET current_auction_id = 3102 WHERE id = 2104;
    UPDATE items SET current_auction_id = 3103 WHERE id = 2140;
    UPDATE items SET current_auction_id = 3104 WHERE id = 2141;
    UPDATE items SET current_auction_id = 3105 WHERE id = 2142;
    UPDATE items SET current_auction_id = 3106 WHERE id = 2143;
    UPDATE items SET current_auction_id = 3107 WHERE id = 2145;
    UPDATE items SET current_auction_id = 3108 WHERE id = 2146;
    UPDATE items SET current_auction_id = 3109 WHERE id = 2147;
    UPDATE items SET current_auction_id = 3110 WHERE id = 2148;
    UPDATE items SET current_auction_id = 3111 WHERE id = 2029;
    UPDATE items SET current_auction_id = 3112 WHERE id = 2149;
    UPDATE items SET current_auction_id = 3113 WHERE id = 2150;
    UPDATE items SET current_auction_id = 3114 WHERE id = 2151;
    UPDATE items SET current_auction_id = 3115 WHERE id = 2152;
    UPDATE items SET current_auction_id = 3116 WHERE id = 2153;
    UPDATE items SET current_auction_id = 3117 WHERE id = 2057;
    UPDATE items SET current_auction_id = 3118 WHERE id = 2154;
    UPDATE items SET current_auction_id = 3119 WHERE id = 2155;
    UPDATE items SET current_auction_id = 3120 WHERE id = 2157;
    UPDATE items SET current_auction_id = 3121 WHERE id = 2158;
    UPDATE items SET current_auction_id = 3122 WHERE id = 2156;

-- Mark sold items (all finished auctions with winning_bid_id)
UPDATE items SET is_sold = 1 WHERE id IN (2043, 2056, 2057, 2069, 2073, 2079, 2081, 2087, 2097, 2110, 2113, 2116, 2119, 2122, 2130, 2002, 2007, 2010, 2011, 2018, 2020, 2026, 2027, 2030, 2031, 2037, 2046, 2055, 2058, 2064, 2065, 2072, 2078, 2080, 2101, 2104, 2140, 2141, 2142, 2143, 2145, 2146, 2147, 2148, 2149, 2150, 2151, 2152, 2153, 2154, 2155, 2156, 2157, 2158, 2029);

-- BIDS (for testing "My Bids" page and bid history)
-- Varied timing for "time to first bid" stat - some bids immediately, some after hours/days
-- Gaming Laptops (111) - High bid activity for "top category by avg bids"
INSERT IGNORE INTO bids (id, buyer_id, auction_id, bid_amount, bid_datetime) VALUES
    -- Auction 3001 (Gaming Laptop) - First bid 2 hours after start, then more bids
    (4001, 201, 3001, 1350.00, DATE_SUB(NOW(), INTERVAL 5 DAY) + INTERVAL 2 HOUR),
    (4002, 203, 3001, 1420.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4003, 205, 3001, 1480.00, DATE_SUB(NOW(), INTERVAL 12 HOUR)),
    -- Auction 3002 - First bid 1 day after start
    (4004, 202, 3002, 1950.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (4005, 204, 3002, 2050.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    -- Auction 3003 - First bid 6 hours after start
    (4006, 201, 3003, 1150.00, DATE_SUB(NOW(), INTERVAL 6 DAY) + INTERVAL 6 HOUR),
    (4007, 203, 3003, 1200.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (4008, 205, 3003, 1250.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4009, 207, 3003, 1300.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    -- Auction 3004 - First bid 12 hours after start
    (4010, 202, 3004, 950.00, DATE_SUB(NOW(), INTERVAL 5 DAY) + INTERVAL 12 HOUR),
    (4011, 206, 3004, 1000.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    -- Auction 3005 (Canon camera) - First bid 3 hours after start
    (4012, 201, 3005, 1350.00, DATE_SUB(NOW(), INTERVAL 4 DAY) + INTERVAL 3 HOUR),
    (4013, 204, 3005, 1400.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4014, 208, 3005, 1450.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4015, 202, 3006, 270.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4016, 203, 3007, 220.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4017, 205, 3007, 240.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    -- Auction 3008 (PS5) - First bid 4 hours after start, many bids
    (4018, 201, 3008, 480.00, DATE_SUB(NOW(), INTERVAL 6 DAY) + INTERVAL 4 HOUR),
    (4019, 202, 3008, 500.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (4020, 204, 3008, 520.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (4021, 206, 3008, 530.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4022, 208, 3008, 540.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4023, 203, 3009, 90.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4024, 201, 3010, 170.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4025, 205, 3010, 190.00, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- More bids (high interest auctions)
INSERT IGNORE INTO bids (id, buyer_id, auction_id, bid_amount, bid_datetime) VALUES
    (4026, 201, 3011, 2100.00, DATE_SUB(NOW(), INTERVAL 8 DAY)),
    (4027, 202, 3011, 2150.00, DATE_SUB(NOW(), INTERVAL 7 DAY)),
    (4028, 203, 3011, 2200.00, DATE_SUB(NOW(), INTERVAL 6 DAY)),
    (4029, 204, 3011, 2250.00, DATE_SUB(NOW(), INTERVAL 5 DAY)),
    (4030, 205, 3011, 2280.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (4031, 206, 3011, 2300.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (4032, 207, 3011, 2320.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4033, 208, 3011, 2350.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4034, 201, 3012, 1350.00, DATE_SUB(NOW(), INTERVAL 6 DAY)),
    (4035, 203, 3012, 1400.00, DATE_SUB(NOW(), INTERVAL 5 DAY)),
    (4036, 205, 3012, 1450.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (4037, 206, 3012, 1480.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (4038, 207, 3012, 1500.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4039, 208, 3012, 1520.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4040, 201, 3013, 2600.00, DATE_SUB(NOW(), INTERVAL 10 DAY)),
    (4041, 202, 3013, 2650.00, DATE_SUB(NOW(), INTERVAL 9 DAY)),
    (4042, 203, 3013, 2700.00, DATE_SUB(NOW(), INTERVAL 8 DAY)),
    (4043, 204, 3013, 2750.00, DATE_SUB(NOW(), INTERVAL 7 DAY)),
    (4044, 205, 3013, 2800.00, DATE_SUB(NOW(), INTERVAL 6 DAY)),
    (4045, 206, 3013, 2820.00, DATE_SUB(NOW(), INTERVAL 5 DAY)),
    (4046, 207, 3013, 2850.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (4047, 208, 3013, 2880.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (4048, 201, 3013, 2900.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4049, 202, 3013, 2920.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4050, 203, 3014, 400.00, DATE_SUB(NOW(), INTERVAL 5 DAY)),
    (4051, 205, 3014, 420.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (4052, 206, 3014, 440.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (4053, 207, 3014, 450.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4054, 208, 3014, 460.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4055, 201, 3015, 1050.00, DATE_SUB(NOW(), INTERVAL 7 DAY)),
    (4056, 202, 3015, 1100.00, DATE_SUB(NOW(), INTERVAL 6 DAY)),
    (4057, 204, 3015, 1150.00, DATE_SUB(NOW(), INTERVAL 5 DAY)),
    (4058, 205, 3015, 1180.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (4059, 206, 3015, 1200.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (4060, 207, 3015, 1220.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4061, 208, 3015, 1250.00, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- More bids on various auctions
INSERT IGNORE INTO bids (id, buyer_id, auction_id, bid_amount, bid_datetime) VALUES
    (4062, 201, 3016, 750.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (4063, 203, 3016, 780.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (4064, 205, 3016, 820.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4065, 207, 3016, 850.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4066, 202, 3017, 1050.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (4067, 204, 3017, 1100.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4068, 206, 3017, 1150.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4069, 201, 3018, 320.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4070, 203, 3018, 350.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4071, 201, 3019, 2000.00, DATE_SUB(NOW(), INTERVAL 6 DAY)),
    (4072, 202, 3019, 2050.00, DATE_SUB(NOW(), INTERVAL 5 DAY)),
    (4073, 204, 3019, 2100.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (4074, 205, 3019, 2120.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (4075, 206, 3019, 2150.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4076, 208, 3019, 2180.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4077, 201, 3020, 2100.00, DATE_SUB(NOW(), INTERVAL 5 DAY)),
    (4078, 203, 3020, 2150.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (4079, 205, 3020, 2200.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (4080, 207, 3020, 2250.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4081, 208, 3020, 2300.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4082, 202, 3021, 500.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (4083, 204, 3021, 550.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4084, 206, 3021, 580.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4085, 201, 3022, 220.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4086, 203, 3022, 250.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4087, 202, 3023, 420.00, DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (4088, 204, 3023, 450.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (4089, 206, 3023, 480.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4090, 208, 3023, 520.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4091, 201, 3024, 420.00, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (4092, 203, 3024, 450.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4093, 205, 3024, 480.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4094, 202, 3025, 200.00, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4095, 204, 3025, 230.00, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    -- More bids on Gaming Laptops (111) to boost average bids per auction
    -- Auction 3011 (Gaming Laptop) - already has 8 bids, add more
    (4161, 209, 3011, 2360.00, DATE_SUB(NOW(), INTERVAL 6 HOUR)),
    (4162, 210, 3011, 2370.00, DATE_SUB(NOW(), INTERVAL 4 HOUR)),
    (4163, 211, 3011, 2380.00, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
    -- Auction 3031 (Gaming Laptop) - add many bids
    (4164, 201, 3031, 1550.00, DATE_SUB(NOW(), INTERVAL 1 DAY) + INTERVAL 1 HOUR),
    (4165, 202, 3031, 1600.00, DATE_SUB(NOW(), INTERVAL 1 DAY) + INTERVAL 3 HOUR),
    (4166, 203, 3031, 1650.00, DATE_SUB(NOW(), INTERVAL 1 DAY) + INTERVAL 6 HOUR),
    (4167, 204, 3031, 1680.00, DATE_SUB(NOW(), INTERVAL 1 DAY) + INTERVAL 12 HOUR),
    (4168, 205, 3031, 1700.00, DATE_SUB(NOW(), INTERVAL 1 DAY) + INTERVAL 18 HOUR),
    (4169, 206, 3031, 1720.00, DATE_SUB(NOW(), INTERVAL 1 DAY) + INTERVAL 1 DAY),
    (4170, 207, 3031, 1750.00, DATE_SUB(NOW(), INTERVAL 1 DAY) + INTERVAL 1 DAY + INTERVAL 6 HOUR),
    (4171, 208, 3031, 1780.00, DATE_SUB(NOW(), INTERVAL 1 DAY) + INTERVAL 2 DAY),
    (4172, 209, 3031, 1800.00, DATE_SUB(NOW(), INTERVAL 1 DAY) + INTERVAL 2 DAY + INTERVAL 6 HOUR);

-- Bids on finished auctions (SOLD) - with varied timing for "time to first bid" stat
INSERT IGNORE INTO bids (id, buyer_id, auction_id, bid_amount, bid_datetime) VALUES
    -- Auction 3064 - First bid 5 hours after start
    (4096, 201, 3064, 1050.00, '2024-11-20 15:00:00'),
    (4097, 203, 3064, 1100.00, '2024-11-25 14:00:00'),
    (4098, 205, 3064, 1200.00, '2024-12-02 16:00:00'),
    -- Auction 3067 (Canon) - First bid 8 hours after start
    (4101, 201, 3067, 950.00, '2024-11-15 17:00:00'),
    (4102, 202, 3067, 1000.00, '2024-11-18 10:00:00'),
    (4103, 203, 3067, 1050.00, '2024-11-22 14:00:00'),
    (4104, 204, 3067, 1100.00, '2024-11-28 16:00:00'),
    -- Auction 3068 (Canon RF) - First bid 2 hours after start
    (4105, 201, 3068, 1950.00, '2024-11-10 13:00:00'),
    (4106, 202, 3068, 2000.00, '2024-11-12 10:00:00'),
    (4107, 203, 3068, 2050.00, '2024-11-15 15:00:00'),
    (4108, 204, 3068, 2100.00, '2024-11-18 11:00:00'),
    (4109, 205, 3068, 2150.00, '2024-11-22 14:00:00'),
    (4110, 206, 3068, 2200.00, '2024-11-25 16:00:00'),
    -- Auction 3069 (Canon R6) - First bid 1 hour after start
    (4111, 201, 3069, 2050.00, '2024-11-05 11:00:00'),
    (4112, 202, 3069, 2100.00, '2024-11-08 10:00:00'),
    (4113, 203, 3069, 2150.00, '2024-11-10 14:00:00'),
    (4114, 204, 3069, 2200.00, '2024-11-12 15:00:00'),
    (4115, 205, 3069, 2250.00, '2024-11-15 11:00:00'),
    (4116, 206, 3069, 2300.00, '2024-11-18 13:00:00'),
    (4117, 207, 3069, 2350.00, '2024-11-20 16:00:00'),
    -- Auction 3070 - First bid 3 days after start
    (4118, 201, 3070, 320.00, '2024-11-15 08:00:00'),
    (4119, 202, 3070, 350.00, '2024-11-18 10:00:00'),
    (4120, 203, 3070, 380.00, '2024-11-22 14:00:00'),
    (4121, 204, 3070, 400.00, '2024-11-25 15:00:00'),
    -- Auction 3071 - First bid 12 hours after start
    (4122, 201, 3071, 260.00, '2024-11-09 02:00:00'),
    (4123, 202, 3071, 280.00, '2024-11-12 10:00:00'),
    (4124, 203, 3071, 300.00, '2024-11-15 14:00:00'),
    (4125, 204, 3071, 320.00, '2024-11-20 16:00:00'),
    -- Auction 3072 - First bid 6 hours after start
    (4126, 201, 3072, 160.00, '2024-11-18 15:00:00'),
    (4127, 202, 3072, 180.00, '2024-11-20 11:00:00'),
    (4128, 203, 3072, 200.00, '2024-11-25 14:00:00'),
    -- Auction 3073 - First bid 1 day after start
    (4129, 201, 3073, 160.00, '2024-11-15 11:00:00'),
    (4130, 202, 3073, 180.00, '2024-11-18 10:00:00'),
    (4131, 203, 3073, 200.00, '2024-11-22 15:00:00'),
    (4132, 204, 3073, 220.00, '2024-11-27 16:00:00'),
    -- Auction 3074 - First bid 4 hours after start
    (4133, 201, 3074, 110.00, '2024-11-16 14:00:00'),
    (4134, 202, 3074, 130.00, '2024-11-20 10:00:00'),
    (4135, 203, 3074, 150.00, '2024-11-25 14:00:00'),
    -- Auction 3075 - First bid 2 days after start
    (4136, 201, 3075, 190.00, '2024-11-24 08:00:00'),
    (4137, 202, 3075, 210.00, '2024-11-26 10:00:00'),
    (4138, 203, 3075, 230.00, '2024-11-28 14:00:00'),
    (4139, 204, 3075, 250.00, '2024-12-02 15:00:00'),
    -- Auction 3076 - First bid 8 hours after start
    (4140, 201, 3076, 260.00, '2024-11-19 21:00:00'),
    (4141, 202, 3076, 280.00, '2024-11-22 10:00:00'),
    (4142, 203, 3076, 300.00, '2024-11-25 14:00:00'),
    (4143, 204, 3076, 320.00, '2024-11-28 16:00:00'),
    (4144, 205, 3076, 350.00, '2024-12-01 15:00:00'),
    -- Auction 3077 - First bid 1 hour after start
    (4145, 201, 3077, 210.00, '2024-11-11 10:00:00'),
    (4146, 202, 3077, 230.00, '2024-11-13 10:00:00'),
    (4147, 203, 3077, 250.00, '2024-11-15 14:00:00'),
    (4148, 204, 3077, 270.00, '2024-11-18 15:00:00'),
    (4149, 205, 3077, 280.00, '2024-11-22 16:00:00'),
    -- Auction 3078 - First bid 5 hours after start
    (4150, 201, 3078, 90.00, '2024-11-13 15:00:00'),
    (4151, 202, 3078, 100.00, '2024-11-16 10:00:00'),
    (4152, 203, 3078, 110.00, '2024-11-20 14:00:00'),
    -- Auction 3079 - First bid 3 days after start
    (4153, 201, 3079, 160.00, '2024-11-20 11:00:00'),
    (4154, 202, 3079, 180.00, '2024-11-22 10:00:00'),
    (4155, 203, 3079, 200.00, '2024-11-25 15:00:00'),
    (4156, 204, 3079, 220.00, '2024-11-28 16:00:00'),
    -- Auction 3080 - First bid 30 minutes after start
    (4157, 201, 3080, 110.00, '2024-11-21 08:30:00'),
    (4158, 202, 3080, 120.00, '2024-11-23 10:00:00'),
    (4159, 203, 3080, 130.00, '2024-11-25 14:00:00'),
    (4160, 204, 3080, 140.00, '2024-11-28 15:00:00'),
    -- Additional bids for new sold auctions (varied timing for stats)
    -- Auction 3082 (Gaming Laptop) - First bid 4 hours after start
    (4173, 201, 3082, 2100.00, '2024-10-15 14:00:00'),
    (4174, 202, 3082, 2150.00, '2024-10-18 10:00:00'),
    (4175, 203, 3082, 2200.00, '2024-10-22 14:00:00'),
    (4176, 204, 3082, 2250.00, '2024-10-25 15:00:00'),
    (4177, 205, 3082, 2300.00, '2024-10-28 16:00:00'),
    (4178, 206, 3082, 2350.00, '2024-10-30 17:00:00'),
    -- Auction 3083 (MacBook) - First bid 2 hours after start
    (4179, 201, 3083, 1950.00, '2024-10-20 11:00:00'),
    (4180, 202, 3083, 2000.00, '2024-10-23 10:00:00'),
    (4181, 203, 3083, 2050.00, '2024-10-26 14:00:00'),
    (4182, 204, 3083, 2100.00, '2024-10-29 15:00:00'),
    (4183, 205, 3083, 2150.00, '2024-11-01 16:00:00'),
    (4184, 206, 3083, 2200.00, '2024-11-04 17:00:00'),
    -- Auction 3084 (Gaming PC) - First bid 1 day after start
    (4185, 201, 3084, 2600.00, '2024-10-11 11:00:00'),
    (4186, 202, 3084, 2650.00, '2024-10-14 10:00:00'),
    (4187, 203, 3084, 2700.00, '2024-10-17 14:00:00'),
    (4188, 204, 3084, 2750.00, '2024-10-20 15:00:00'),
    (4189, 205, 3084, 2800.00, '2024-10-23 16:00:00'),
    (4190, 206, 3084, 2850.00, '2024-10-25 17:00:00'),
    (4191, 207, 3084, 2900.00, '2024-10-27 18:00:00'),
    (4192, 208, 3084, 2950.00, '2024-10-29 19:00:00'),
    (4193, 209, 3084, 3000.00, '2024-10-31 20:00:00'),
    -- Auction 3085 (Gaming Desktop) - First bid 6 hours after start
    (4194, 201, 3085, 2100.00, '2024-10-18 14:00:00'),
    (4195, 202, 3085, 2150.00, '2024-10-21 10:00:00'),
    (4196, 203, 3085, 2200.00, '2024-10-24 14:00:00'),
    (4197, 204, 3085, 2250.00, '2024-10-27 15:00:00'),
    (4198, 205, 3085, 2300.00, '2024-10-30 16:00:00'),
    (4199, 206, 3085, 2350.00, '2024-11-01 17:00:00'),
    (4200, 207, 3085, 2400.00, '2024-11-02 18:00:00'),
    -- Auction 3086 (Monitor) - First bid 3 hours after start
    (4201, 201, 3086, 380.00, '2024-10-22 13:00:00'),
    (4202, 202, 3086, 400.00, '2024-10-25 10:00:00'),
    (4203, 203, 3086, 420.00, '2024-10-28 14:00:00'),
    (4204, 204, 3086, 440.00, '2024-10-31 15:00:00'),
    (4205, 205, 3086, 460.00, '2024-11-03 16:00:00'),
    (4206, 206, 3086, 480.00, '2024-11-06 17:00:00'),
    -- Auction 3087 (Gaming Monitor) - First bid 8 hours after start
    (4207, 201, 3087, 1350.00, '2024-10-12 17:00:00'),
    (4208, 202, 3087, 1400.00, '2024-10-15 10:00:00'),
    (4209, 203, 3087, 1450.00, '2024-10-18 14:00:00'),
    (4210, 204, 3087, 1500.00, '2024-10-21 15:00:00'),
    (4211, 205, 3087, 1550.00, '2024-10-24 16:00:00'),
    (4212, 206, 3087, 1600.00, '2024-10-27 17:00:00'),
    -- Auction 3088 (iPhone) - First bid 1 hour after start
    (4213, 201, 3088, 1150.00, '2024-10-25 12:00:00'),
    (4214, 202, 3088, 1200.00, '2024-10-28 10:00:00'),
    (4215, 203, 3088, 1250.00, '2024-10-31 14:00:00'),
    (4216, 204, 3088, 1300.00, '2024-11-03 15:00:00'),
    (4217, 205, 3088, 1350.00, '2024-11-06 16:00:00'),
    (4218, 206, 3088, 1400.00, '2024-11-09 17:00:00'),
    -- Auction 3089 (iPhone) - First bid 5 hours after start
    (4219, 201, 3089, 1050.00, '2024-10-28 13:00:00'),
    (4220, 202, 3089, 1100.00, '2024-10-31 10:00:00'),
    (4221, 203, 3089, 1150.00, '2024-11-03 14:00:00'),
    (4222, 204, 3089, 1200.00, '2024-11-06 15:00:00'),
    (4223, 205, 3089, 1250.00, '2024-11-09 16:00:00'),
    (4224, 206, 3089, 1300.00, '2024-11-12 17:00:00'),
    -- Auction 3090 (Samsung) - First bid 12 hours after start
    (4225, 201, 3090, 950.00, '2024-10-14 22:00:00'),
    (4226, 202, 3090, 1000.00, '2024-10-17 10:00:00'),
    (4227, 203, 3090, 1050.00, '2024-10-20 14:00:00'),
    (4228, 204, 3090, 1100.00, '2024-10-23 15:00:00'),
    (4229, 205, 3090, 1150.00, '2024-10-26 16:00:00'),
    (4230, 206, 3090, 1200.00, '2024-10-29 17:00:00'),
    -- Auction 3091 (Pixel) - First bid 2 days after start
    (4231, 201, 3091, 750.00, '2024-10-18 09:00:00'),
    (4232, 202, 3091, 800.00, '2024-10-21 10:00:00'),
    (4233, 203, 3091, 850.00, '2024-10-24 14:00:00'),
    (4234, 204, 3091, 900.00, '2024-10-27 15:00:00'),
    -- Auction 3092 (iPad) - First bid 3 hours after start
    (4235, 201, 3092, 1050.00, '2024-10-19 14:00:00'),
    (4236, 202, 3092, 1100.00, '2024-10-22 10:00:00'),
    (4237, 203, 3092, 1150.00, '2024-10-25 14:00:00'),
    (4238, 204, 3092, 1200.00, '2024-10-28 15:00:00'),
    (4239, 205, 3092, 1250.00, '2024-10-31 16:00:00'),
    (4240, 206, 3092, 1300.00, '2024-11-03 17:00:00'),
    -- Auction 3093 (Apple Watch) - First bid 1 day after start
    (4241, 201, 3093, 320.00, '2024-10-22 08:00:00'),
    (4242, 202, 3093, 350.00, '2024-10-25 10:00:00'),
    (4243, 203, 3093, 380.00, '2024-10-28 14:00:00'),
    (4244, 204, 3093, 400.00, '2024-10-31 15:00:00'),
    -- Auction 3094 (Canon DSLR) - First bid 4 hours after start
    (4245, 201, 3094, 1950.00, '2024-10-11 14:00:00'),
    (4246, 202, 3094, 2000.00, '2024-10-14 10:00:00'),
    (4247, 203, 3094, 2050.00, '2024-10-17 14:00:00'),
    (4248, 204, 3094, 2100.00, '2024-10-20 15:00:00'),
    (4249, 205, 3094, 2150.00, '2024-10-23 16:00:00'),
    (4250, 206, 3094, 2200.00, '2024-10-26 17:00:00'),
    -- Auction 3095 (Nikon DSLR) - First bid 6 hours after start
    (4251, 201, 3095, 2050.00, '2024-10-13 15:00:00'),
    (4252, 202, 3095, 2100.00, '2024-10-16 10:00:00'),
    (4253, 203, 3095, 2150.00, '2024-10-19 14:00:00'),
    (4254, 204, 3095, 2200.00, '2024-10-22 15:00:00'),
    (4255, 205, 3095, 2250.00, '2024-10-25 16:00:00'),
    (4256, 206, 3095, 2300.00, '2024-10-28 17:00:00'),
    (4257, 207, 3095, 2350.00, '2024-10-30 18:00:00'),
    (4258, 208, 3095, 2400.00, '2024-11-01 19:00:00'),
    -- Auction 3096 (Sony Mirrorless) - First bid 2 hours after start
    (4259, 201, 3096, 2050.00, '2024-10-17 13:00:00'),
    (4260, 202, 3096, 2100.00, '2024-10-20 10:00:00'),
    (4261, 203, 3096, 2150.00, '2024-10-23 14:00:00'),
    (4262, 204, 3096, 2200.00, '2024-10-26 15:00:00'),
    (4263, 205, 3096, 2250.00, '2024-10-29 16:00:00'),
    (4264, 206, 3096, 2300.00, '2024-11-01 17:00:00'),
    -- Auction 3097 (Sony A7R V) - First bid 1 hour after start
    (4265, 201, 3097, 3050.00, '2024-10-23 09:00:00'),
    (4266, 202, 3097, 3100.00, '2024-10-26 10:00:00'),
    (4267, 203, 3097, 3150.00, '2024-10-29 14:00:00'),
    (4268, 204, 3097, 3200.00, '2024-11-01 15:00:00'),
    (4269, 205, 3097, 3250.00, '2024-11-04 16:00:00'),
    (4270, 206, 3097, 3300.00, '2024-11-06 17:00:00'),
    (4271, 207, 3097, 3400.00, '2024-11-07 18:00:00'),
    (4272, 208, 3097, 3500.00, '2024-11-08 19:00:00'),
    (4273, 209, 3097, 3600.00, '2024-11-09 20:00:00'),
    -- Auction 3098 (GoPro) - First bid 5 hours after start
    (4274, 201, 3098, 320.00, '2024-10-24 13:00:00'),
    (4275, 202, 3098, 350.00, '2024-10-27 10:00:00'),
    (4276, 203, 3098, 380.00, '2024-10-30 14:00:00'),
    (4277, 204, 3098, 400.00, '2024-11-02 15:00:00'),
    -- Auction 3099 (AirPods Max) - First bid 3 hours after start
    (4278, 201, 3099, 480.00, '2024-10-26 12:00:00'),
    (4279, 202, 3099, 520.00, '2024-10-29 10:00:00'),
    (4280, 203, 3099, 550.00, '2024-11-01 14:00:00'),
    (4281, 204, 3099, 580.00, '2024-11-04 15:00:00'),
    (4282, 205, 3099, 600.00, '2024-11-07 16:00:00'),
    -- Auction 3100 (Sennheiser) - First bid 8 hours after start
    (4283, 201, 3100, 320.00, '2024-10-27 19:00:00'),
    (4284, 202, 3100, 350.00, '2024-10-30 10:00:00'),
    (4285, 203, 3100, 380.00, '2024-11-02 14:00:00'),
    (4286, 204, 3100, 400.00, '2024-11-05 15:00:00'),
    -- Auction 3101 (PS5) - First bid 2 hours after start
    (4287, 201, 3101, 480.00, '2024-10-29 10:00:00'),
    (4288, 202, 3101, 500.00, '2024-11-01 10:00:00'),
    (4289, 203, 3101, 520.00, '2024-11-04 14:00:00'),
    (4290, 204, 3101, 540.00, '2024-11-07 15:00:00'),
    (4291, 205, 3101, 550.00, '2024-11-10 16:00:00'),
    -- Auction 3102 (Xbox) - First bid 4 hours after start
    (4292, 201, 3102, 420.00, '2024-10-30 14:00:00'),
    (4293, 202, 3102, 450.00, '2024-11-02 10:00:00'),
    (4294, 203, 3102, 480.00, '2024-11-05 14:00:00'),
    (4295, 204, 3102, 500.00, '2024-11-08 15:00:00'),
    -- Bids for additional sold auctions - Gaming Laptops (111)
    -- Auction 3103 - First bid 3 hours after start
    (4296, 201, 3103, 2600.00, '2024-09-20 13:00:00'),
    (4297, 202, 3103, 2700.00, '2024-09-23 10:00:00'),
    (4298, 203, 3103, 2800.00, '2024-09-26 14:00:00'),
    (4299, 204, 3103, 2900.00, '2024-09-29 15:00:00'),
    (4300, 205, 3103, 3000.00, '2024-10-02 16:00:00'),
    -- Auction 3104 - First bid 5 hours after start
    (4301, 201, 3104, 2100.00, '2024-09-25 14:00:00'),
    (4302, 202, 3104, 2200.00, '2024-09-28 10:00:00'),
    (4303, 203, 3104, 2300.00, '2024-10-01 14:00:00'),
    (4304, 204, 3104, 2400.00, '2024-10-04 15:00:00'),
    (4305, 205, 3104, 2450.00, '2024-10-07 16:00:00'),
    (4306, 206, 3104, 2500.00, '2024-10-10 17:00:00'),
    -- Auction 3105 - First bid 1 day after start
    (4307, 201, 3105, 1600.00, '2024-10-02 11:00:00'),
    (4308, 202, 3105, 1700.00, '2024-10-05 10:00:00'),
    (4309, 203, 3105, 1800.00, '2024-10-08 14:00:00'),
    (4310, 204, 3105, 1850.00, '2024-10-11 15:00:00'),
    (4311, 205, 3105, 1900.00, '2024-10-14 16:00:00'),
    -- Auction 3106 - First bid 2 hours after start
    (4312, 201, 3106, 1350.00, '2024-10-05 10:00:00'),
    (4313, 202, 3106, 1450.00, '2024-10-08 10:00:00'),
    (4314, 203, 3106, 1550.00, '2024-10-11 14:00:00'),
    (4315, 204, 3106, 1600.00, '2024-10-14 15:00:00'),
    (4316, 205, 3106, 1650.00, '2024-10-17 16:00:00'),
    (4317, 206, 3106, 1700.00, '2024-10-20 17:00:00'),
    -- Gaming PCs (121)
    -- Auction 3107 - First bid 6 hours after start
    (4318, 201, 3107, 1900.00, '2024-09-28 16:00:00'),
    (4319, 202, 3107, 2000.00, '2024-10-01 10:00:00'),
    (4320, 203, 3107, 2100.00, '2024-10-04 14:00:00'),
    (4321, 204, 3107, 2200.00, '2024-10-07 15:00:00'),
    (4322, 205, 3107, 2250.00, '2024-10-10 16:00:00'),
    (4323, 206, 3107, 2300.00, '2024-10-13 17:00:00'),
    -- Auction 3108 - First bid 4 hours after start
    (4324, 201, 3108, 2000.00, '2024-10-03 13:00:00'),
    (4325, 202, 3108, 2100.00, '2024-10-06 10:00:00'),
    (4326, 203, 3108, 2200.00, '2024-10-09 14:00:00'),
    (4327, 204, 3108, 2300.00, '2024-10-12 15:00:00'),
    (4328, 205, 3108, 2350.00, '2024-10-15 16:00:00'),
    (4329, 206, 3108, 2400.00, '2024-10-18 17:00:00'),
    -- iPhone (211)
    -- Auction 3109 - First bid 1 hour after start
    (4330, 201, 3109, 950.00, '2024-09-22 12:00:00'),
    (4331, 202, 3109, 1000.00, '2024-09-25 10:00:00'),
    (4332, 203, 3109, 1050.00, '2024-09-28 14:00:00'),
    (4333, 204, 3109, 1100.00, '2024-10-01 15:00:00'),
    (4334, 205, 3109, 1120.00, '2024-10-04 16:00:00'),
    (4335, 206, 3109, 1150.00, '2024-10-07 17:00:00'),
    -- Auction 3110 - First bid 8 hours after start
    (4336, 201, 3110, 850.00, '2024-09-30 16:00:00'),
    (4337, 202, 3110, 900.00, '2024-10-03 10:00:00'),
    (4338, 203, 3110, 950.00, '2024-10-06 14:00:00'),
    (4339, 204, 3110, 1000.00, '2024-10-09 15:00:00'),
    (4340, 205, 3110, 1020.00, '2024-10-12 16:00:00'),
    -- Auction 3111 - First bid 2 hours after start
    (4341, 201, 3111, 750.00, '2024-10-08 12:00:00'),
    (4342, 202, 3111, 800.00, '2024-10-11 10:00:00'),
    (4343, 203, 3111, 850.00, '2024-10-14 14:00:00'),
    (4344, 204, 3111, 900.00, '2024-10-17 15:00:00'),
    (4345, 205, 3111, 920.00, '2024-10-20 16:00:00'),
    (4346, 206, 3111, 950.00, '2024-10-23 17:00:00'),
    -- Android Flagship (212)
    -- Auction 3112 - First bid 3 hours after start
    (4347, 201, 3112, 950.00, '2024-09-26 12:00:00'),
    (4348, 202, 3112, 1000.00, '2024-09-29 10:00:00'),
    (4349, 203, 3112, 1100.00, '2024-10-02 14:00:00'),
    (4350, 204, 3112, 1150.00, '2024-10-05 15:00:00'),
    (4351, 205, 3112, 1200.00, '2024-10-08 16:00:00'),
    (4352, 206, 3112, 1250.00, '2024-10-11 17:00:00'),
    -- Auction 3113 - First bid 12 hours after start
    (4353, 201, 3113, 700.00, '2024-10-02 23:00:00'),
    (4354, 202, 3113, 750.00, '2024-10-05 10:00:00'),
    (4355, 203, 3113, 800.00, '2024-10-08 14:00:00'),
    (4356, 204, 3113, 850.00, '2024-10-11 15:00:00'),
    (4357, 205, 3113, 880.00, '2024-10-14 16:00:00'),
    -- Canon DSLR (311)
    -- Auction 3114 - First bid 2 hours after start
    (4358, 201, 3114, 3400.00, '2024-09-24 12:00:00'),
    (4359, 202, 3114, 3500.00, '2024-09-27 10:00:00'),
    (4360, 203, 3114, 3600.00, '2024-09-30 14:00:00'),
    (4361, 204, 3114, 3700.00, '2024-10-03 15:00:00'),
    (4362, 205, 3114, 3800.00, '2024-10-06 16:00:00'),
    (4363, 206, 3114, 3900.00, '2024-10-09 17:00:00'),
    (4364, 207, 3114, 4000.00, '2024-10-12 18:00:00'),
    -- Auction 3115 - First bid 5 hours after start
    (4365, 201, 3115, 1600.00, '2024-10-06 13:00:00'),
    (4366, 202, 3115, 1700.00, '2024-10-09 10:00:00'),
    (4367, 203, 3115, 1800.00, '2024-10-12 14:00:00'),
    (4368, 204, 3115, 1850.00, '2024-10-15 15:00:00'),
    (4369, 205, 3115, 1900.00, '2024-10-18 16:00:00'),
    -- Auction 3116 - First bid 4 hours after start
    (4370, 201, 3116, 1350.00, '2024-09-27 15:00:00'),
    (4371, 202, 3116, 1450.00, '2024-09-30 10:00:00'),
    (4372, 203, 3116, 1550.00, '2024-10-03 14:00:00'),
    (4373, 204, 3116, 1600.00, '2024-10-06 15:00:00'),
    (4374, 205, 3116, 1650.00, '2024-10-09 16:00:00'),
    (4375, 206, 3116, 1700.00, '2024-10-12 17:00:00'),
    -- Canon Mirrorless (323)
    -- Auction 3117 - First bid 1 hour after start
    (4376, 201, 3117, 2050.00, '2024-10-04 10:00:00'),
    (4377, 202, 3117, 2150.00, '2024-10-07 10:00:00'),
    (4378, 203, 3117, 2250.00, '2024-10-10 14:00:00'),
    (4379, 204, 3117, 2350.00, '2024-10-13 15:00:00'),
    (4380, 205, 3117, 2400.00, '2024-10-16 16:00:00'),
    (4381, 206, 3117, 2450.00, '2024-10-19 17:00:00'),
    -- Sony Mirrorless (321)
    -- Auction 3118 - First bid 3 hours after start
    (4382, 201, 3118, 1550.00, '2024-09-29 13:00:00'),
    (4383, 202, 3118, 1650.00, '2024-10-02 10:00:00'),
    (4384, 203, 3118, 1750.00, '2024-10-05 14:00:00'),
    (4385, 204, 3118, 1800.00, '2024-10-08 15:00:00'),
    (4386, 205, 3118, 1850.00, '2024-10-11 16:00:00'),
    (4387, 206, 3118, 1900.00, '2024-10-14 17:00:00'),
    -- Auction 3119 - First bid 6 hours after start
    (4388, 201, 3119, 1050.00, '2024-10-07 14:00:00'),
    (4389, 202, 3119, 1150.00, '2024-10-10 10:00:00'),
    (4390, 203, 3119, 1250.00, '2024-10-13 14:00:00'),
    (4391, 204, 3119, 1300.00, '2024-10-16 15:00:00'),
    (4392, 205, 3119, 1350.00, '2024-10-19 16:00:00'),
    -- Nikon DSLR (312)
    -- Auction 3120 - First bid 2 hours after start
    (4393, 201, 3120, 1850.00, '2024-10-09 13:00:00'),
    (4394, 202, 3120, 1950.00, '2024-10-12 10:00:00'),
    (4395, 203, 3120, 2050.00, '2024-10-15 14:00:00'),
    (4396, 204, 3120, 2150.00, '2024-10-18 15:00:00'),
    (4397, 205, 3120, 2200.00, '2024-10-21 16:00:00'),
    (4398, 206, 3120, 2250.00, '2024-10-24 17:00:00'),
    -- Auction 3121 - First bid 1 hour after start
    (4399, 201, 3121, 2550.00, '2024-09-23 10:00:00'),
    (4400, 202, 3121, 2650.00, '2024-09-26 10:00:00'),
    (4401, 203, 3121, 2750.00, '2024-09-29 14:00:00'),
    (4402, 204, 3121, 2850.00, '2024-10-02 15:00:00'),
    (4403, 205, 3121, 2950.00, '2024-10-05 16:00:00'),
    (4404, 206, 3121, 3000.00, '2024-10-08 17:00:00'),
    -- Fujifilm Mirrorless (322)
    -- Auction 3122 - First bid 4 hours after start
    (4405, 201, 3122, 1750.00, '2024-10-10 14:00:00'),
    (4406, 202, 3122, 1850.00, '2024-10-13 10:00:00'),
    (4407, 203, 3122, 1950.00, '2024-10-16 14:00:00'),
    (4408, 204, 3122, 2050.00, '2024-10-19 15:00:00'),
    (4409, 205, 3122, 2100.00, '2024-10-22 16:00:00'),
    (4410, 206, 3122, 2150.00, '2024-10-25 17:00:00'),
    -- Auction 3065 (NOT SOLD) - bids below reserve
    (4099, 202, 3065, 220.00, '2024-12-11 11:00:00'),
    (4100, 204, 3065, 250.00, '2024-12-13 15:00:00');

-- Update winning_bid_id for finished auctions (SOLD)
UPDATE auctions SET winning_bid_id = 4098 WHERE id = 3064;
UPDATE auctions SET winning_bid_id = 4104 WHERE id = 3067;
UPDATE auctions SET winning_bid_id = 4110 WHERE id = 3068;
UPDATE auctions SET winning_bid_id = 4117 WHERE id = 3069;
UPDATE auctions SET winning_bid_id = 4121 WHERE id = 3070;
UPDATE auctions SET winning_bid_id = 4125 WHERE id = 3071;
UPDATE auctions SET winning_bid_id = 4128 WHERE id = 3072;
UPDATE auctions SET winning_bid_id = 4132 WHERE id = 3073;
UPDATE auctions SET winning_bid_id = 4135 WHERE id = 3074;
UPDATE auctions SET winning_bid_id = 4139 WHERE id = 3075;
UPDATE auctions SET winning_bid_id = 4144 WHERE id = 3076;
UPDATE auctions SET winning_bid_id = 4149 WHERE id = 3077;
UPDATE auctions SET winning_bid_id = 4152 WHERE id = 3078;
UPDATE auctions SET winning_bid_id = 4156 WHERE id = 3079;
UPDATE auctions SET winning_bid_id = 4160 WHERE id = 3080;
UPDATE auctions SET winning_bid_id = 4178 WHERE id = 3082;
UPDATE auctions SET winning_bid_id = 4184 WHERE id = 3083;
UPDATE auctions SET winning_bid_id = 4193 WHERE id = 3084;
UPDATE auctions SET winning_bid_id = 4200 WHERE id = 3085;
UPDATE auctions SET winning_bid_id = 4206 WHERE id = 3086;
UPDATE auctions SET winning_bid_id = 4212 WHERE id = 3087;
UPDATE auctions SET winning_bid_id = 4218 WHERE id = 3088;
UPDATE auctions SET winning_bid_id = 4224 WHERE id = 3089;
UPDATE auctions SET winning_bid_id = 4230 WHERE id = 3090;
UPDATE auctions SET winning_bid_id = 4234 WHERE id = 3091;
UPDATE auctions SET winning_bid_id = 4240 WHERE id = 3092;
UPDATE auctions SET winning_bid_id = 4244 WHERE id = 3093;
UPDATE auctions SET winning_bid_id = 4250 WHERE id = 3094;
UPDATE auctions SET winning_bid_id = 4258 WHERE id = 3095;
UPDATE auctions SET winning_bid_id = 4264 WHERE id = 3096;
UPDATE auctions SET winning_bid_id = 4273 WHERE id = 3097;
UPDATE auctions SET winning_bid_id = 4277 WHERE id = 3098;
UPDATE auctions SET winning_bid_id = 4282 WHERE id = 3099;
UPDATE auctions SET winning_bid_id = 4286 WHERE id = 3100;
UPDATE auctions SET winning_bid_id = 4291 WHERE id = 3101;
UPDATE auctions SET winning_bid_id = 4295 WHERE id = 3102;
UPDATE auctions SET winning_bid_id = 4300 WHERE id = 3103;
UPDATE auctions SET winning_bid_id = 4306 WHERE id = 3104;
UPDATE auctions SET winning_bid_id = 4311 WHERE id = 3105;
UPDATE auctions SET winning_bid_id = 4317 WHERE id = 3106;
UPDATE auctions SET winning_bid_id = 4323 WHERE id = 3107;
UPDATE auctions SET winning_bid_id = 4329 WHERE id = 3108;
UPDATE auctions SET winning_bid_id = 4335 WHERE id = 3109;
UPDATE auctions SET winning_bid_id = 4340 WHERE id = 3110;
UPDATE auctions SET winning_bid_id = 4346 WHERE id = 3111;
UPDATE auctions SET winning_bid_id = 4352 WHERE id = 3112;
UPDATE auctions SET winning_bid_id = 4357 WHERE id = 3113;
UPDATE auctions SET winning_bid_id = 4364 WHERE id = 3114;
UPDATE auctions SET winning_bid_id = 4369 WHERE id = 3115;
UPDATE auctions SET winning_bid_id = 4375 WHERE id = 3116;
UPDATE auctions SET winning_bid_id = 4381 WHERE id = 3117;
UPDATE auctions SET winning_bid_id = 4387 WHERE id = 3118;
UPDATE auctions SET winning_bid_id = 4392 WHERE id = 3119;
UPDATE auctions SET winning_bid_id = 4398 WHERE id = 3120;
UPDATE auctions SET winning_bid_id = 4404 WHERE id = 3121;
UPDATE auctions SET winning_bid_id = 4410 WHERE id = 3122;
-- Auctions 3065, 3066, 3081 left with NULL winning_bid_id (not sold)

-- WATCHLISTS (for testing "My Watchlist" page)
-- Concentrate watches in Gaming Laptops (111) and Canon cameras (311, 323) for "Most Watched Category" stat
-- User 101 (buyer only) - watching several auctions, mostly Gaming Laptops
INSERT IGNORE INTO watchlists (user_id, auction_id, watched_datetime) VALUES
    (101, 3001, DATE_SUB(NOW(), INTERVAL 3 DAY)),  -- Gaming Laptop
    (101, 3011, DATE_SUB(NOW(), INTERVAL 4 DAY)),  -- Gaming Laptop
    (101, 3031, DATE_SUB(NOW(), INTERVAL 2 DAY)),  -- Gaming Laptop
    (101, 3003, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (101, 3008, DATE_SUB(NOW(), INTERVAL 5 DAY)),
    (101, 3015, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (101, 3020, DATE_SUB(NOW(), INTERVAL 6 DAY));

-- User 201 (buyer + seller) - watching Gaming Laptops and Canon cameras
INSERT IGNORE INTO watchlists (user_id, auction_id, watched_datetime) VALUES
    (201, 3001, DATE_SUB(NOW(), INTERVAL 2 DAY)),  -- Gaming Laptop
    (201, 3011, DATE_SUB(NOW(), INTERVAL 3 DAY)),  -- Gaming Laptop
    (201, 3005, DATE_SUB(NOW(), INTERVAL 1 DAY)),  -- Canon camera
    (201, 3002, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (201, 3004, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (201, 3006, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (201, 3012, DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (201, 3016, DATE_SUB(NOW(), INTERVAL 5 DAY)),
    (201, 3021, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (201, 3023, DATE_SUB(NOW(), INTERVAL 3 DAY));

-- User 202 (buyer + seller) - watching Gaming Laptops heavily
INSERT IGNORE INTO watchlists (user_id, auction_id, watched_datetime) VALUES
    (202, 3001, DATE_SUB(NOW(), INTERVAL 4 DAY)),  -- Gaming Laptop
    (202, 3011, DATE_SUB(NOW(), INTERVAL 3 DAY)),  -- Gaming Laptop
    (202, 3031, DATE_SUB(NOW(), INTERVAL 2 DAY)),  -- Gaming Laptop
    (202, 3005, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (202, 3007, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (202, 3013, DATE_SUB(NOW(), INTERVAL 5 DAY)),
    (202, 3017, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (202, 3019, DATE_SUB(NOW(), INTERVAL 6 DAY));

-- User 203 (buyer + seller) - watching Canon cameras
INSERT IGNORE INTO watchlists (user_id, auction_id, watched_datetime) VALUES
    (203, 3005, DATE_SUB(NOW(), INTERVAL 3 DAY)),  -- Canon camera
    (203, 3019, DATE_SUB(NOW(), INTERVAL 2 DAY)),  -- Canon camera
    (203, 3002, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (203, 3010, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (203, 3014, DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (203, 3020, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (203, 3024, DATE_SUB(NOW(), INTERVAL 5 DAY));

-- User 204 (buyer + seller) - watching Gaming Laptops
INSERT IGNORE INTO watchlists (user_id, auction_id, watched_datetime) VALUES
    (204, 3001, DATE_SUB(NOW(), INTERVAL 2 DAY)),  -- Gaming Laptop
    (204, 3011, DATE_SUB(NOW(), INTERVAL 1 DAY)),  -- Gaming Laptop
    (204, 3003, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (204, 3009, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (204, 3015, DATE_SUB(NOW(), INTERVAL 4 DAY));

-- User 205 (buyer + seller) - watching Gaming Laptops heavily
INSERT IGNORE INTO watchlists (user_id, auction_id, watched_datetime) VALUES
    (205, 3001, DATE_SUB(NOW(), INTERVAL 3 DAY)),  -- Gaming Laptop
    (205, 3011, DATE_SUB(NOW(), INTERVAL 2 DAY)),  -- Gaming Laptop
    (205, 3031, DATE_SUB(NOW(), INTERVAL 1 DAY)),  -- Gaming Laptop
    (205, 3008, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (205, 3024, DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (205, 3025, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- User 206 (buyer + seller) - watching Canon cameras
INSERT IGNORE INTO watchlists (user_id, auction_id, watched_datetime) VALUES
    (206, 3005, DATE_SUB(NOW(), INTERVAL 2 DAY)),  -- Canon camera
    (206, 3019, DATE_SUB(NOW(), INTERVAL 3 DAY)),  -- Canon camera
    (206, 3020, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (206, 3021, DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (206, 3022, DATE_SUB(NOW(), INTERVAL 5 DAY));

-- User 207 (buyer + seller) - watching Gaming Laptops
INSERT IGNORE INTO watchlists (user_id, auction_id, watched_datetime) VALUES
    (207, 3001, DATE_SUB(NOW(), INTERVAL 3 DAY)),  -- Gaming Laptop
    (207, 3011, DATE_SUB(NOW(), INTERVAL 2 DAY)),  -- Gaming Laptop
    (207, 3004, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (207, 3006, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (207, 3012, DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (207, 3018, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (207, 3023, DATE_SUB(NOW(), INTERVAL 5 DAY));

-- User 208 (buyer + seller) - watching Gaming Laptops and Canon cameras
INSERT IGNORE INTO watchlists (user_id, auction_id, watched_datetime) VALUES
    (208, 3001, DATE_SUB(NOW(), INTERVAL 4 DAY)),  -- Gaming Laptop
    (208, 3011, DATE_SUB(NOW(), INTERVAL 3 DAY)),  -- Gaming Laptop
    (208, 3005, DATE_SUB(NOW(), INTERVAL 2 DAY)),  -- Canon camera
    (208, 3019, DATE_SUB(NOW(), INTERVAL 1 DAY)),  -- Canon camera
    (208, 3002, DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (208, 3010, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (208, 3013, DATE_SUB(NOW(), INTERVAL 2 DAY));

