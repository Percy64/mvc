-- Migración para agregar soporte de mascotas perdidas
-- Ejecutar en la base de datos mascotas_db

USE mascotas_db;

-- Agregar la columna perdidos si no existe
ALTER TABLE mascotas 
ADD COLUMN perdidos BOOLEAN DEFAULT FALSE AFTER fecha_registro;

-- Actualizar mascotas existentes
UPDATE mascotas SET perdidos = FALSE WHERE perdidos IS NULL;

-- Crear tabla para reportes de mascotas encontradas
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

-- Verificar que todo esté correcto
SELECT 'Migración completada - Columna perdidos agregada' AS resultado;
DESCRIBE mascotas;