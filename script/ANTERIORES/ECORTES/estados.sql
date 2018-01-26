-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 28, 2016 at 01:32 AM
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
-- Table structure for table `estados`
--
DROP TABLE IF EXISTS `distrito`;


CREATE TABLE `estados` (
  `estados_id` bigint(20) NOT NULL,
  `subId_ubigeo` varchar(2) NOT NULL,
  `estados_nombre` varchar(45) DEFAULT NULL,
  `pais_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `estados`
--

INSERT INTO `estados` (`estados_id`, `subId_ubigeo`, `estados_nombre`, `pais_id`) VALUES
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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`estados_id`),
  ADD KEY `estado_fk_1_idx` (`pais_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `estados`
--
ALTER TABLE `estados`
  MODIFY `estados_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
