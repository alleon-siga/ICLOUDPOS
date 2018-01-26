ALTER TABLE `cliente`   
  ADD COLUMN `categoria_precio` BIGINT NULL AFTER `cliente_status`,
  ADD FOREIGN KEY (`categoria_precio`) REFERENCES `precios`(`id_precio`);