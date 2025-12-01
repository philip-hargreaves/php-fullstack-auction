DROP database auction_db;
CREATE database auction_db;

USE auction_db;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_datetime DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_roles (
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

CREATE TABLE addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address VARCHAR(255) NOT NULL,
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    parent_category_id INT NULL,
    FOREIGN KEY (parent_category_id) REFERENCES categories(id) ON DELETE SET NULL
    -- ON DELETE Restrict for parent_category_id
);

CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NULL,
    current_auction_id INT NULL,
    item_name VARCHAR(255) NOT NULL,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    is_sold TINYINT(1) NOT NULL DEFAULT 0,

    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE auctions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    winning_bid_id INT NULL,
    category_id INT NULL,
    auction_description TEXT NOT NULL,
    auction_condition ENUM('New', 'Like New', 'Used') NOT NULL,
    start_datetime DATETIME NOT NULL,
    end_datetime DATETIME NOT NULL,
    starting_price DECIMAL(10, 2) NOT NULL,
    reserve_price DECIMAL(10, 2) NULL,
    auction_status ENUM('Scheduled', 'Active', 'Finished') NOT NULL DEFAULT 'Scheduled',

    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    CONSTRAINT chk_auction_times CHECK (end_datetime > start_datetime)
);

CREATE TABLE auction_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auction_id INT NOT NULL,
    image_url VARCHAR(2048) NOT NULL,
    is_main TINYINT(1) NOT NULL DEFAULT 0,
    uploaded_datetime DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE
);

ALTER TABLE items
    ADD CONSTRAINT fk_items_current_auction
        FOREIGN KEY (current_auction_id) REFERENCES auctions(id) ON DELETE SET NULL;

CREATE TABLE bids (
    id INT AUTO_INCREMENT PRIMARY KEY,
    buyer_id INT NULL,
    auction_id INT NULL,
    bid_amount DECIMAL(10, 2) NOT NULL,
    bid_datetime DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE SET NULL
);

-- ALTER statement to add the foreign key *after* bids table exists
ALTER TABLE auctions
ADD CONSTRAINT fk_winning_bid
FOREIGN KEY (winning_bid_id) REFERENCES bids(id) ON DELETE SET NULL;

CREATE TABLE watchlists (
  user_id INT NOT NULL,
  auction_id INT NOT NULL,
  watched_datetime DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, auction_id),
  CONSTRAINT FK_Watchlist_User FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT FK_Watchlist_Auction FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auction_id INT NULL UNIQUE,
    address_id INT NULL,
    order_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    order_status  ENUM('Pending', 'IsConfirmed', 'Canceled', 'Shipped', 'Completed') NOT NULL DEFAULT 'Pending', -- unsure
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE SET NULL,
    FOREIGN KEY (address_id) REFERENCES addresses(id) ON DELETE SET NULL
);

CREATE TABLE ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auction_id INT NULL,
    rater_id INT NULL,
    rated_id INT NULL,
    rating_value TINYINT UNSIGNED NOT NULL CHECK (rating_value BETWEEN 1 AND 5),
    rating_comment TEXT,
    rating_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE SET NULL,
    FOREIGN KEY (rater_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (rated_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auction_id INT NULL,
    started_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE SET NULL
);

CREATE TABLE participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    participant_id INT NULL,
    message_content TEXT NOT NULL,
    sent_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE SET NULL
);