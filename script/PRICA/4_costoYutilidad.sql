alter table `ingreso` add column `costo_por` int(11) not null default 0 AFTER `facturado`

alter table `ingreso` add column `utilidad_por` int(11) not null default 0 AFTER `costo_por`