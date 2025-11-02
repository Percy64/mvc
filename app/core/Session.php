<?php

/**
 * Clase Session para MVC
 * Gestiona las sesiones de usuario en la aplicación.
 */
class Session {
    
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Inicia la sesión con los datos del usuario
     */
    public function iniciarSesion($userData) {
        if (is_array($userData)) {
            // Si se pasa un array, usar los datos directamente
            $_SESSION['id'] = $userData['id'] ?? null;
            $_SESSION['nombre'] = $userData['nombre'] ?? '';
            $_SESSION['apellido'] = $userData['apellido'] ?? '';
            $_SESSION['email'] = $userData['email'] ?? '';
            $_SESSION['status'] = true;
            return true;
        } elseif (is_string($userData)) {
            // Compatibilidad con el método anterior (buscar por email)
            require_once __DIR__ . '/../../clases/database.php';
            $query = "SELECT id, nombre, apellido, email FROM usuarios WHERE email = ?";
            $usuario = Database::obtenerRegistro($query, [$userData]);
            
            if ($usuario) {
                $_SESSION['id'] = $usuario['id'] ?? null;
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['apellido'] = $usuario['apellido'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['status'] = true;
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Cierra la sesión del usuario
     */
    public function cerrarSesion() {
        session_unset();
        session_destroy();
        return true;
    }
    
    /**
     * Verifica si hay un usuario autenticado
     */
    public function estaLogueado() {
        return isset($_SESSION['status']) && $_SESSION['status'] === true;
    }
    
    /**
     * Obtener datos del usuario logueado
     */
    public function getUsuario() {
        if ($this->estaLogueado()) {
            return [
                'id' => $_SESSION['id'] ?? null,
                'nombre' => $_SESSION['nombre'] ?? '',
                'apellido' => $_SESSION['apellido'] ?? '',
                'email' => $_SESSION['email'] ?? ''
            ];
        }
        return null;
    }
    
    /**
     * Obtener ID del usuario logueado
     */
    public function getUserId() {
        return $_SESSION['id'] ?? null;
    }
    
    /**
     * Guardar datos en la sesión
     */
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Obtener datos de la sesión
     */
    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Eliminar datos de la sesión
     */
    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
            return true;
        }
        return false;
    }
    
    /**
     * Establecer mensaje flash (se elimina después de leer)
     */
    public function setFlash($key, $message) {
        $_SESSION['flash'][$key] = $message;
    }
    
    /**
     * Obtener mensaje flash
     */
    public function getFlash($key) {
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }
    
    /**
     * Verificar si existe un mensaje flash
     */
    public function hasFlash($key) {
        return isset($_SESSION['flash'][$key]);
    }
}