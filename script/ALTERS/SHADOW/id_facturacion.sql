/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  user
 * Created: 12/10/2018
 */

ALTER TABLE `venta_shadow`
ADD COLUMN `id_factura`  bigint(20) NULL AFTER `venta_id`;