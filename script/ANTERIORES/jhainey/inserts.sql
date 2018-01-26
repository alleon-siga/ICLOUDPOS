insert into `cliente_tipo_campo_padre` (`tipo_campo_padre_id`, `tipo_campo_padre_nombre`, `tipo_campo_padre_slug`) values('2','DIRECCION','direccion');
insert into `cliente_tipo_campo_padre` (`tipo_campo_padre_id`, `tipo_campo_padre_nombre`, `tipo_campo_padre_slug`) values('3','PAGINA WEB','pagina_web');
insert into `cliente_tipo_campo_padre` (`tipo_campo_padre_id`, `tipo_campo_padre_nombre`, `tipo_campo_padre_slug`) values('4','RAZON SOCIAL','razon_social');
insert into `cliente_tipo_campo_padre` (`tipo_campo_padre_id`, `tipo_campo_padre_nombre`, `tipo_campo_padre_slug`) values('5','CORREO','correo');
insert into `cliente_tipo_campo_padre` (`tipo_campo_padre_id`, `tipo_campo_padre_nombre`, `tipo_campo_padre_slug`) values('6','FECHA NACIMIENTO','fecha_nacimiento');
insert into `cliente_tipo_campo_padre` (`tipo_campo_padre_id`, `tipo_campo_padre_nombre`, `tipo_campo_padre_slug`) values('7','NOTA','nota');

insert into `cliente_tipo_campo` (`id_tipo`, `nombre`, `slug`, `padre_id`, `input_type`) values('2','Pagina web','pagina_web','3','text');
insert into `cliente_tipo_campo` (`id_tipo`, `nombre`, `slug`, `padre_id`, `input_type`) values('3','Calle','calle','2','text');
insert into `cliente_tipo_campo` (`id_tipo`, `nombre`, `slug`, `padre_id`, `input_type`) values('4','Ciudad','ciudad','2','select');
insert into `cliente_tipo_campo` (`id_tipo`, `nombre`, `slug`, `padre_id`, `input_type`) values('5','Distrito','distrito','2','select');
insert into `cliente_tipo_campo` (`id_tipo`, `nombre`, `slug`, `padre_id`, `input_type`) values('6','Direccion envio','direccion_envio','2','checkbox');
insert into `cliente_tipo_campo` (`id_tipo`, `nombre`, `slug`, `padre_id`, `input_type`) values('7','Direccion facturacion','direccion_facturacion','2','checkbox');



insert into `cliente_campo_valor` (`id_campo`, `campo_cliente`, `campo_valor`, `tipo_campo`) values('2','2','www.ejemplo.com','2');
insert into `cliente_campo_valor` (`id_campo`, `campo_cliente`, `campo_valor`, `tipo_campo`) values('3','1','Calle la atalntida','3');
insert into `cliente_campo_valor` (`id_campo`, `campo_cliente`, `campo_valor`, `tipo_campo`) values('4','1','1','4');
insert into `cliente_campo_valor` (`id_campo`, `campo_cliente`, `campo_valor`, `tipo_campo`) values('5','1','1','5');


