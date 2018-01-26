insert into `configuraciones` (`config_key`, `config_value`) values('FACTURAR_INGRESO','NO');
ALTER TABLE `ingreso`
  ADD COLUMN `factura_ingreso_id` BIGINT DEFAULT NULL  NULL AFTER `tasa_cambio`;
  ALTER TABLE `ingreso`
  ADD COLUMN `facturado` BOOLEAN DEFAULT 0  NULL AFTER `factura_ingreso_id`;

