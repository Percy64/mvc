-- Migración para asegurar que el sistema de mascotas perdidas funcione correctamente
-- Ejecutar este archivo en la base de datos mascotas_db

USE mascotas_db;

-- Asegurar que la columna perdidos existe con el tipo correcto
ALTER TABLE mascotas 
MODIFY COLUMN perdidos BOOLEAN DEFAULT FALSE;

-- Crear tabla para reportes de mascotas encontradas (si no existe)
CREATE TABLE IF NOT EXISTS reportes_encontradas (
    id_reporte INT AUTO_INCREMENT PRIMARY KEY,
    id_mascota INT NOT NULL,
    usuario_reporta INT NULL,
    fecha_reporte TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_reporte VARCHAR(45),
    procesado BOOLEAN DEFAULT FALSE,
    notas TEXT NULL,
    INDEX idx_mascota (id_mascota),
    INDEX idx_fecha (fecha_reporte),
    FOREIGN KEY (id_mascota) REFERENCES mascotas(id_mascota) ON DELETE CASCADE,
    FOREIGN KEY (usuario_reporta) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Actualizar mascotas existentes para asegurar que tienen valor por defecto
UPDATE mascotas SET perdidos = FALSE WHERE perdidos IS NULL;

SELECT 'Migración completada correctamente' AS mensaje;