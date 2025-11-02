<?php

namespace app\models;

use Model;
use DataBase;

class Usuario extends Model {
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    
    public $id;
    public $nombre;
    public $apellido;
    public $telefono;
    public $email;
    public $direccion;
    public $password;
    public $fecha_creacion;
    public $foto_url;
    
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->fill($data);
        }
    }
    
    /**
     * Llenar el modelo con datos
     */
    public function fill($data) {
        // Preservar valores actuales si la clave no viene en $data (evita limpiar campos en edición)
        $this->id = array_key_exists('id', $data) ? $data['id'] : $this->id;
        $this->nombre = array_key_exists('nombre', $data) ? $data['nombre'] : $this->nombre;
        $this->apellido = array_key_exists('apellido', $data) ? $data['apellido'] : $this->apellido;
        $this->telefono = array_key_exists('telefono', $data) ? $data['telefono'] : $this->telefono;
        $this->email = array_key_exists('email', $data) ? $data['email'] : $this->email;
        $this->direccion = array_key_exists('direccion', $data) ? $data['direccion'] : $this->direccion;
        $this->password = array_key_exists('password', $data) ? $data['password'] : $this->password;
        $this->fecha_creacion = array_key_exists('fecha_creacion', $data) ? $data['fecha_creacion'] : $this->fecha_creacion;
        $this->foto_url = array_key_exists('foto_url', $data) ? $data['foto_url'] : $this->foto_url;
    }
    
    /**
     * Formatear número de WhatsApp para URL
     */
    public static function formatWhatsAppNumber($number) {
        if (empty($number)) return null;
        
        // Remover espacios, guiones y otros caracteres
        $number = preg_replace('/[^0-9+]/', '', $number);
        
        // Si no tiene código de país, agregar +54 (Argentina)
        if (!str_starts_with($number, '+')) {
            if (str_starts_with($number, '54')) {
                $number = '+' . $number;
            } else {
                $number = '+54' . $number;
            }
        }
        
        return $number;
    }
    
    /**
     * Generar URL de WhatsApp usando el teléfono
     */
    public function getWhatsAppUrl($message = '') {
        if (empty($this->telefono)) return null;
        
        $number = self::formatWhatsAppNumber($this->telefono);
        $encodedMessage = urlencode($message);
        
        return "https://wa.me/{$number}?text={$encodedMessage}";
    }
    
    /**
     * Validar datos del usuario
     */
    public function validate($data, $isUpdate = false) {
        $errores = [];
        
        // Validar nombre
        if (empty($data['nombre']) || strlen(trim($data['nombre'])) < 2) {
            $errores[] = 'El nombre debe tener al menos 2 caracteres.';
        }
        
        // Validar apellido
        if (empty($data['apellido']) || strlen(trim($data['apellido'])) < 2) {
            $errores[] = 'El apellido debe tener al menos 2 caracteres.';
        }
        
        // Validar teléfono (que también será usado para WhatsApp)
        if (empty($data['telefono']) || strlen(trim($data['telefono'])) < 8) {
            $errores[] = 'El número de teléfono es obligatorio y debe tener al menos 8 dígitos.';
        } elseif (!preg_match('/^[\+]?[0-9\s\-\(\)]+$/', trim($data['telefono']))) {
            $errores[] = 'El número de teléfono debe contener solo números, espacios, guiones y el símbolo +.';
        }
        
        // Validar password (solo al crear)
        if (!$isUpdate && (empty($data['password']) || strlen($data['password']) < 6)) {
            $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
        }
        
        return $errores;
    }
    
    /**
     * Autenticar usuario
     */
    public static function authenticate($email, $password) {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $usuario = DataBase::getRecord($sql, [$email]);
        
        if ($usuario && password_verify($password, $usuario['password'])) {
            return $usuario;
        }
        
        return false;
    }
    
    /**
     * Buscar usuario por ID
     */
    public static function findById($id) {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        return DataBase::getRecord($sql, [$id]);
    }
    
    /**
     * Obtener todos los usuarios
     */
    public static function getAll() {
        $sql = "SELECT id, nombre, apellido, email, telefono, direccion, fecha_creacion, foto_url FROM usuarios ORDER BY fecha_creacion DESC";
        return DataBase::getRecords($sql);
    }
    
    /**
     * Guardar usuario
     */
    public function save() {
        if ($this->id) {
            // Actualizar
            $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, telefono = ?, email = ?, direccion = ?, foto_url = ? WHERE id = ?";
            $params = [
                $this->nombre,
                $this->apellido,
                $this->telefono,
                $this->email,
                $this->direccion,
                $this->foto_url,
                $this->id
            ];
            return DataBase::execute($sql, $params) > 0;
        } else {
            // Insertar
            $sql = "INSERT INTO usuarios (nombre, apellido, telefono, email, direccion, password, foto_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $params = [
                $this->nombre,
                $this->apellido,
                $this->telefono,
                $this->email,
                $this->direccion,
                password_hash($this->password, PASSWORD_DEFAULT),
                $this->foto_url
            ];
            $result = DataBase::execute($sql, $params);
            if ($result > 0) {
                $this->id = DataBase::obtenerUltimoId();
                return true;
            }
        }
        return false;
    }
    
    /**
     * Actualizar contraseña
     */
    public function updatePassword($newPassword) {
        if (!$this->id) return false;
        
        $sql = "UPDATE usuarios SET password = ? WHERE id = ?";
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $result = DataBase::execute($sql, [$hashedPassword, $this->id]);
        
        if ($result > 0) {
            $this->password = $hashedPassword;
            return true;
        }
        return false;
    }
    
    /**
     * Eliminar usuario
     */
    public function delete() {
        if (!$this->id) return false;
        
        $sql = "DELETE FROM usuarios WHERE id = ?";
        return DataBase::execute($sql, [$this->id]) > 0;
    }
    
    /**
     * Convertir a array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'direccion' => $this->direccion,
            'fecha_creacion' => $this->fecha_creacion,
            'foto_url' => $this->foto_url
        ];
    }
}