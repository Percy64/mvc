<!DOCTYPE html>
<html>
<head>
    <title>Test del Sistema de Mascotas Perdidas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test { margin: 10px 0; padding: 10px; border: 1px solid #ccc; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
    </style>
</head>
<body>
    <h1>Test del Sistema de Mascotas Perdidas</h1>
    
    <?php
    // Incluir autoloader y configuración
    require_once '../app/core/Autoloader.php';
    $autoloader = new Autoloader();
    
    define('APP_PATH', '../app/');
    
    echo "<h2>1. Test de Conexión a Base de Datos</h2>";
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=mascotas_db;charset=utf8", "root", "");
        echo "<div class='test success'>✅ Conexión a base de datos exitosa</div>";
    } catch (Exception $e) {
        echo "<div class='test error'>❌ Error de conexión: " . $e->getMessage() . "</div>";
    }
    
    echo "<h2>2. Test de Estructura de Tabla mascotas</h2>";
    try {
        $stmt = $pdo->query("DESCRIBE mascotas");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $hasPerdidos = false;
        foreach ($columns as $column) {
            if ($column['Field'] === 'perdidos') {
                $hasPerdidos = true;
                echo "<div class='test success'>✅ Columna 'perdidos' encontrada: " . $column['Type'] . "</div>";
                break;
            }
        }
        if (!$hasPerdidos) {
            echo "<div class='test error'>❌ Columna 'perdidos' no encontrada</div>";
        }
    } catch (Exception $e) {
        echo "<div class='test error'>❌ Error al verificar tabla: " . $e->getMessage() . "</div>";
    }
    
    echo "<h2>3. Test de Modelo Mascota</h2>";
    try {
        $mascota = \app\models\Mascota::findById(1);
        if ($mascota) {
            echo "<div class='test success'>✅ Modelo Mascota funcionando</div>";
            echo "<div class='test'>Mascota ID 1: " . ($mascota['nombre'] ?? 'Sin nombre') . "</div>";
        } else {
            echo "<div class='test error'>❌ No se encontró mascota con ID 1</div>";
        }
    } catch (Exception $e) {
        echo "<div class='test error'>❌ Error en modelo: " . $e->getMessage() . "</div>";
    }
    
    echo "<h2>4. Test de Métodos de Perdidos</h2>";
    try {
        $metodoExists = method_exists('\app\models\Mascota', 'marcarComoPerdida');
        if ($metodoExists) {
            echo "<div class='test success'>✅ Método marcarComoPerdida existe</div>";
        } else {
            echo "<div class='test error'>❌ Método marcarComoPerdida no existe</div>";
        }
    } catch (Exception $e) {
        echo "<div class='test error'>❌ Error verificando métodos: " . $e->getMessage() . "</div>";
    }
    
    echo "<h2>5. Test de Rutas del Controlador</h2>";
    try {
        $controller = new \app\controllers\MascotaController();
        $methods = ['actionMarcarperdida', 'actionMarcarencontrada', 'actionReportarencontrada'];
        foreach ($methods as $method) {
            if (method_exists($controller, $method)) {
                echo "<div class='test success'>✅ Método $method existe</div>";
            } else {
                echo "<div class='test error'>❌ Método $method no existe</div>";
            }
        }
    } catch (Exception $e) {
        echo "<div class='test error'>❌ Error verificando controlador: " . $e->getMessage() . "</div>";
    }
    
    echo "<h2>Resumen</h2>";
    echo "<p>Si todos los tests son exitosos, el sistema debería funcionar correctamente.</p>";
    echo "<p>Si hay errores, ejecutar la migración SQL en <code>migracion_perdidos.sql</code></p>";
    ?>
</body>
</html>