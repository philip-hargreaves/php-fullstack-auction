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