--
-- Table structure for table `banco`
--

DROP TABLE IF EXISTS `banco`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banco` (
  `banco_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `banco_nombre` varchar(255) DEFAULT NULL,
  `banco_numero_cuenta` varchar(255) DEFAULT NULL,
  `banco_saldo` decimal(18,2) DEFAULT NULL,
  `banco_cuenta_contable` varchar(255) DEFAULT NULL,
  `banco_titular` varchar(255) DEFAULT NULL,
  `banco_status` tinyint(1) DEFAULT '1',
  `cuenta_id` bigint(20) NOT NULL,
  PRIMARY KEY (`banco_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banco`
--

LOCK TABLES `banco` WRITE;
/*!40000 ALTER TABLE `banco` DISABLE KEYS */;
INSERT INTO `banco` VALUES (1,'BCP','11111111',0.00,'0','TEAYUDO',1,2);
/*!40000 ALTER TABLE `banco` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `caja`
--

DROP TABLE IF EXISTS `caja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `caja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `local_id` bigint(20) NOT NULL,
  `moneda_id` bigint(20) NOT NULL,
  `responsable_id` bigint(20) NOT NULL,
  `estado` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caja`
--

LOCK TABLES `caja` WRITE;
/*!40000 ALTER TABLE `caja` DISABLE KEYS */;
INSERT INTO `caja` VALUES (1,1,1,1,1),(2,1,2,1,0);
/*!40000 ALTER TABLE `caja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caja_desglose`
--

DROP TABLE IF EXISTS `caja_desglose`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `caja_desglose` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caja_id` int(11) NOT NULL,
  `responsable_id` bigint(20) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `saldo` decimal(18,2) NOT NULL DEFAULT '0.00',
  `principal` tinyint(2) NOT NULL DEFAULT '0',
  `retencion` tinyint(2) NOT NULL DEFAULT '0',
  `estado` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caja_desglose`
--

LOCK TABLES `caja_desglose` WRITE;
/*!40000 ALTER TABLE `caja_desglose` DISABLE KEYS */;
INSERT INTO `caja_desglose` VALUES (1,1,1,'Caja Mayor',0.00,1,0,1),(2,1,1,'BCO BCP',0.00,0,0,1);
/*!40000 ALTER TABLE `caja_desglose` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caja_movimiento`
--

DROP TABLE IF EXISTS `caja_movimiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `caja_movimiento` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `caja_desglose_id` bigint(20) NOT NULL,
  `usuario_id` bigint(20) NOT NULL,
  `fecha_mov` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `movimiento` varchar(45) NOT NULL,
  `operacion` varchar(45) NOT NULL,
  `medio_pago` varchar(45) NOT NULL,
  `saldo` decimal(18,2) NOT NULL,
  `saldo_old` decimal(18,2) NOT NULL,
  `ref_id` varchar(50) DEFAULT NULL,
  `ref_val` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caja_movimiento`
--

LOCK TABLES `caja_movimiento` WRITE;
/*!40000 ALTER TABLE `caja_movimiento` DISABLE KEYS */;
/*!40000 ALTER TABLE `caja_movimiento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caja_pendiente`
--

DROP TABLE IF EXISTS `caja_pendiente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `caja_pendiente` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `caja_desglose_id` bigint(20) NOT NULL,
  `usuario_id` bigint(20) NOT NULL,
  `tipo` varchar(45) NOT NULL,
  `IO` tinyint(4) NOT NULL,
  `monto` decimal(18,2) NOT NULL,
  `estado` tinyint(4) NOT NULL,
  `ref_id` varchar(45) DEFAULT NULL,
  `ref_val` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caja_pendiente`
--

LOCK TABLES `caja_pendiente` WRITE;
/*!40000 ALTER TABLE `caja_pendiente` DISABLE KEYS */;
/*!40000 ALTER TABLE `caja_pendiente` ENABLE KEYS */;
UNLOCK TABLES;




--
-- Table structure for table `metodos_pago`
--

DROP TABLE IF EXISTS `metodos_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metodos_pago` (
  `id_metodo` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_metodo` varchar(255) DEFAULT NULL,
  `status_metodo` tinyint(1) DEFAULT '1',
  `tipo_metodo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_metodo`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metodos_pago`
--

LOCK TABLES `metodos_pago` WRITE;
/*!40000 ALTER TABLE `metodos_pago` DISABLE KEYS */;
INSERT INTO `metodos_pago` VALUES (3,'EFECTIVO',1,'CAJA'),(4,'DEPOSITO',1,'BANCO'),(5,'CHEQUE',1,'CAJA'),(6,'NOTA DE CREDITO',1,'CAJA');
/*!40000 ALTER TABLE `metodos_pago` ENABLE KEYS */;
UNLOCK TABLES;


DROP VIEW `vw_monedas_cajas`;
CREATE VIEW `vw_monedas_cajas` AS
    SELECT
        `m`.`id_moneda` AS `id_moneda`,
        `m`.`nombre` AS `nombre`,
        `m`.`tasa_soles` AS `tasa_soles`,
        `m`.`ope_tasa` AS `ope_tasa`,
        `m`.`pais` AS `pais`,
        `m`.`simbolo` AS `simbolo`
    FROM
        `moneda` `m`
    WHERE
        (`m`.`status_moneda` = 1)
