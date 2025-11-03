-- Migration: add lost-location fields and reportes_encontradas table
-- Safe to run multiple times; uses IF NOT EXISTS where supported

USE `mascotas_db`;

-- Add columns to mascotas (MariaDB 10.4 supports IF NOT EXISTS on columns)
ALTER TABLE `mascotas`
  ADD COLUMN IF NOT EXISTS `ultima_ubicacion` VARCHAR(255) NULL AFTER `perdido`,
  ADD COLUMN IF NOT EXISTS `ultima_lat` DECIMAL(10,7) NULL AFTER `ultima_ubicacion`,
  ADD COLUMN IF NOT EXISTS `ultima_lng` DECIMAL(10,7) NULL AFTER `ultima_lat`;

-- Create index for coords (MariaDB <10.5 may not support IF NOT EXISTS on indexes)
-- Attempt to create; ignore error if already exists
CREATE INDEX `idx_ultima_coords` ON `mascotas` (`ultima_lat`,`ultima_lng`);

-- Create reportes_encontradas table if not exists
CREATE TABLE IF NOT EXISTS `reportes_encontradas` (
  `id_reporte` int(11) NOT NULL AUTO_INCREMENT,
  `id_mascota` int(11) NOT NULL,
  `usuario_reporta` int(11) DEFAULT NULL,
  `fecha_reporte` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_reporte` varchar(45) DEFAULT NULL,
  `procesado` tinyint(1) DEFAULT 0,
  `ubicacion` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `contacto` varchar(255) DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  PRIMARY KEY (`id_reporte`),
  KEY `id_mascota` (`id_mascota`),
  KEY `idx_coords` (`lat`,`lng`),
  CONSTRAINT `reportes_encontradas_ibfk_1` FOREIGN KEY (`id_mascota`) REFERENCES `mascotas` (`id_mascota`) ON DELETE CASCADE,
  CONSTRAINT `reportes_encontradas_ibfk_2` FOREIGN KEY (`usuario_reporta`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
