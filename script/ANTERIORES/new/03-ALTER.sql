CREATE TABLE `producto_series` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `producto_id` BIGINT(20) NOT NULL,
  `serie` VARCHAR(255) NULL,
  `local_id` BIGINT(20) NOT NULL,
  PRIMARY KEY (`id`));