/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  user
 * Created: 25/10/2018
 */

ALTER TABLE `cliente`
ADD COLUMN `status_sunat`  tinyint(1)  NULL DEFAULT 0 COMMENT '0 = baja segun sunat ; 1 = activo segun sunat ; 2= no validado por sunat ' AFTER `cliente_status`;