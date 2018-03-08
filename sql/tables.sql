CREATE TABLE IF NOT EXISTS `user`
(
  `id`         INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username`   VARCHAR(255)                NOT NULL,
  `password`   VARCHAR(255)                NOT NULL,
  `registered` DATE                        NOT NULL,
  `active`     TINYINT DEFAULT 1           NOT NULL,

  INDEX `user_username_index` (`username`),
  INDEX `user_active_index` (`active`)
);

CREATE TABLE IF NOT EXISTS `role`
(
  `id`         INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(255)                NOT NULL,
  `active`     TINYINT DEFAULT 1           NOT NULL,

  INDEX `role_active_index` (`active`)
);

CREATE TABLE IF NOT EXISTS `user_role`
(
  `id`         INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT(10) UNSIGNED            NOT NULL,
  `role_id`    INT(10) UNSIGNED            NOT NULL,

  CONSTRAINT `user_role_user_id_fk`
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `user_role_role_id_fk`
  FOREIGN KEY (`role_id`) REFERENCES `role` (`id`),
);
