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
  PRIMARY KEY (`id_qr`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `codigos_qr` */

insert  into `codigos_qr`(`id_qr`,`codigo`,`fecha_creacion`,`activo`) values (1,'http://localhost/login/vistas/mascotas/perfil.php?id=72','2025-10-14 21:50:33',1);

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `fotos_mascotas` */

insert  into `fotos_mascotas`(`id_foto`,`id_mascota`,`url`,`descripcion`,`fecha_subida`) values (1,72,'assets/images/mascotas/mascota_1760488784_819b820b.jfif',NULL,'2025-10-14 21:39:51');

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
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `perdido` tinyint(1) DEFAULT 0,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_mascota`),
  UNIQUE KEY `id_qr` (`id_qr`),
  KEY `id_dueño` (`id`),
  CONSTRAINT `mascotas_ibfk_1` FOREIGN KEY (`id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `mascotas_ibfk_2` FOREIGN KEY (`id_qr`) REFERENCES `codigos_qr` (`id_qr`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `mascotas` */

insert  into `mascotas`(`id_mascota`,`nombre`,`especie`,`raza`,`edad`,`sexo`,`color`,`id`,`id_qr`,`foto_url`,`fecha_registro`,`perdido`,`descripcion`) values (72,'botitas','gato',NULL,4,NULL,NULL,13,1,'assets/images/mascotas/mascota_1760488784_819b820b.jfif','2025-10-25 23:10:52',0,NULL),(73,'lona','Perro','border coli',1,'Hembra','blanco y negro',15,NULL,'assets/images/mascotas/mascota_1761606238_68fffa5ed0e6f.jpg','2025-10-27 19:39:25',0,NULL),(74,'simba','gato','gris',4,'macho',NULL,15,NULL,'assets/images/mascotas/mascota_15_1761699453.jpg','2025-10-28 21:57:33',0,NULL),(75,'luna','gato',NULL,2,'macho','gris',15,NULL,'assets/images/mascotas/mascota_15_1761700197.jpg','2025-10-28 22:09:57',0,NULL),(76,'nala','Gato','calico',4,'Hembra','vaios',16,NULL,'assets/images/mascotas/mascota_1761865256_6903ee28a205e.jpg','2025-10-30 20:00:56',0,NULL),(77,'nala','Gato','calico',4,'Hembra','varios',16,NULL,'assets/images/mascotas/mascota_1761919451_6904c1dbbd1ef.jpg','2025-10-31 11:04:11',0,NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

/*Data for the table `usuarios` */

insert  into `usuarios`(`id`,`nombre`,`apellido`,`telefono`,`email`,`direccion`,`password`,`fecha_creacion`,`foto_url`) values (13,'cristian','merlo',NULL,'cristian@gmail.com',NULL,'$2y$10$qe2TzM3GqXQgI36ylqYyRumGZzUmylPh5/e1PVOR.a4xqFzRr2Hp2','2025-10-14 21:37:33','http://localhost/login/assets/images/usuarios/user_1760488652_2ebf6e17.png'),(14,'Test','User',NULL,'test@test.com',NULL,'\\.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2025-10-23 19:59:05',NULL),(15,'emanuel','merlo','3417208555','emanuel@gmail.com','','$2y$10$qD4TTCo.c5w6/1H7KrIowu1jNjXwu4gdhLamDKxSwVdxi9YjqW7Q6','2025-10-23 20:05:59','assets/images/usuarios/usuario_15_1761699153.jpeg'),(16,'Emanuel','Merlo','03413613207','emanuel1@gmail.com','rosario','$2y$10$XF3z8hAI16va1j59hWfycublHyTft5f85jLUcQ8ZPKKZlALAVhbrK','2025-10-29 21:32:44','assets/usuarios/usuario_1761864754_6903ec32d8fd9.jpg');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
