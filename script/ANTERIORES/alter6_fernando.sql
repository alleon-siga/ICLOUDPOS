CREATE TABLE `historial_cronograma` (
  `historialcrono_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cronogramapago_id` bigint(20) DEFAULT NULL,
  `historialcrono_fecha` datetime DEFAULT NULL,
  `historialcrono_monto` float(20,4) DEFAULT NULL,
  `historialcrono_tipopago` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`historialcrono_id`),
  KEY `cronogramapago_id` (`cronogramapago_id`),
  KEY `historialcrono_tipopago` (`historialcrono_tipopago`),
  CONSTRAINT `historial_cronograma_ibfk_1` FOREIGN KEY (`cronogramapago_id`) REFERENCES `cronogramapago` (`nCPagoCodigo`),
  CONSTRAINT `historial_cronograma_ibfk_2` FOREIGN KEY (`historialcrono_tipopago`) REFERENCES `metodos_pago` (`id_metodo`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1;

ALTER TABLE `cronogramapago`
  DROP COLUMN `cronograma_metodo_pago`,
  DROP INDEX `cronograma_metodo_pago`,
  DROP FOREIGN KEY `cronogramapago_ibfk_2`;
