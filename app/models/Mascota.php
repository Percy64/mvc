<?php

namespace app\models;

use Model;
use DataBase;

class Mascota extends Model {
    protected $table = 'mascotas';
    protected $primaryKey = 'id_mascota';
    
    public $id_mascota;
    public $nombre;
    public $especie;
    public $raza;
    public $edad;
    public $descripcion; // No existe en BASE.sql; solo usada en vistas como opcional
    public $foto_url;
    public $fecha_registro; // No existe en BASE.sql; mantenida para compatibilidad de vistas
    public $usuario_id; // Se mapea a la columna `id` (dueño) en BASE.sql
    public $sexo; // Existe en BASE.sql
    public $color; // Existe en BASE.sql
    
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->fill($data);
        }
    }
    
    /**
     * Llenar el modelo con datos
     */
    public function fill($data) {
        // Preservar valores actuales si la clave no viene en $data (especialmente id_mascota)
        $this->id_mascota = array_key_exists('id_mascota', $data) ? $data['id_mascota'] : $this->id_mascota;
        $this->nombre = array_key_exists('nombre', $data) ? $data['nombre'] : $this->nombre;
        $this->especie = array_key_exists('especie', $data) ? $data['especie'] : $this->especie;
        $this->raza = array_key_exists('raza', $data) ? $data['raza'] : $this->raza;
        $this->edad = array_key_exists('edad', $data) ? $data['edad'] : $this->edad;
        // Normalizar descripción: recortar y limitar a 255 caracteres
        if (array_key_exists('descripcion', $data)) {
            $desc = is_string($data['descripcion']) ? trim($data['descripcion']) : '';
            if (function_exists('mb_substr')) {
                $desc = mb_substr($desc, 0, 255, 'UTF-8');
            } else {
                $desc = substr($desc, 0, 255);
            }
            $this->descripcion = $desc;
        }
        $this->foto_url = array_key_exists('foto_url', $data) ? $data['foto_url'] : $this->foto_url;
        $this->fecha_registro = array_key_exists('fecha_registro', $data) ? $data['fecha_registro'] : $this->fecha_registro;
        // En BASE.sql el dueño se almacena en la columna `id`. Usar usuario_id si viene,
        // en su defecto 'id' (pero solo si no se proporcionó usuario_id), sino preservar el actual.
        if (array_key_exists('usuario_id', $data)) {
            $this->usuario_id = $data['usuario_id'];
        } elseif (array_key_exists('id', $data)) {
            $this->usuario_id = $data['id'];
        }
        $this->sexo = array_key_exists('sexo', $data) ? $data['sexo'] : $this->sexo;
        $this->color = array_key_exists('color', $data) ? $data['color'] : $this->color;
    }
    
    /**
     * Validar datos de la mascota
     */
    public function validate($data) {
        $errores = [];
        
        // Validar nombre
        if (empty($data['nombre']) || strlen(trim($data['nombre'])) < 2) {
            $errores[] = 'El nombre debe tener al menos 2 caracteres.';
        }
        
        // Validar especie
        if (empty($data['especie'])) {
            $errores[] = 'La especie es obligatoria.';
        }
        
        // Validar edad (si se proporciona)
        if (!empty($data['edad']) && !is_numeric($data['edad'])) {
            $errores[] = 'La edad debe ser un número.';
        }
        
        return $errores;
    }
    
    /**
     * Buscar mascota por ID
     */
    public static function findById($id) {
        $sql = "SELECT * FROM mascotas WHERE id_mascota = ?";
        return DataBase::getRecord($sql, [$id]);
    }
    
    /**
     * Obtener todas las mascotas
     */
    public static function getAll($limit = null) {
        // BASE.sql no tiene fecha_registro; ordenar por id_mascota DESC
        $sql = "SELECT * FROM mascotas ORDER BY id_mascota DESC";
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        return DataBase::getRecords($sql);
    }
    
    /**
     * Obtener mascotas con foto
     */
    public static function getAllWithPhoto($limit = null) {
        $sql = "SELECT * FROM mascotas WHERE foto_url IS NOT NULL AND foto_url != '' ORDER BY id_mascota DESC";
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        return DataBase::getRecords($sql);
    }
    
    /**
     * Guardar mascota
     */
    public function save() {
        $edadVal = (isset($this->edad) && $this->edad !== '' && $this->edad !== null) ? $this->edad : null;
        if ($this->id_mascota) {
            // Actualizar
            $sql = "UPDATE mascotas SET nombre = ?, especie = ?, raza = ?, edad = ?, sexo = ?, color = ?, descripcion = ?, foto_url = ? WHERE id_mascota = ?";
            $descVal = ($this->descripcion !== null && $this->descripcion !== '') ? $this->descripcion : null;
            $params = [
                $this->nombre,
                $this->especie,
                $this->raza,
                $edadVal,
                $this->sexo,
                $this->color,
                $descVal,
                $this->foto_url,
                $this->id_mascota
            ];
            return DataBase::execute($sql, $params) > 0;
        } else {
            // Insertar (mapear usuario_id a columna `id`)
            $sql = "INSERT INTO mascotas (nombre, especie, raza, edad, sexo, color, descripcion, id, foto_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $descVal = ($this->descripcion !== null && $this->descripcion !== '') ? $this->descripcion : null;
            $params = [
                $this->nombre,
                $this->especie,
                $this->raza,
                $edadVal,
                $this->sexo,
                $this->color,
                $descVal,
                $this->usuario_id,
                $this->foto_url
            ];
            $result = DataBase::execute($sql, $params);
            if ($result > 0) {
                $this->id_mascota = DataBase::obtenerUltimoId();
                return true;
            }
        }
        return false;
    }
    
    /**
     * Actualizar foto
     */
    public function updatePhoto($photoUrl) {
        if (!$this->id_mascota) return false;
        
        $sql = "UPDATE mascotas SET foto_url = ? WHERE id_mascota = ?";
        $result = DataBase::execute($sql, [$photoUrl, $this->id_mascota]);
        
        if ($result > 0) {
            $this->foto_url = $photoUrl;
            return true;
        }
        return false;
    }
    
    /**
     * Obtener mascotas por usuario
     */
    public static function getByUserId($userId) {
        $sql = "SELECT * FROM mascotas WHERE id = ? ORDER BY id_mascota DESC";
        return DataBase::getRecords($sql, [$userId]);
    }
    
    /**
     * Eliminar mascota por ID
     */
    public static function deleteById($id) {
        $sql = "DELETE FROM mascotas WHERE id_mascota = ?";
        return DataBase::execute($sql, [$id]) > 0;
    }
    
    /**
     * Convertir a array
     */
    public function toArray() {
        return [
            'id_mascota' => $this->id_mascota,
            'nombre' => $this->nombre,
            'especie' => $this->especie,
            'raza' => $this->raza,
            'edad' => $this->edad,
            'descripcion' => $this->descripcion,
            'foto_url' => $this->foto_url,
            'fecha_registro' => $this->fecha_registro,
            'usuario_id' => $this->usuario_id,
            'sexo' => $this->sexo,
            'color' => $this->color
        ];
    }
    
    /**
     * Marcar mascota como perdida o encontrada
     */
    public static function marcarComoPerdida($id, $perdida = true) {
        $sql = "UPDATE mascotas SET perdido = ? WHERE id_mascota = ?";
        return DataBase::execute($sql, [$perdida ? 1 : 0, $id]) > 0;
    }
    
    /**
     * Verificar si una mascota está perdida
     */
    public static function estaPerdida($id) {
        $sql = "SELECT perdido FROM mascotas WHERE id_mascota = ?";
        $result = DataBase::getRecord($sql, [$id]);
        return $result ? (bool)$result['perdido'] : false;
    }
    
    /**
     * Obtener todas las mascotas perdidas
     */
    public static function getMascotasPerdidas($limit = null) {
        $sql = "SELECT * FROM mascotas WHERE perdido = 1 ORDER BY id_mascota DESC";
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        return DataBase::getRecords($sql);
    }

    /**
     * Agregar una foto a la galería de una mascota
     */
    public static function addGalleryPhoto($id_mascota, $url, $descripcion = null) {
        // Crear tabla si no existe (defensivo)
        $create = "CREATE TABLE IF NOT EXISTS fotos_mascotas (
            id_foto INT AUTO_INCREMENT PRIMARY KEY,
            id_mascota INT NOT NULL,
            url VARCHAR(255) NOT NULL,
            descripcion VARCHAR(255) NULL,
            fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_fotos_mascotas_mascota FOREIGN KEY (id_mascota) REFERENCES mascotas(id_mascota) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        try { DataBase::execute($create); } catch (\Throwable $e) { /* ignore */ }

        $sql = "INSERT INTO fotos_mascotas (id_mascota, url, descripcion) VALUES (?, ?, ?)";
        $descVal = ($descripcion !== null && $descripcion !== '') ? $descripcion : null;
        return DataBase::execute($sql, [$id_mascota, $url, $descVal]) > 0;
    }
    
    /**
     * Registrar reporte de mascota encontrada por otro usuario
     */
    public static function registrarReporteEncontrada($id_mascota, $usuario_reporta = null) {
        // Crear tabla de reportes si no existe
        $createTable = "CREATE TABLE IF NOT EXISTS reportes_encontradas (
            id_reporte INT AUTO_INCREMENT PRIMARY KEY,
            id_mascota INT NOT NULL,
            usuario_reporta INT NULL,
            fecha_reporte TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            ip_reporte VARCHAR(45),
            procesado BOOLEAN DEFAULT FALSE,
            FOREIGN KEY (id_mascota) REFERENCES mascotas(id_mascota) ON DELETE CASCADE,
            FOREIGN KEY (usuario_reporta) REFERENCES usuarios(id) ON DELETE SET NULL
        )";
        
        try {
            DataBase::execute($createTable);
        } catch (\Exception $e) {
            // La tabla ya existe o hay otro error, continuar
        }
        
        // Insertar el reporte
        $sql = "INSERT INTO reportes_encontradas (id_mascota, usuario_reporta, ip_reporte) VALUES (?, ?, ?)";
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $result = DataBase::execute($sql, [$id_mascota, $usuario_reporta, $ip]);
        
        if ($result > 0) {
            return DataBase::obtenerUltimoId();
        }
        return false;
    }
    
    /**
     * Obtener reportes de mascotas encontradas
     */
    public static function getReportesEncontradas($id_mascota) {
        $sql = "SELECT r.*, u.nombre, u.email 
                FROM reportes_encontradas r 
                LEFT JOIN usuarios u ON r.usuario_reporta = u.id 
                WHERE r.id_mascota = ? 
                ORDER BY r.fecha_reporte DESC";
        return DataBase::getRecords($sql, [$id_mascota]);
    }
}