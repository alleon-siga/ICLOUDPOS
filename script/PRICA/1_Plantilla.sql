CREATE  TABLE `plantilla_tipo` (
  `idtipo_plantilla` INT NOT NULL ,
  `campo_valor` VARCHAR(45) NULL ,
  `estado` INT NULL ,
  PRIMARY KEY (`idtipo_plantilla`) );

CREATE  TABLE `plantilla_campo_valor` (
  `idplantilla_campo_valor` INT NOT NULL ,
  `tipo_plantilla` INT NULL ,
  `campo_valor` VARCHAR(45) NULL ,
  `estado` INT NULL ,
  PRIMARY KEY (`idplantilla_campo_valor`) );