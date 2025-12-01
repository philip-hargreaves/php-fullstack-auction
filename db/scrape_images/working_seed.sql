INSERT IGNORE INTO items (id, seller_id, item_name) VALUES
    (2001, 102, 'ASUS ROG Strix G16 Gaming Laptop RTX 4070');

INSERT IGNORE INTO auctions (id, item_id, category_id, auction_description, auction_condition, start_datetime, end_datetime, starting_price, reserve_price, auction_status) VALUES
    (3001, 2001, 111, 'Powerful gaming laptop with Intel i9, 32GB RAM, 1TB SSD, RTX 4070 graphics. 16 inch 165Hz display. Excellent condition, barely used. Perfect for gaming and content creation.', 'Like New', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 1 DAY), 1299.99, 1600.00, 'Active'),
    (3002, 2001, 113, 'Excellent condition MacBook Pro with M2 chip, 16GB RAM, 512GB SSD. Perfect for professionals and creatives. Includes original charger and box. Barely used, like new condition.', 'Like New', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(NOW(), INTERVAL 2 DAY), 1899.99, 2200.00, 'Active'),
    (3003, 2001, 211, 'Brand new iPhone 15 Pro Max in Titanium Blue. Still sealed in original packaging. Includes all accessories. Unlocked for all carriers.', 'New', DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_ADD(NOW(), INTERVAL 1 DAY), 1099.99, 1400.00, 'Active'),
    (3004, 2001, 212, 'Flagship smartphone with S Pen. Excellent condition, barely used. Includes original box and charger. Unlocked.', 'Like New', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 3 DAY), 899.99, 1200.00, 'Active');
