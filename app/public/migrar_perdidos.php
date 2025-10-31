<?php
// Script para ejecutar la migración de mascotas perdidas
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Migración - Sistema de Mascotas Perdidas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f8f9fa; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .step { margin: 15px 0; padding: 15px; border-radius: 5px; }
        .success { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .warning { background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        .info { background-color: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
        .btn { padding: 10px 20px; margin: 5px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-success { background-color: #28a745; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🐾 Migración del Sistema de Mascotas Perdidas</h1>
        
        <?php
        // Configuración de la base de datos
        $host = 'localhost';
        $dbname = 'mascotas_db';
        $username = 'root';
        $password = '';
        
        $migracionEjecutada = false;
        
        if (isset($_POST['ejecutar_migracion'])) {
            echo "<h2>Ejecutando Migración...</h2>";
            
            try {
                // Conectar a la base de datos
                $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                echo "<div class='step success'>✅ Conexión a la base de datos exitosa</div>";
                
                // Verificar si la columna ya existe
                $stmt = $pdo->query("SHOW COLUMNS FROM mascotas LIKE 'perdidos'");
                $columnaExiste = $stmt->rowCount() > 0;
                
                if ($columnaExiste) {
                    echo "<div class='step warning'>⚠️ La columna 'perdidos' ya existe</div>";
                } else {
                    echo "<div class='step info'>📝 Agregando columna 'perdidos' a la tabla mascotas...</div>";
                    
                    // Agregar la columna perdidos
                    $pdo->exec("ALTER TABLE mascotas ADD COLUMN perdidos BOOLEAN DEFAULT FALSE AFTER fecha_registro");
                    echo "<div class='step success'>✅ Columna 'perdidos' agregada exitosamente</div>";
                }
                
                // Actualizar registros existentes
                $stmt = $pdo->prepare("UPDATE mascotas SET perdidos = FALSE WHERE perdidos IS NULL");
                $stmt->execute();
                echo "<div class='step success'>✅ Registros existentes actualizados</div>";
                
                // Crear tabla de reportes
                $sqlReportes = "CREATE TABLE IF NOT EXISTS reportes_encontradas (
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
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
                
                $pdo->exec($sqlReportes);
                echo "<div class='step success'>✅ Tabla 'reportes_encontradas' creada/verificada</div>";
                
                $migracionEjecutada = true;
                echo "<div class='step success'><strong>🎉 ¡Migración completada exitosamente!</strong></div>";
                
                // Mostrar estructura actualizada
                echo "<h3>Estructura de la tabla mascotas:</h3>";
                $stmt = $pdo->query("DESCRIBE mascotas");
                $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "<pre>";
                foreach ($columns as $column) {
                    echo sprintf("%-20s %-15s %-5s %-5s %-10s %s\n", 
                        $column['Field'], 
                        $column['Type'], 
                        $column['Null'], 
                        $column['Key'], 
                        $column['Default'], 
                        $column['Extra']
                    );
                }
                echo "</pre>";
                
            } catch (PDOException $e) {
                echo "<div class='step error'>❌ Error de base de datos: " . htmlspecialchars($e->getMessage()) . "</div>";
            } catch (Exception $e) {
                echo "<div class='step error'>❌ Error general: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
        
        if (!$migracionEjecutada) {
            ?>
            <div class="step info">
                <p><strong>Este script agregará la funcionalidad de mascotas perdidas a tu sistema.</strong></p>
                <p>Se realizarán los siguientes cambios:</p>
                <ul>
                    <li>✅ Agregar columna <code>perdidos</code> a la tabla <code>mascotas</code></li>
                    <li>✅ Crear tabla <code>reportes_encontradas</code> para reportes de hallazgos</li>
                    <li>✅ Actualizar registros existentes</li>
                </ul>
                
                <form method="POST">
                    <button type="submit" name="ejecutar_migracion" class="btn btn-primary">
                        🚀 Ejecutar Migración
                    </button>
                </form>
            </div>
            <?php
        } else {
            ?>
            <div class="step success">
                <h3>🎯 Próximos pasos:</h3>
                <ol>
                    <li>La migración se ha completado exitosamente</li>
                    <li>Ahora puedes probar el sistema de mascotas perdidas</li>
                    <li>Ve al perfil de una mascota para probar los nuevos botones</li>
                </ol>
                
                <a href="mascota/perfil?id=76" class="btn btn-success">🐾 Probar Sistema</a>
                <a href="test_perdidos.php" class="btn btn-primary">🧪 Ejecutar Tests</a>
            </div>
            <?php
        }
        ?>
        
        <div class="step info">
            <h3>📋 Información de configuración:</h3>
            <ul>
                <li><strong>Base de datos:</strong> <?= htmlspecialchars($dbname) ?></li>
                <li><strong>Host:</strong> <?= htmlspecialchars($host) ?></li>
                <li><strong>Usuario:</strong> <?= htmlspecialchars($username) ?></li>
            </ul>
        </div>
    </div>
</body>
</html>