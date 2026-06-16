CREATE DATABASE IF NOT EXISTS restaurant_testing
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE restaurant_testing;

CREATE TABLE IF NOT EXISTS `users` (
    `email`         VARCHAR(255) NOT NULL PRIMARY KEY,
    `firstName`     VARCHAR(255) NOT NULL,
    `lastName`      VARCHAR(255) NOT NULL,
    `contact`       VARCHAR(20)  NOT NULL,
    `password`      VARCHAR(255) NOT NULL,
    `dateCreated`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `profile_image` VARCHAR(255) NOT NULL DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `menucategory` (
    `catId`       INT(11)      NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `catName`     VARCHAR(255) NOT NULL,
    `dateCreated` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `menuitem` (
    `itemId`      INT(11)      NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `itemName`    VARCHAR(255) NOT NULL,
    `catName`     VARCHAR(255) NOT NULL,
    `price`       VARCHAR(255) NOT NULL,
    `status`      ENUM('Available','Unavailable') NOT NULL DEFAULT 'Available',
    `description` VARCHAR(255) NOT NULL,
    `image`       VARCHAR(255) NOT NULL,
    `dateCreated` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updatedDate` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `is_popular`  TINYINT(1)   DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `cart` (
    `id`          INT(11)      NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `itemName`    VARCHAR(255) NOT NULL,
    `price`       DECIMAL(10,0) NOT NULL,
    `image`       VARCHAR(255) NOT NULL,
    `quantity`    INT(11)      NOT NULL,
    `catName`     VARCHAR(255) NOT NULL,
    `email`       VARCHAR(255) NOT NULL,
    `total_price` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `orders` (
    `order_id`       INT(11)      NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email`          VARCHAR(255) NOT NULL,
    `firstName`      VARCHAR(255) NOT NULL,
    `lastName`       VARCHAR(255) NOT NULL,
    `phone`          VARCHAR(20)  NOT NULL,
    `address`        TEXT         NOT NULL,
    `pmode`          VARCHAR(50)  NOT NULL DEFAULT 'cash',
    `payment_status` VARCHAR(50)  NOT NULL DEFAULT 'Pending',
    `sub_total`      DECIMAL(10,2) NOT NULL,
    `grand_total`    DECIMAL(10,2) NOT NULL,
    `order_date`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `order_status`   VARCHAR(50)  NOT NULL DEFAULT 'Order Placed',
    `cancel_reason`  TEXT,
    `note`           TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `order_items` (
    `id`          INT(11)      NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `order_id`    INT(11)      NOT NULL,
    `itemName`    VARCHAR(255) NOT NULL,
    `image`       VARCHAR(255) NOT NULL,
    `quantity`    INT(11)      NOT NULL,
    `price`       DECIMAL(10,2) NOT NULL,
    `total_price` DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`order_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `reservations` (
    `reservation_id` VARCHAR(50)  NOT NULL PRIMARY KEY,
    `email`          VARCHAR(255) NOT NULL,
    `name`           VARCHAR(255) NOT NULL,
    `contact`        VARCHAR(20)  NOT NULL,
    `noOfGuests`     INT(11)      NOT NULL,
    `reservedTime`   VARCHAR(10)  NOT NULL,
    `reservedDate`   DATE         NOT NULL,
    `reservedAt`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status`         VARCHAR(50)  NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `reviews` (
    `review_id`   INT(11)      NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email`       VARCHAR(255) NOT NULL,
    `order_id`    INT(11)      NOT NULL,
    `rating`      INT(1)       NOT NULL,
    `review_text` TEXT         NOT NULL,
    `review_date` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status`      VARCHAR(50)  NOT NULL DEFAULT 'Pending',
    `response`    TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `staff` (
    `id`            INT(11)      NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `firstName`     VARCHAR(255) NOT NULL,
    `lastName`      VARCHAR(255) NOT NULL,
    `email`         VARCHAR(255) NOT NULL UNIQUE,
    `contact`       VARCHAR(20)  NOT NULL,
    `role`          VARCHAR(50)  NOT NULL DEFAULT 'Staff',
    `password`      VARCHAR(255) NOT NULL,
    `createdAt`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updatedAt`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `profile_image` VARCHAR(255) NOT NULL DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;