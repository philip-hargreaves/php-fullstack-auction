DROP database auction_db;
CREATE database auction_db;

USE auction_db;

CREATE TABLE `roles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `role_name` VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE `user_roles` (
  `user_id` INT NOT NULL,
  `role_id` INT NOT NULL,
  PRIMARY KEY (`user_id`, `role_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`)
);

CREATE TABLE `items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `seller_id` INT NOT NULL,
  `item_name` VARCHAR(100) NOT NULL,
  `item_description` TEXT NULL,
  `item_condition` ENUM('New', 'Like New', 'Used') NULL,
  `item_status` ENUM('Available', 'InAuction', 'Sold', 'Deleted') NOT NULL DEFAULT 'Available',
  FOREIGN KEY (`seller_id`) REFERENCES `users`(`id`)
  -- `category_id` INT NULL,
  -- Assuming a FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE `auctions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `item_id` INT NOT NULL,
  `winning_bid_id` INT NULL,
  `start_datetime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_datetime` DATETIME NOT NULL,
  `starting_price` DECIMAL(10, 2) NOT NULL,
  `reserve_price` DECIMAL(10, 2) NULL,
  `auction_status` ENUM('Pending', 'Active', 'Finished') NOT NULL DEFAULT 'Pending',
  FOREIGN KEY (`item_id`) REFERENCES `items`(`id`),
  CONSTRAINT `chk_auction_times` CHECK (`end_datetime` > `start_datetime`)
  -- `payment_deadline` DATETIME NULL,
);

CREATE TABLE `bids` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `buyer_id` INT NOT NULL,
  `auction_id` INT NOT NULL,
  `bid_amount` DECIMAL(10, 2) NOT NULL,
  `bid_datetime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`buyer_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`auction_id`) REFERENCES `auctions`(`id`)
);

CREATE TABLE `images` (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    auction_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    FOREIGN KEY (auction_id) REFERENCES `auctions` (id) ON DELETE CASCADE
);

-- ALTER statement to add the foreign key *after* bids table exists
ALTER TABLE `auctions`
ADD CONSTRAINT `fk_winning_bid`
FOREIGN KEY (`winning_bid_id`) REFERENCES `bids`(`id`)
ON DELETE SET NULL; -- If a winning bid is deleted, set the FK to NULL

CREATE TABLE watchlist (
  user_id INT NOT NULL,
  auction_id INT NOT NULL,
  watched_datetime DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, auction_id),
  CONSTRAINT FK_Watchlist_User FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT FK_Watchlist_Auction FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
