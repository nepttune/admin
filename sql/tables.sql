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
  `datetime`   DATETIME                    NOT NULL,
  
  INDEX `log_login_ip_address_index` (`ip_address`)
) ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `user`
(
  `id`         INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username`   VARCHAR(255)                NOT NULL,
  `password`   VARCHAR(255)                NOT NULL,
  `registered` DATE                        NOT NULL,
  `active`     TINYINT DEFAULT 1           NOT NULL,

  INDEX `user_active_username_index` (`active`, `username`)
) ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `user_access`
(
  `id`         INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT(10) UNSIGNED            NOT NULL,
  `resource`   VARCHAR(255)                NOT NULL,
  `privilege`  VARCHAR(255)                NOT NULL,

  CONSTRAINT `user_access_user_id_fk`
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),

  INDEX `user_access_user_id_resource_privilege_index` (`user_id`, `resource`, `privilege`)
) ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `subscription_type` (
  `id`         INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(255)                NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `user_subscription_type`
(
  `id`                   INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`              INT(10) UNSIGNED            NOT NULL,
  `subscription_type_id` INT(10) UNSIGNED            NOT NULL,

  CONSTRAINT `user_subscription_type_user_id_fk`
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `user_subscription_type_subscription_type_id_fk`
  FOREIGN KEY (`subscription_type_id`) REFERENCES `subscription_type` (`id`)
) ENGINE = INNODB;

ALTER TABLE `subscription`
  ADD CONSTRAINT `subscription_user_id_fk` 
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
  
INSERT INTO `user` (`id`, `username`, `password`, `registered`, `active`) VALUES
(1, 'test', '$2y$10$yX2aYVewjkhJywP8QIpyvOtFqr8xYIIAh4fIwZkP67DPVKk7WCt6.', '2018-04-23', 1);
