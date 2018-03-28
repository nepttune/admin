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

  INDEX `user_username_index` (`username`),
  INDEX `user_active_index` (`active`)
) ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `role`
(
  `id`         INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(255)                NOT NULL,
  `active`     TINYINT DEFAULT 1           NOT NULL,

  INDEX `role_active_index` (`active`)
) ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `user_role`
(
  `id`         INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT(10) UNSIGNED            NOT NULL,
  `role_id`    INT(10) UNSIGNED            NOT NULL,

  CONSTRAINT `user_role_user_id_fk`
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `user_role_role_id_fk`
  FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
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
