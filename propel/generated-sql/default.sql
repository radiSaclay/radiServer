
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- user
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user`
(
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `is_admin` TINYINT(1) DEFAULT 0 NOT NULL,
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `user_u_ce4c89` (`email`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- farm
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `farm`;

CREATE TABLE `farm`
(
    `owner_id` INTEGER NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `address` TEXT NOT NULL,
    `web_site` VARCHAR(255),
    `phone` VARCHAR(255),
    `email` VARCHAR(255),
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `farm_fi_ac5b84` (`owner_id`),
    CONSTRAINT `farm_fk_ac5b84`
        FOREIGN KEY (`owner_id`)
        REFERENCES `user` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- product
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `product`;

CREATE TABLE `product`
(
    `parent_id` INTEGER,
    `name` VARCHAR(255) NOT NULL,
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `product_fi_2217fa` (`parent_id`),
    CONSTRAINT `product_fk_2217fa`
        FOREIGN KEY (`parent_id`)
        REFERENCES `product` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- event
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `event`;

CREATE TABLE `event`
(
    `farm_id` INTEGER,
    `product_id` INTEGER,
    `description` TEXT NOT NULL,
    `publish_at` DATETIME,
    `begin_at` DATETIME,
    `end_at` DATETIME,
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `event_fi_0f5ed8` (`product_id`),
    INDEX `event_fi_388cc1` (`farm_id`),
    CONSTRAINT `event_fk_0f5ed8`
        FOREIGN KEY (`product_id`)
        REFERENCES `product` (`id`),
    CONSTRAINT `event_fk_388cc1`
        FOREIGN KEY (`farm_id`)
        REFERENCES `farm` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- order
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `order`;

CREATE TABLE `order`
(
    `user_id` INTEGER NOT NULL,
    `farm_id` INTEGER NOT NULL,
    `product_id` INTEGER NOT NULL,
    `quantity` DECIMAL NOT NULL,
    `collected_at` DATETIME,
    `delivered_at` DATETIME,
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`id`),
    INDEX `order_fi_29554a` (`user_id`),
    INDEX `order_fi_388cc1` (`farm_id`),
    INDEX `order_fi_0f5ed8` (`product_id`),
    CONSTRAINT `order_fk_29554a`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`id`),
    CONSTRAINT `order_fk_388cc1`
        FOREIGN KEY (`farm_id`)
        REFERENCES `farm` (`id`),
    CONSTRAINT `order_fk_0f5ed8`
        FOREIGN KEY (`product_id`)
        REFERENCES `product` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- farm_product
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `farm_product`;

CREATE TABLE `farm_product`
(
    `farm_id` INTEGER NOT NULL,
    `product_id` INTEGER NOT NULL,
    PRIMARY KEY (`farm_id`,`product_id`),
    INDEX `farm_product_fi_0f5ed8` (`product_id`),
    CONSTRAINT `farm_product_fk_0f5ed8`
        FOREIGN KEY (`product_id`)
        REFERENCES `product` (`id`),
    CONSTRAINT `farm_product_fk_388cc1`
        FOREIGN KEY (`farm_id`)
        REFERENCES `farm` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- pin
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `pin`;

CREATE TABLE `pin`
(
    `event_id` INTEGER NOT NULL,
    `user_id` INTEGER NOT NULL,
    PRIMARY KEY (`event_id`,`user_id`),
    INDEX `pin_fi_29554a` (`user_id`),
    CONSTRAINT `pin_fk_29554a`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`id`),
    CONSTRAINT `pin_fk_b54508`
        FOREIGN KEY (`event_id`)
        REFERENCES `event` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- subscription
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `subscription`;

CREATE TABLE `subscription`
(
    `user_id` INTEGER NOT NULL,
    `subscription_id` INTEGER NOT NULL,
    `subscription_type` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`user_id`,`subscription_id`,`subscription_type`),
    CONSTRAINT `subscription_fk_29554a`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`id`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
