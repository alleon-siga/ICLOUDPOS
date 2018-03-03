ALTER TABLE `credito`
ADD COLUMN `tasa_interes` FLOAT NULL DEFAULT 0 AFTER `periodo_gracia`;
