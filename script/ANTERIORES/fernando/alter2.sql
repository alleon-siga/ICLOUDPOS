insert into `configuraciones` (`config_key`, `config_value`) values('PAGOS_ANTICIPADOS','SI');
insert into `configuraciones` (`config_key`, `config_value`) values('ADELANTO_PAGO_CUOTA','0');
ALTER TABLE `credito_cuotas`
  ADD COLUMN `fecha_pago` DATETIME NULL AFTER `ispagado`;
ALTER TABLE `credito`
  ADD COLUMN `pago_anticipado` INT(10) NULL AFTER `num_corre_gr`;

