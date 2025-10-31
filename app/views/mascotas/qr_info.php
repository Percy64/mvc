<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($mascota['nombre'] ?? 'Mascota') ?> - Información de Contacto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .pet-photo {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .contact-section {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
        }
        .emergency-banner {
            background: linear-gradient(45deg, #dc3545, #e74c3c);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        .info-badge {
            background: #007bff;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            display: inline-block;
            margin: 3px;
        }
        .contact-info {
            font-size: 1.1em;
            line-height: 1.6;
        }
        .pet-name {
            color: #007bff;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container mt-4 mb-4">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <?php if (isset($mascota['perdido']) && $mascota['perdido']): ?>
                <div class="emergency-banner">
                    <h4 class="mb-2">🚨 MASCOTA PERDIDA 🚨</h4>
                    <p class="mb-0">Si has encontrado a esta mascota, por favor contacta inmediatamente</p>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body text-center p-4">
                        <!-- Foto de la mascota -->
                        <div class="mb-4">
                            <?php 
                            // Normalizar URL de imagen similar a perfil
                            $img = trim($mascota['foto_url'] ?? '');
                            if ($img !== '') {
                                $img = str_replace('\\', '/', $img);
                                $lower = strtolower($img);
                                $isAbsoluteHttp = (strpos($lower, 'http://') === 0) || (strpos($lower, 'https://') === 0) || (strpos($lower, 'data:') === 0);
                                $isWindowsPath = preg_match('/^[a-z]:\//i', $img) === 1;
                                if ($isWindowsPath) {
                                    $img = '';
                                } elseif ($isAbsoluteHttp) {
                                    // Dejar tal cual
                                } elseif (strpos($img, '/') === 0) {
                                    if (strpos($img, '/assets/') === 0) {
                                        $img = $ROOT . ltrim($img, '/');
                                    }
                                } else {
                                    $img = $ROOT . ltrim($img, '/');
                                }
                            }
                            if ($img === '') {
                                $img = $ROOT . 'assets/images/avatar-placeholder.svg';
                            }
                            ?>
                            <img src="<?= htmlspecialchars($img) ?>" 
                                 alt="<?= htmlspecialchars($mascota['nombre'] ?? '') ?>" 
                                 class="pet-photo"
                                 onerror="this.src='<?= $ROOT ?>assets/images/avatar-placeholder.svg'">
                        </div>
                        
                        <!-- Información de la mascota -->
                        <h2 class="pet-name mb-3"><?= htmlspecialchars($mascota['nombre'] ?? 'Sin nombre') ?></h2>
                        
                        <div class="mb-3">
                            <span class="info-badge"><?= htmlspecialchars($mascota['especie'] ?? 'No especificada') ?></span>
                            <?php if (!empty($mascota['raza'])): ?>
                                <span class="info-badge"><?= htmlspecialchars($mascota['raza']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($mascota['color'])): ?>
                                <span class="info-badge"><?= htmlspecialchars($mascota['color']) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($mascota['descripcion'])): ?>
                        <div class="mb-4">
                            <p class="text-muted"><?= htmlspecialchars($mascota['descripcion']) ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Información de contacto del dueño -->
                        <div class="contact-section">
                            <h5 class="mb-3">📞 Información de Contacto</h5>
                            <?php if (!empty($owner)): ?>
                                <div class="contact-info mb-2">
                                    <strong>Dueño:</strong> <?= htmlspecialchars(($owner['nombre'] ?? '') . ' ' . ($owner['apellido'] ?? '')) ?>
                                </div>
                                <?php if (!empty($owner['telefono'])): ?>
                                <div class="contact-info mb-2">
                                    <strong>Teléfono:</strong> 
                                    <a href="tel:<?= htmlspecialchars($owner['telefono']) ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($owner['telefono']) ?>
                                    </a>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($owner['email'])): ?>
                                <div class="contact-info mb-2">
                                    <strong>Email:</strong> 
                                    <a href="mailto:<?= htmlspecialchars($owner['email']) ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($owner['email']) ?>
                                    </a>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($owner['direccion'])): ?>
                                <div class="contact-info mb-2">
                                    <strong>Dirección:</strong> <?= htmlspecialchars($owner['direccion']) ?>
                                </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="text-muted mb-0">Información de contacto no disponible.</p>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (isset($mascota['perdido']) && $mascota['perdido']): ?>
                        <div class="contact-section" style="background: linear-gradient(45deg, #fff3cd, #ffeaa7);">
                            <h6 class="text-danger mb-2">⚠️ ¿Encontraste a esta mascota?</h6>
                            <p class="small mb-2">Por favor contacta inmediatamente al dueño usando la información de arriba.</p>
                            <p class="small mb-0 text-muted">Fecha reportada como perdida: <?= date('d/m/Y') ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Información adicional -->
                        <div class="mt-4">
                            <p class="small text-muted">
                                📱 Has escaneado el código QR de esta mascota<br>
                                💝 Gracias por ayudar a mantener a las mascotas seguras
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>