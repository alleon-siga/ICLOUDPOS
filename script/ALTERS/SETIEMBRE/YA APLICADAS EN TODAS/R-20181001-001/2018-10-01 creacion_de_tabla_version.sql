CREATE TABLE `version` (
`id`  int NOT NULL AUTO_INCREMENT ,
`nombre_empresa`  varchar(255) NULL ,
`ruta_logo1`  varchar(255) NULL COMMENT 'IMAGEN PNG DEL LOGIN' ,
`ruta_logo2`  varchar(255) NULL COMMENT 'IMAGEN SVG DEL LOGIN' ,
`ruta_logo3`  varchar(255) NULL COMMENT 'IMAGEN LOGO DE LA PAGINA PRINCIPAL' ,
`color_fondo`  varchar(255) NULL COMMENT 'COLOR FONDO PANEL IZQUIERDO DEL LOGIN',
`color_boton`  varchar(255) NULL COMMENT 'COLOR DE BOTON PANEL IZQUIERDO DEL LOGIN',
`version`  varchar(255) NULL ,
PRIMARY KEY (`id`)
);

INSERT INTO `version` VALUES ('1', 'SIGA', 'logo_rif.png', 'logo_svg_white.svg', 'punto_de_venta_v2.jpg', '#394263', '#ffcf00', '2.0');