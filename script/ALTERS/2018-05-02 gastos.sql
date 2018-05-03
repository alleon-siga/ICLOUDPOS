ALTER TABLE `gastos`
ADD COLUMN `gravable`  char(1) NULL DEFAULT 0 COMMENT 'GRAVABLE 1=SI Y 0=NO' AFTER `motivo_eliminar`,
ADD COLUMN `id_documento`  int NULL COMMENT 'DOCUMENTO' AFTER `gravable`,
ADD COLUMN `serie`  varchar(255) NULL COMMENT 'NUMERO DE SERIE' AFTER `id_documento`,
ADD COLUMN `numero`  varchar(255) NULL COMMENT 'NUMERO' AFTER `serie`;

INSERT INTO documentos VALUES('7', 'RECIBO DE CAJA', '1', 'RC');

ALTER TABLE `documentos`
ADD COLUMN `compras`  char(1) NULL DEFAULT 0 COMMENT '0=NO, 1=SI' AFTER `abr_doc`,
ADD COLUMN `ventas`  char(1) NULL DEFAULT 0 COMMENT '0=NO, 1=SI' AFTER `compras`,
ADD COLUMN `gastos`  char(1) NULL DEFAULT 0 COMMENT '0=NO, 1=SI' AFTER `ventas`;

UPDATE documentos set gastos=1 where id_doc in(1,3,6,7);
UPDATE documentos set compras=1 where id_doc in(1,3,6);
UPDATE documentos set ventas=1 where id_doc in(1,3,6);