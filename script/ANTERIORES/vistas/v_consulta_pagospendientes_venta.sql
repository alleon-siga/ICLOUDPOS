DELIMITER $$


DROP VIEW IF EXISTS `v_consulta_pagospendientes_venta`$$

CREATE ALGORITHM=UNDEFINED  SQL SECURITY DEFINER VIEW `v_consulta_pagospendientes_venta` AS (
SELECT
  `v`.`venta_id`              AS `Venta_id`,
  `c`.`id_cliente`            AS `Cliente_Id`,
  `c`.`razon_social`          AS `Cliente`,
  `p`.`nombre`                AS `Personal`,
  CAST(`v`.`fecha` AS DATE)   AS `FechaReg`,
  IFNULL((SELECT MAX(`cp`.`dat_cronpago_fecpago`) FROM `cronogramapago` `cp` WHERE (`cp`.`nVenCodigo` = `v`.`venta_id`)),'') AS `FechaVenc`,
  `v`.`total`                 AS `MontoTotal`,
  IFNULL((SELECT SUM(`cp`.`dec_cronpago_pagorecibido`) FROM `cronogramapago` `cp` WHERE (`cp`.`nVenCodigo` = `v`.`venta_id`)),0.00) AS `MontoCancelado`,
  (`v`.`total` - IFNULL((SELECT SUM(`cp`.`dec_cronpago_pagorecibido`) FROM `cronogramapago` `cp` WHERE (`cp`.`nVenCodigo` = `v`.`venta_id`)),0.00)) AS `SaldoPendiente`,
  CONCAT(`d`.`documento_Serie`,'-',`d`.`documento_Numero`) AS `NroVenta`,
  `d`.`nombre_tipo_documento` AS `TipoDocumento`,
  IFNULL((SELECT `cd`.`var_credito_estado` FROM `credito` `cd` WHERE (`cd`.`id_venta` = `v`.`venta_id`)),'') AS `Estado`,
  `cond`.`nombre_condiciones` AS `FormaPago`
FROM ((((`venta` `v`
      JOIN `cliente` `c`
        ON ((`c`.`id_cliente` = `v`.`id_cliente`)))
     JOIN `documento_venta` `d`
       ON ((`d`.`id_tipo_documento` = `v`.`numero_documento`)))
    JOIN `usuario` `p`
      ON ((`p`.`nUsuCodigo` = `v`.`id_vendedor`)))
   JOIN `condiciones_pago` `cond`
     ON ((`cond`.`id_condiciones` = `v`.`condicion_pago`)))
WHERE (((SELECT
           `condiciones_pago`.`dias`
         FROM `condiciones_pago`
         WHERE (`v`.`condicion_pago` = `condiciones_pago`.`id_condiciones`)) > 0)
       AND ((SELECT
               SUM(`cp`.`dec_cronpago_pagorecibido`)
             FROM `cronogramapago` `cp`
             WHERE (`cp`.`nVenCodigo` = `v`.`venta_id`)) < `v`.`total`) ) AND v.venta_status='COMPLETADO' )$$

DELIMITER ;