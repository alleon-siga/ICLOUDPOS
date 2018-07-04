ALTER TABLE `kardex` 
ADD COLUMN `costo` decimal(18,2) NULL AFTER `cantidad_saldo`,
ADD COLUMN `moneda_id` int NULL AFTER `costo`;