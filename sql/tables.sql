# This file is part of Nepttune (https://www.peldax.com)
#
# Copyright (c) 2018 Václav Pelíšek (info@peldax.com)
#
# This software consists of voluntary contributions made by many individuals
# and is licensed under the MIT license. For more information, see
# <https://www.peldax.com>.

CREATE TABLE IF NOT EXISTS `log_login`
(
  `id`         INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ip_address` VARBINARY(16)               NOT NULL,
  `username`   VARCHAR(255)                NOT NULL,
  `result`     ENUM('success', 'failure')  NOT NULL,
  `datetime`   DATETIME                    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  INDEX `log_login_ip_address_index` (`ip_address`)
) ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `role`
(
  `id`          INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(255)                NOT NULL,
  `description` TEXT                        DEFAULT NULL,
  `active`      TINYINT(1) DEFAULT 1        NOT NULL
) ENGINE = INNODB 
  CHARACTER SET `utf8mb4`
  COLLATE `utf8mb4_general_ci`;

CREATE TABLE IF NOT EXISTS `role_access`
(
  `id`        INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `role_id`   INT(10) UNSIGNED            NOT NULL,
  `resource`  VARCHAR(255)                NOT NULL,
  `privilege` VARCHAR(255)                DEFAULT NULL,

  CONSTRAINT `role_access_role_id_fk`
  FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) ENGINE = INNODB 
  CHARACTER SET `utf8mb4`
  COLLATE `utf8mb4_general_ci`;

CREATE TABLE IF NOT EXISTS `user`
(
  `id`         INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `role_id`    INT(10) UNSIGNED            NOT NULL,
  `username`   VARCHAR(255)                NOT NULL,
  `password`   VARCHAR(255)                NOT NULL,
  `registered` DATE                        NOT NULL,
  `root`       TINYINT(1) DEFAULT 0        NOT NULL,
  `active`     TINYINT(1) DEFAULT 1        NOT NULL,

  CONSTRAINT `user_role_id_fk`
  FOREIGN KEY (`role_id`) REFERENCES `role` (`id`),
  
  INDEX `user_active_username_index` (`active`, `username`)
) ENGINE = INNODB 
  CHARACTER SET `utf8mb4`
  COLLATE `utf8mb4_general_ci`;

CREATE TABLE IF NOT EXISTS `subscription_type` (
  `id`         INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(255)                NOT NULL
) ENGINE = INNODB 
  CHARACTER SET `utf8mb4`
  COLLATE `utf8mb4_general_ci`;

CREATE TABLE IF NOT EXISTS `user_subscription_type`
(
  `id`                   INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`              INT(10) UNSIGNED            NOT NULL,
  `subscription_type_id` INT(10) UNSIGNED            NOT NULL,

  CONSTRAINT `user_subscription_type_user_id_fk`
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  
  CONSTRAINT `user_subscription_type_subscription_type_id_fk`
  FOREIGN KEY (`subscription_type_id`) REFERENCES `subscription_type` (`id`)
) ENGINE = INNODB 
  CHARACTER SET `utf8mb4`
  COLLATE `utf8mb4_general_ci`;

ALTER TABLE `subscription`
  ADD CONSTRAINT `subscription_user_id_fk` 
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
  
INSERT INTO `user` (`id`, `username`, `password`, `registered`, `root`, `active`) VALUES
(1, 'test', '$2y$10$yX2aYVewjkhJywP8QIpyvOtFqr8xYIIAh4fIwZkP67DPVKk7WCt6.', '2018-04-23', 1, 1);
