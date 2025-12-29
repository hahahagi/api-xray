-- --------------------------------------------------------
-- Host:                         bagipanen.my.id
-- Server version:               10.6.23-MariaDB-cll-lve-log - MariaDB Server
-- Server OS:                    Linux
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for kmiprodm_aioa_xray
DROP DATABASE IF EXISTS `kmiprodm_aioa_xray`;
CREATE DATABASE IF NOT EXISTS `kmiprodm_aioa_xray` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */;
USE `kmiprodm_aioa_xray`;

-- Dumping structure for table kmiprodm_aioa_xray.tb_control
DROP TABLE IF EXISTS `tb_control`;
CREATE TABLE IF NOT EXISTS `tb_control` (
  `id` int(11) NOT NULL,
  `target_status` varchar(5) DEFAULT 'ON',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table kmiprodm_aioa_xray.tb_machine_log
DROP TABLE IF EXISTS `tb_machine_log`;
CREATE TABLE IF NOT EXISTS `tb_machine_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(5) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=154 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table kmiprodm_aioa_xray.tb_performance
DROP TABLE IF EXISTS `tb_performance`;
CREATE TABLE IF NOT EXISTS `tb_performance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shift_date` date DEFAULT NULL,
  `total_ban_check` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table kmiprodm_aioa_xray.tb_quality
DROP TABLE IF EXISTS `tb_quality`;
CREATE TABLE IF NOT EXISTS `tb_quality` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shift_date` date DEFAULT NULL,
  `jumlah_ok` int(11) DEFAULT 0,
  `jumlah_ng` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table kmiprodm_aioa_xray.tb_xray_log
DROP TABLE IF EXISTS `tb_xray_log`;
CREATE TABLE IF NOT EXISTS `tb_xray_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `result` varchar(5) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
