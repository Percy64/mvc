<?php
/**
 * Conexión con la Base de Datos utilizando PDO
 */
class DataBase
{
    // Valores por defecto (desarrollo local)
    private static $host = "localhost";
    private static $dbname = "mascotas_db";
    private static $dbuser = "root";
    private static $dbpass = "";
    private static $port = 3306;

    private static $dbh = null; // Database handler
    private static $error;

    // Obtener una instancia de la conexión PDO
    private static function connection()
    {
        if (self::$dbh === null) {
            // 1) Cargar configuración desde app/config/db.php si existe
            $configPath = __DIR__ . '/../config/db.php';
            if (file_exists($configPath)) {
                $cfg = require $configPath; // Debe devolver un array
                if (is_array($cfg)) {
                    self::$host = $cfg['host'] ?? self::$host;
                    self::$dbname = $cfg['name'] ?? self::$dbname;
                    self::$dbuser = $cfg['user'] ?? self::$dbuser;
                    self::$dbpass = $cfg['pass'] ?? self::$dbpass;
                    self::$port = isset($cfg['port']) ? intval($cfg['port']) : self::$port;
                }
            }

            // 2) Variables de entorno (tienen prioridad)
            self::$host = getenv('DB_HOST') ?: self::$host;
            self::$dbname = getenv('DB_NAME') ?: self::$dbname;
            self::$dbuser = getenv('DB_USER') ?: self::$dbuser;
            self::$dbpass = getenv('DB_PASS') ?: self::$dbpass;
            $envPort = getenv('DB_PORT');
            if ($envPort !== false && $envPort !== '') {
                self::$port = intval($envPort);
            }

            $dsn = "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$dbname . ";charset=utf8mb4";
            $opciones = [
                // Algunos hostings gratuitos rechazan conexiones persistentes
                PDO::ATTR_PERSISTENT => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 5,
            ];

            try {
                self::$dbh = new PDO($dsn, self::$dbuser, self::$dbpass, $opciones);
                self::$dbh->exec('SET NAMES utf8mb4');
                self::$dbh->exec('SET time_zone = "-03:00";');
            } catch (PDOException $e) {
                self::$error = $e->getMessage();
                throw new Exception("Error de conexión: " . self::$error);
            }
        }
        return self::$dbh;
    }

    // Ejecutar una consulta con parámetros
    public static function query($sql, $params = [])
    {
        $statement = self::prepareAndExecute($sql, $params);
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    // Ejecutar un SQL que no requiere obtener resultados
    public static function execute($sql, $params = [])
    {
        return self::prepareAndExecute($sql, $params)->rowCount();
    }

    // Obtener el número de registros afectados
    public static function rowCount($sql, $params = [])
    {
        return self::prepareAndExecute($sql, $params)->rowCount();
    }

    // Obtener nombres de columnas de una tabla
    public static function getColumnsNames($table)
    {
        $sql = "SELECT column_name FROM information_schema.columns WHERE table_name = :table";
        return self::query($sql, ['table' => $table]);
    }

    // (removido) ejecutar($sql) sin parámetros: causaba conflicto con el wrapper de compatibilidad

    // Preparar y ejecutar una consulta con manejo de excepciones
    private static function prepareAndExecute($sql, $params = [])
    {
        $statement = self::connection()->prepare($sql);
        try {
            $statement->execute($params);
        } catch (PDOException $e) {
            throw new Exception("Error en la consulta: " . $e->getMessage());
        }
        return $statement;
    }

    // Métodos para compatibilidad con el código existente
    public static function getRecord($sql, $params = [])
    {
        $statement = self::prepareAndExecute($sql, $params);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public static function getRecords($sql, $params = [])
    {
        $statement = self::prepareAndExecute($sql, $params);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerRegistro($sql, $params = [])
    {
        return self::getRecord($sql, $params);
    }

    public static function obtenerRegistros($sql, $params = [])
    {
        return self::getRecords($sql, $params);
    }

    public static function ejecutar($sql, $params = [])
    {
        $result = self::execute($sql, $params);
        return $result !== false;
    }

    public static function obtenerUltimoId()
    {
        return self::connection()->lastInsertId();
    }
}
