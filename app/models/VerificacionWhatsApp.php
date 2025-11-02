<?php

namespace app\models;

use Model;
use DataBase;

class VerificacionWhatsApp extends Model {
    protected $table = 'verificaciones_whatsapp';
    protected $primaryKey = 'id';
    
    public $id;
    public $telefono;
    public $codigo;
    public $fecha_creacion;
    public $fecha_expiracion;
    public $usado;
    public $intentos;
    
    const EXPIRACION_MINUTOS = 10; // Código válido por 10 minutos
    const MAX_INTENTOS = 3; // Máximo 3 intentos fallidos
    
    /**
     * Generar y guardar código de verificación
     */
    public static function generarCodigo($telefono) {
        // Limpiar teléfono
        $telefonoLimpio = preg_replace('/[^0-9+]/', '', $telefono);
        
        // Verificar si ya existe un código activo para este teléfono
        self::limpiarCodigosExpirados();
        $codigoExistente = self::buscarCodigoActivo($telefonoLimpio);
        
        if ($codigoExistente) {
            // Si ya existe un código activo, lo retornamos
            return $codigoExistente['codigo'];
        }
        
        // Generar nuevo código de 6 dígitos
        $codigo = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        // Calcular fecha de expiración
        $fechaExpiracion = date('Y-m-d H:i:s', strtotime('+' . self::EXPIRACION_MINUTOS . ' minutes'));
        
        // Guardar en base de datos
        $sql = "INSERT INTO verificaciones_whatsapp (telefono, codigo, fecha_expiracion) 
                VALUES (?, ?, ?)";
        $result = DataBase::execute($sql, [$telefonoLimpio, $codigo, $fechaExpiracion]);
        
        if ($result > 0) {
            return $codigo;
        }
        
        return false;
    }
    
    /**
     * Verificar código ingresado por el usuario
     */
    public static function verificarCodigo($telefono, $codigo) {
        $telefonoLimpio = preg_replace('/[^0-9+]/', '', $telefono);
        
        // Limpiar códigos expirados
        self::limpiarCodigosExpirados();
        
        // Buscar código activo
        $sql = "SELECT * FROM verificaciones_whatsapp 
                WHERE telefono = ? AND codigo = ? AND usado = 0 
                AND fecha_expiracion > NOW() 
                ORDER BY fecha_creacion DESC LIMIT 1";
        $verificacion = DataBase::getRecord($sql, [$telefonoLimpio, $codigo]);
        
        if (!$verificacion) {
            // Incrementar intentos fallidos si existe el teléfono
            self::incrementarIntentos($telefonoLimpio);
            return false;
        }
        
        // Verificar si no ha excedido intentos
        if ($verificacion['intentos'] >= self::MAX_INTENTOS) {
            return false;
        }
        
        // Marcar código como usado
        $sqlUpdate = "UPDATE verificaciones_whatsapp SET usado = 1 WHERE id = ?";
        DataBase::execute($sqlUpdate, [$verificacion['id']]);
        
        return true;
    }
    
    /**
     * Buscar código activo para un teléfono
     */
    private static function buscarCodigoActivo($telefono) {
        $sql = "SELECT * FROM verificaciones_whatsapp 
                WHERE telefono = ? AND usado = 0 AND fecha_expiracion > NOW() 
                ORDER BY fecha_creacion DESC LIMIT 1";
        return DataBase::getRecord($sql, [$telefono]);
    }
    
    /**
     * Limpiar códigos expirados de la base de datos
     */
    public static function limpiarCodigosExpirados() {
        $sql = "DELETE FROM verificaciones_whatsapp WHERE fecha_expiracion < NOW()";
        return DataBase::execute($sql);
    }
    
    /**
     * Incrementar intentos fallidos
     */
    private static function incrementarIntentos($telefono) {
        $sql = "UPDATE verificaciones_whatsapp 
                SET intentos = intentos + 1 
                WHERE telefono = ? AND usado = 0 AND fecha_expiracion > NOW()";
        return DataBase::execute($sql, [$telefono]);
    }
    
    /**
     * Verificar si un teléfono ha excedido intentos
     */
    public static function haExcedidoIntentos($telefono) {
        $telefonoLimpio = preg_replace('/[^0-9+]/', '', $telefono);
        $sql = "SELECT COUNT(*) as total FROM verificaciones_whatsapp 
                WHERE telefono = ? AND intentos >= ? AND fecha_expiracion > NOW()";
        $result = DataBase::getRecord($sql, [$telefonoLimpio, self::MAX_INTENTOS]);
        return ($result['total'] > 0);
    }
    
    /**
     * Obtener tiempo restante de expiración en minutos
     */
    public static function getTiempoRestante($telefono) {
        $telefonoLimpio = preg_replace('/[^0-9+]/', '', $telefono);
        $sql = "SELECT TIMESTAMPDIFF(MINUTE, NOW(), fecha_expiracion) as minutos_restantes 
                FROM verificaciones_whatsapp 
                WHERE telefono = ? AND usado = 0 AND fecha_expiracion > NOW() 
                ORDER BY fecha_creacion DESC LIMIT 1";
        $result = DataBase::getRecord($sql, [$telefonoLimpio]);
        return $result ? max(0, (int)$result['minutos_restantes']) : 0;
    }
    
    /**
     * Invalidar todos los códigos de un teléfono
     */
    public static function invalidarCodigos($telefono) {
        $telefonoLimpio = preg_replace('/[^0-9+]/', '', $telefono);
        $sql = "UPDATE verificaciones_whatsapp SET usado = 1 WHERE telefono = ? AND usado = 0";
        return DataBase::execute($sql, [$telefonoLimpio]);
    }
}