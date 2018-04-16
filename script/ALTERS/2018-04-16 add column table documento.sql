ALTER TABLE `documentos`
ADD COLUMN `abr_doc`  varchar(3) NULL AFTER `estado`;

update documentos set abr_doc='FA' where id_doc=1;
update documentos set abr_doc='NC' where id_doc=2;
update documentos set abr_doc='BO' where id_doc=3;
update documentos set abr_doc='GR' where id_doc=4;
update documentos set abr_doc='PCV' where id_doc=5;
update documentos set abr_doc='NP' where id_doc=6;