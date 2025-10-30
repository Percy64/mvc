<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prueba MVC - BOTI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h1 class="mb-0">🎉 ¡Sistema MVC Funcionando!</h1>
                    </div>
                    <div class="card-body">
                        <h3>✅ Proyecto BOTI en /mvc/</h3>
                        <p class="lead">El sistema MVC ha sido configurado correctamente en la carpeta <code>/mvc/</code></p>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h5>🔗 Enlaces de Prueba:</h5>
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <a href="/MVC/public/" class="text-decoration-none">🏠 Inicio (Home)</a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="/MVC/public/mascota" class="text-decoration-none">🐕 Mascotas</a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="/MVC/public/usuario/login" class="text-decoration-none">🔐 Login</a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="/MVC/public/usuario/register" class="text-decoration-none">📝 Registro</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5>📋 Información del Sistema:</h5>
                                <ul class="list-unstyled">
                                    <li><strong>📂 Estructura:</strong> MVC con namespaces</li>
                                    <li><strong>🗃️ Base de datos:</strong> MySQL (mascotas_db)</li>
                                    <li><strong>🔧 Autoloader:</strong> PSR-4 compatible</li>
                                    <li><strong>🎨 UI:</strong> Bootstrap 5</li>
                                    <li><strong>⚡ Servidor:</strong> XAMPP</li>
                                </ul>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="alert alert-info">
                            <h6>💡 Información de Debug:</h6>
                            <p class="mb-1"><strong>URL actual:</strong> <?= $_SERVER['REQUEST_URI'] ?? 'N/A' ?></p>
                            <p class="mb-1"><strong>Parámetro URL:</strong> <?= $_GET['url'] ?? 'Ninguno' ?></p>
                            <p class="mb-0"><strong>Método:</strong> <?= $_SERVER['REQUEST_METHOD'] ?? 'N/A' ?></p>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="/MVC/public/" class="btn btn-primary btn-lg">
                                🚀 Ir al Sistema Completo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>