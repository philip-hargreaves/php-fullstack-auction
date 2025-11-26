USE auction_db;

-- Insert roles (if they don't exist)
INSERT IGNORE INTO roles (id, role_name) VALUES
    (1, 'buyer'),
    (2, 'seller');

-- Insert test users with hashed passwords
-- Password: password123 (hashed with bcrypt)
INSERT IGNORE INTO users (id, username, email, password, is_active) VALUES
    (101, 'john_buyer', 'john@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1),
    (102, 'jane_seller', 'jane@example.com', '$2y$12$LbSlJ7uaWPoUF9OHsr58lOXVWwao14j42jXP3xpha8iFfSu1oQ8um', 1);

-- Assign roles to users
INSERT IGNORE INTO user_roles (user_id, role_id) VALUES
    (101, 1),  -- john is a buyer
    (102, 2);  -- jane is a seller

-- Insert items (if they don't exist)
INSERT IGNORE INTO items (id, seller_id, item_name, item_description, item_condition) VALUES
    (1111,
     101,
     'Dell S2725HS 27"" LED Monitor',
     'With clear FHD resolution, high 100Hz refresh rate, integrated dual 5W speakers and subtle texture on the back inspired by Japanese sand raking, any activity becomes an immersive experience. Enjoy more detailed sound with spacious audio featuring greater output power, deeper frequency response and more decibel range than the previous generation.',
     'New');

-- Insert auction (if they don't exist)
INSERT IGNORE INTO auctions (id, item_id, start_datetime, end_datetime, starting_price, reserve_price, auction_status) VALUES
    (2222,
     1111,
     '2025-08-10 23:59:59',
     '2025-12-15 12:00:00',
     50.00,
     180.00,
     'Active');

-- Insert item image (if they don't exist)
INSERT IGNORE INTO item_images (id, item_id, image_url, is_main, uploaded_datetime) VALUES
    (1, 1111, 'https://images.shopcdn.co.uk/18/c8/18c8f85f068472284acf4e1b62f8cb16/2048x2048/webp/fit?force=true&quality=80&compression=80',
        1, '2025-08-10 23:59:59'),
    (2, 1111, 'https://images.shopcdn.co.uk/c5/f2/c5f25fda773c2c9a5c70c02003e20476/2048x2048/webp/fit?force=true&quality=80&compression=80',
        0, '2025-08-10 23:59:59');
