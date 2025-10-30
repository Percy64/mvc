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
        $this->descripcion = array_key_exists('descripcion', $data) ? $data['descripcion'] : $this->descripcion;
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
            $sql = "UPDATE mascotas SET nombre = ?, especie = ?, raza = ?, edad = ?, sexo = ?, color = ?, foto_url = ? WHERE id_mascota = ?";
            $params = [
                $this->nombre,
                $this->especie,
                $this->raza,
                $edadVal,
                $this->sexo,
                $this->color,
                $this->foto_url,
                $this->id_mascota
            ];
            return DataBase::execute($sql, $params) > 0;
        } else {
            // Insertar (mapear usuario_id a columna `id`)
            $sql = "INSERT INTO mascotas (nombre, especie, raza, edad, sexo, color, id, foto_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [
                $this->nombre,
                $this->especie,
                $this->raza,
                $edadVal,
                $this->sexo,
                $this->color,
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
}