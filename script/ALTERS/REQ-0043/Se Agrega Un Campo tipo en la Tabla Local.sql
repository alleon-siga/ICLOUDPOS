/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  user
 * Created: 24/10/2018
 */

ALTER TABLE local
ADD COLUMN tipo int NULL DEFAULT 0 COMMENT '0 = Establecimiento de venta (punto de venta); 1 = almacen ' AFTER telefono;