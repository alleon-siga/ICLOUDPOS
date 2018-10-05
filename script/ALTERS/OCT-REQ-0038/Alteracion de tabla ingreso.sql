/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  user
 * Created: 04/10/2018
 */

ALTER TABLE `ingreso` 
ADD COLUMN `medio_pago` varchar(45) NULL AFTER `total_ingreso`;