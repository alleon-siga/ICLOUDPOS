

DROP TABLE IF EXISTS `departamento`;


CREATE TABLE `departamento` (
  `departamento_id` bigint(20) NOT NULL,
  `subId_ubigeo` varchar(2) NOT NULL,
  `departamento_nombre` varchar(45) DEFAULT NULL,
  `pais_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



INSERT INTO `departamento` (`departamento_id`, `subId_ubigeo`, `departamento_nombre`, `pais_id`) VALUES
(1, '01', 'Amazonas', 1),
(2, '02', 'Áncash', 1),
(3, '03', 'Apurímac', 1),
(4, '04', 'Arequipa', 1),
(5, '05', 'Ayacucho', 1),
(6, '06', 'Cajamarca', 1),
(7, '07', 'Callao', 1),
(8, '08', 'Cusco', 1),
(9, '09', 'Huancavelica', 1),
(10, '10', 'Huánuco', 1),
(11, '11', 'Ica', 1),
(12, '12', 'Junín', 1),
(13, '13', 'La Libertad', 1),
(14, '14', 'Lambayeque', 1),
(15, '15', 'Lima', 1),
(16, '16', 'Loreto', 1),
(17, '17', 'Madre de Dios', 1),
(18, '18', 'Moquegua', 1),
(19, '19', 'Pasco', 1),
(20, '20', 'Piura', 1),
(21, '21', 'Puno', 1),
(22, '22', 'San Martín', 1),
(23, '23', 'Tacna', 1),
(24, '24', 'Tumbes', 1),
(25, '25', 'Ucayali', 1);


ALTER TABLE `departamento`
  ADD PRIMARY KEY (`departamento_id`),
  ADD KEY `estado_fk_1_idx` (`pais_id`);



ALTER TABLE `departamento`
  MODIFY `departamento_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

