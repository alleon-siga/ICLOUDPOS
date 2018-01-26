CREATE TABLE `cliente_tipo_campo_padre`(
  `tipo_campo_padre_id` BIGINT,
  `tipo_campo_padre_nombre` VARCHAR(255)
);

ALTER TABLE `cliente_tipo_campo_padre`
  CHANGE `tipo_campo_padre_id` `tipo_campo_padre_id` BIGINT(20) NOT NULL,
  ADD PRIMARY KEY (`tipo_campo_padre_id`);

ALTER TABLE `cliente_tipo_campo`
  ADD COLUMN `padre_id` BIGINT NULL AFTER `valor`,
  ADD FOREIGN KEY (`padre_id`) REFERENCES `cliente_tipo_campo_padre`(`tipo_campo_padre_id`),
  ENGINE=INNODB;

ALTER TABLE `cliente_tipo_campo`
  ADD FOREIGN KEY (`id_cliente`) REFERENCES `cliente`(`id_cliente`);
ALTER TABLE `cliente_tipo_campo_padre`
  CHANGE `tipo_campo_padre_id` `tipo_campo_padre_id` BIGINT(20) NOT NULL AUTO_INCREMENT;



ALTER TABLE `cliente_tipo_campo`
  ADD COLUMN `input_type` VARCHAR(100) NULL AFTER `padre_id`;


ALTER TABLE `cliente_tipo_campo`
  CHANGE `valor` `nombre` VARCHAR(100) NOT NULL;

  ALTER TABLE `cliente_tipo_campo_padre`
  ADD COLUMN `tipo_campo_padre_slug` VARCHAR(255) NULL AFTER `tipo_campo_padre_nombre`;

ALTER TABLE `cliente_tipo_campo`
  ADD COLUMN `slug` VARCHAR(100) NULL AFTER `nombre`;

ALTER TABLE `cliente_tipo_campo`
  DROP COLUMN `id_cliente`,
  DROP INDEX `id_cliente`,
  DROP FOREIGN KEY `cliente_tipo_campo_ibfk_2`;


ALTER TABLE `cliente_campo_valor`
  ADD FOREIGN KEY (`campo_cliente`) REFERENCES `cliente`(`id_cliente`),
  ENGINE=INNODB;


ALTER TABLE `cliente_campo_valor`
  ADD COLUMN `tipo_campo` BIGINT(20) NULL AFTER `campo_valor`,
  ADD FOREIGN KEY (`tipo_campo`) REFERENCES `cliente_tipo_campo`(`id_tipo`);

