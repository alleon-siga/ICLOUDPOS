-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 28, 2016 at 01:23 AM
-- Server version: 5.7.13-0ubuntu0.16.04.2
-- PHP Version: 7.0.9-1+deb.sury.org~xenial+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bdinventario_newlevel`
--

-- --------------------------------------------------------

--
-- Table structure for table `ciudades`
--

DROP TABLE IF EXISTS `ciudades`;

CREATE TABLE `ciudades` (
  `ciudad_id` bigint(20) NOT NULL,
  `subId_ubigeo` varchar(11) NOT NULL,
  `ciudad_nombre` varchar(45) DEFAULT NULL,
  `estado_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ciudades`
--

INSERT INTO `ciudades` (`ciudad_id`, `subId_ubigeo`, `ciudad_nombre`, `estado_id`) VALUES
(1,  'Chachapoyas', 1),
(2,  'Bagua', 1),
(3,  'Bongará', 1),
(4,  'Condorcanqui', 1),
(5,  'Luya', 1),
(6,  'Rodríguez de Mendoza', 1),
(7, 'Utcubamba', 1),
(8,  'Huaraz', 2),
(9,  'Aija', 2),
(10,  'Antonio Raymondi', 2),
(11,  'Asunción', 2),
(12,  'Bolognesi', 2),
(13,  'Carhuaz', 2),
(14,  'Carlos Fermín Fitzcarrald', 2),
(15,  'Casma', 2),
(16,  'Corongo', 2),
(17,  'Huari', 2),
(18,  'Huarmey', 2),
(19,  'Huaylas', 2),
(20,  'Mariscal Luzuriaga', 2),
(21,  'Ocros', 2),
(22,  'Pallasca', 2),
(23,  'Pomabamba', 2),
(24,  'Recuay', 2),
(25,  'Santa', 2),
(26,  'Sihuas', 2),
(27,  'Yungay', 2),
(28,  'Abancay', 3),
(29,  'Andahuaylas', 3),
(30,  'Antabamba', 3),
(31,  'Aymaraes', 3),
(32,  'Cotabambas', 3),
(33,  'Chincheros', 3),
(34,  'Grau', 3),
(35,  'Arequipa', 4),
(36,  'Camaná', 4),
(37,  'Caravelí', 4),
(38,  'Castilla', 4),
(39,  'Caylloma', 4),
(40,  'Condesuyos', 4),
(41, 'Islay', 4),
(42,  'La Uniòn', 4),
(43, 'Huamanga', 5),
(44, 'Cangallo', 5),
(45,  'Huanca Sancos', 5),
(46,  'Huanta', 5),
(47,  'La Mar', 5),
(48,  'Lucanas', 5),
(49,  'Parinacochas', 5),
(50,  'Pàucar del Sara Sara ', 5),
(51,  'Sucre', 5),
(52,  'Víctor Fajardo', 5),
(53,  'Vilcas Huamán', 5),
(54,  'Cajamarca', 6),
(55,  'Cajabamba', 6),
(56,  'Celendín', 6),
(57,  'Chota', 6),
(58,  'Contumazá', 6),
(59,  'Cutervo', 6),
(60,  'Hualgayoc', 6),
(61,  'Jaén', 6),
(62,  'San Ignacio', 6),
(63,  'San Marcos', 6),
(64,  'San Miguel', 6),
(65,  'San Pablo', 6),
(66,  'Santa Cruz', 6),
(67,  'Prov. Const. del Callao', 7),
(68,  'Cusco', 8),
(69,  'Acomayo', 8),
(70,  'Anta', 8),
(71,  'Calca', 8),
(72,  'Canas', 8),
(73,  'Canchis', 8),
(74,  'Chumbivilcas', 8),
(75,  'Espinar', 8),
(76,  'La Convención', 8),
(77,  'Paruro', 8),
(78,  'Paucartambo', 8),
(79,  'Quispicanchi', 8),
(80,  'Urubamba', 8),
(81,  'Huancavelica', 9),
(82,  'Acobamba', 9),
(83,  'Angaraes', 9),
(84,  'Castrovirreyna', 9),
(85, 'Churcampa', 9),
(86,  'Huaytará', 9),
(87,  'Tayacaja', 9),
(88,  'Huánuco', 10),
(89,  'Ambo', 10),
(90,  'Dos de Mayo', 10),
(91,  'Huacaybamba', 10),
(92,  'Huamalíes', 10),
(93,  'Leoncio Prado', 10),
(94,  'Marañón', 10),
(95,  'Pachitea', 10),
(96,  'Puerto Inca', 10),
(97,  'Lauricocha', 10),
(98,  'Yarowilca', 10),
(99,  'Ica', 11),
(100,  'Chincha', 11),
(101,  'Nazca', 11),
(102,  'Palpa', 11),
(103,  'Pisco', 11),
(104,  'Huancayo', 12),
(105,  'Concepción', 12),
(106,  'Chanchamayo', 12),
(107,  'Jauja', 12),
(108,  'Junín', 12),
(109,  'Satipo', 12),
(110,  'Tarma', 12),
(111,  'Yauli', 12),
(112,  'Chupaca', 12),
(113,  'Trujillo', 13),
(114,  'Ascope', 13),
(115,  'Bolívar', 13),
(116,  'Chepén', 13),
(117,  'Julcán', 13),
(118,  'Otuzco', 13),
(119,  'Pacasmayo', 13),
(120,  'Pataz', 13),
(121,  'Sánchez Carrión', 13),
(122,  'Santiago de Chuco', 13),
(123,  'Gran Chimú', 13),
(124,  'Virú', 13),
(125,  'Chiclayo', 14),
(126,  'Ferreñafe', 14),
(127,  'Lambayeque', 14),
(128,  'Lima', 15),
(129,  'Barranca', 15),
(130,  'Cajatambo', 15),
(131,  'Canta', 15),
(132,  'Cañete', 15),
(133,  'Huaral', 15),
(134,  'Huarochirí', 15),
(135,  'Huaura', 15),
(136,  'Oyón', 15),
(137,  'Yauyos', 15),
(138,  'Maynas', 16),
(139,  'Alto Amazonas', 16),
(140,  'Loreto', 16),
(141,  'Mariscal Ramón Castilla', 16),
(142,  'Requena', 16),
(143,  'Ucayali', 16),
(144,  'Datem del Marañón', 16),
(145,  'Putumayo', 16),
(146,  'Tambopata', 17),
(147,  'Manu', 17),
(148,  'Tahuamanu', 17),
(149,  'Mariscal Nieto', 18),
(150,  'General Sánchez Cerro', 18),
(151,  'Ilo', 18),
(152,  'Pasco', 19),
(153,  'Daniel Alcides Carrión', 19),
(154,  'Oxapampa', 19),
(155,  'Piura', 20),
(156,  'Ayabaca', 20),
(157,  'Huancabamba', 20),
(158,  'Morropón', 20),
(159,  'Paita', 20),
(160,  'Sullana', 20),
(161,  'Talara', 20),
(162,  'Sechura', 20),
(163,  'Puno', 21),
(164,  'Azángaro', 21),
(165,  'Carabaya', 21),
(166,  'Chucuito', 21),
(167,  'El Collao', 21),
(168,  'Huancané', 21),
(169,  'Lampa', 21),
(170,  'Melgar', 21),
(171,  'Moho', 21),
(172,  'San Antonio de Putina ', 21),
(173,  'San Román', 21),
(174,  'Sandia', 21),
(175,  'Yunguyo', 21),
(176,  'Moyobamba', 22),
(177,  'Bellavista', 22),
(178,  'El Dorado', 22),
(179,  'Huallaga', 22),
(180,  'Lamas', 22),
(181,  'Mariscal Cáceres', 22),
(182,  'Picota', 22),
(183,  'Rioja', 22),
(184,  'San Martín', 22),
(185,  'Tocache', 22),
(186,  'Tacna', 23),
(187,  'Candarave', 23),
(188,  'Jorge Basadre', 23),
(189,  'Tarata', 23),
(190,  'Tumbes', 24),
(191,  'Contralmirante Villar', 24),
(192,  'Zarumilla', 24),
(193,  'Coronel Portillo', 25),
(194,  'Atalaya', 25),
(195,  'Padre Abad', 25),
(196,  'Purús', 25);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ciudades`
--
ALTER TABLE `ciudades`
  ADD PRIMARY KEY (`ciudad_id`),
  ADD KEY `ciudad_pk_1_idx` (`estado_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ciudades`
--
ALTER TABLE `ciudades`
  MODIFY `ciudad_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=197;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
