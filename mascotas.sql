/*
SQLyog Ultimate v12.09 (32 bit)
MySQL - 10.4.32-MariaDB : Database - mascotas_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`mascotas_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `mascotas_db`;

/*Table structure for table `codigos_qr` */

DROP TABLE IF EXISTS `codigos_qr`;

CREATE TABLE `codigos_qr` (
  `id_qr` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1,
  `url_qr` varchar(255) DEFAULT NULL,
  `ruta_imagen` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_qr`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `codigos_qr` */

insert  into `codigos_qr`(`id_qr`,`codigo`,`fecha_creacion`,`activo`,`url_qr`,`ruta_imagen`) values (51,'QR-76-690183d657858','2025-10-29 00:02:46',1,'http://localhost/lost-found/perfil_mascota.php?id=76','assets/images/qr/qr_mascota_76_1761706966.png');

/*Table structure for table `fotos_mascotas` */

DROP TABLE IF EXISTS `fotos_mascotas`;

CREATE TABLE `fotos_mascotas` (
  `id_foto` int(11) NOT NULL AUTO_INCREMENT,
  `id_mascota` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_foto`),
  KEY `id_mascota` (`id_mascota`),
  CONSTRAINT `fotos_mascotas_ibfk_1` FOREIGN KEY (`id_mascota`) REFERENCES `mascotas` (`id_mascota`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `fotos_mascotas` */

/*Table structure for table `historial_medico` */

DROP TABLE IF EXISTS `historial_medico`;

CREATE TABLE `historial_medico` (
  `id_historial` int(11) NOT NULL AUTO_INCREMENT,
  `id_mascota` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` text DEFAULT NULL,
  `veterinario` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_historial`),
  KEY `id_mascota` (`id_mascota`),
  CONSTRAINT `historial_medico_ibfk_1` FOREIGN KEY (`id_mascota`) REFERENCES `mascotas` (`id_mascota`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `historial_medico` */

/*Table structure for table `mascotas` */

DROP TABLE IF EXISTS `mascotas`;

CREATE TABLE `mascotas` (
  `id_mascota` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `especie` varchar(50) NOT NULL,
  `raza` varchar(100) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `sexo` varchar(10) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `id` int(11) NOT NULL,
  `id_qr` int(11) DEFAULT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_mascota`),
  UNIQUE KEY `id_qr` (`id_qr`),
  KEY `id_dueño` (`id`),
  CONSTRAINT `mascotas_ibfk_1` FOREIGN KEY (`id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `mascotas_ibfk_2` FOREIGN KEY (`id_qr`) REFERENCES `codigos_qr` (`id_qr`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `mascotas` */

insert  into `mascotas`(`id_mascota`,`nombre`,`especie`,`raza`,`edad`,`sexo`,`color`,`id`,`id_qr`,`foto_url`,`fecha_registro`) values (76,'botitas','gato','carey',5,'hembra','blanco y negro',1,51,'assets/images/mascotas/mascota_1_1761706789.jpg','2025-10-28 23:59:49');

/*Table structure for table `usuarios` */

DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `apellido` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `direccion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*Data for the table `usuarios` */

insert  into `usuarios`(`id`,`nombre`,`apellido`,`telefono`,`email`,`direccion`,`password`,`fecha_creacion`,`foto_url`) values (1,'Emanuel','Merlo','03413613207','emanuelmerlo15@gmail.com','PASCO 123','$2y$10$xchjtvrAZrfQLyl5JJh3Benh90h7S5pM9/51hVdmoLXsU8tMVSh4G','2025-10-10 02:29:26','assets/images/usuarios/usuario_1_1761704969.jpeg'),(2,'Juan','Pérez','341-5551234','juan.perez@example.com','Calle Falsa 123, Rosario','$2b$10$hashDeEjemplo1234567890','2025-10-10 02:49:46',NULL),(3,'Juan','Pérez','341-5551234','juan.perez@example.com','Calle Falsa 123, Rosario','$2b$10$hashEjemplo1','2025-10-10 04:12:30',NULL),(4,'María','Gómez','341-5555678','maria.gomez@example.com','Av. Libertad 456, Rosario','$2b$10$hashEjemplo2','2025-10-10 04:12:30',NULL),(5,'Carlos','López','341-5559876','carlos.lopez@example.com','San Martín 789, Rosario','$2b$10$hashEjemplo3','2025-10-10 04:12:30',NULL),(6,'Lucía','Fernández','341-5554321','lucia.fernandez@example.com','Belgrano 321, Rosario','$2b$10$hashEjemplo4','2025-10-10 04:12:30',NULL),(7,'Pedro','Martínez','341-5552468','pedro.martinez@example.com','Mitre 654, Rosario','$2b$10$hashEjemplo5','2025-10-10 04:12:30',NULL),(13,'brisa','merlo',NULL,'brisa@gmail.com',NULL,'$2y$10$YNkCLndmY/oc6oZ6cUB75Ol10WEy3QpEy4ieResFEj2ArBcMnz50K','2025-10-11 21:10:56','http://localhost/login/assets/images/usuarios/user_1760227856_68df8210.jpg');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
