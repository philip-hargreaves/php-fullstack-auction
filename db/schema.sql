USE auction_db;

CREATE TABLE `Role` (
  `roleID` INT AUTO_INCREMENT PRIMARY KEY,
  `roleName` VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE `User` (
  `userID` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `isActive` TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE `UserRole` (
  `userID` INT NOT NULL,
  `roleID` INT NOT NULL,
  PRIMARY KEY (`userID`, `roleID`),
  FOREIGN KEY (`userID`) REFERENCES `User`(`userID`),
  FOREIGN KEY (`roleID`) REFERENCES `Role`(`roleID`)
);

CREATE TABLE `Item` (
  `itemID` INT AUTO_INCREMENT PRIMARY KEY,
  `sellerID` INT NOT NULL,
  `itemName` VARCHAR(100) NOT NULL,
  `itemDescription` TEXT NULL,
  `itemCondition` ENUM('New', 'Like New', 'Used') NULL,
  `itemStatus` ENUM('Available', 'InAuction', 'Sold', 'Deleted') NOT NULL DEFAULT 'Available',
  FOREIGN KEY (`sellerID`) REFERENCES `User`(`userID`)
  -- `categoryID` INT NULL,
  -- Assuming a FOREIGN KEY (categoryID) REFERENCES Category(categoryID)
);

CREATE TABLE `Auction` (
  `auctionID` INT AUTO_INCREMENT PRIMARY KEY,
  `itemID` INT NOT NULL,
  `winningBidID` INT NULL,
  `startDateTime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `endDateTime` DATETIME NOT NULL,
  `startingPrice` DECIMAL(10, 2) NOT NULL,
  `reservePrice` DECIMAL(10, 2) NULL,
  `auctionStatus` ENUM('Pending', 'Active', 'Finished') NOT NULL DEFAULT 'Pending',
  FOREIGN KEY (`itemID`) REFERENCES `Item`(`itemID`),
  CONSTRAINT `chk_auction_times` CHECK (`endDateTime` > `startDateTime`)
  -- `paymentDeadline` DATETIME NULL,
);

CREATE TABLE `Bid` (
  `bidID` INT AUTO_INCREMENT PRIMARY KEY,
  `buyerID` INT NOT NULL,
  `auctionID` INT NOT NULL,
  `bidAmount` DECIMAL(10, 2) NOT NULL,
  `bidDateTime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`buyerID`) REFERENCES `User`(`userID`),
  FOREIGN KEY (`auctionID`) REFERENCES `Auction`(`auctionID`)
);

-- ALTER statement to add the foreign key *after* Bid table exists
ALTER TABLE `Auction`
ADD CONSTRAINT `fk_winning_bid`
FOREIGN KEY (`winningBidID`) REFERENCES `Bid`(`bidID`)
ON DELETE SET NULL; -- If a winning bid is deleted, set the FK to NULL