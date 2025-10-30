<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
<?php echo \app\controllers\SiteController::head(); ?>
<div class="container mt-4">
    <?php if ($mascota): ?>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><?= htmlspecialchars($mascota['nombre'] ?? 'Sin nombre') ?></h3>
                    <div class="d-flex align-items-center gap-2">
                        <a href="<?= Controller::path() ?>mascota" class="btn btn-outline-secondary btn-sm">Ver todas</a>
                        <a href="<?= Controller::path() ?>" class="btn btn-outline-primary btn-sm">Inicio</a>
                        <?php if (isset($_SESSION['id']) && ($_SESSION['id'] == ($mascota['usuario_id'] ?? $mascota['id']))): ?>
                            <a href="<?= Controller::path() ?>mascota/editar?id=<?= urlencode($mascota['id_mascota']) ?>" class="btn btn-warning btn-sm">Editar</a>
                            <form method="POST" action="<?= Controller::path() ?>mascota/eliminar" onsubmit="return confirm('¿Seguro que deseas eliminar esta mascota? Esta acción no se puede deshacer.');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                <input type="hidden" name="id_mascota" value="<?= htmlspecialchars($mascota['id_mascota']) ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?php 
                            // Normalizar URL de imagen principal de la mascota (compatible PHP < 8)
                            $img = trim($mascota['foto_url'] ?? '');
                            if ($img !== '') {
                                // Unificar separadores en caso de rutas tipo Windows
                                $img = str_replace('\\', '/', $img);
                                $lower = strtolower($img);
                                $isAbsoluteHttp = (strpos($lower, 'http://') === 0) || (strpos($lower, 'https://') === 0) || (strpos($lower, 'data:') === 0);
                                $isWindowsPath = preg_match('/^[a-z]:\//i', $img) === 1; // e.g. C:/...
                                if ($isWindowsPath) {
                                    // Rutas locales no son servibles por el navegador
                                    $img = '';
                                } elseif ($isAbsoluteHttp) {
                                    // Dejar tal cual
                                } elseif (strpos($img, '/') === 0) {
                                    // Ruta absoluta del sitio. Si apunta a /assets/... convertirla a base del proyecto
                                    if (strpos($img, '/assets/') === 0) {
                                        $img = $ROOT . ltrim($img, '/');
                                    }
                                    // Si ya comienza con la base del proyecto ($ROOT) la dejamos tal cual
                                } else {
                                    // Ruta relativa (assets/...), prefijar base del proyecto
                                    $img = $ROOT . ltrim($img, '/');
                                }
                            }
                            if ($img === '') {
                                $img = $ROOT . 'assets/images/avatar-placeholder.svg';
                            }
                            ?>
                            <img src="<?= htmlspecialchars($img) ?>" 
                                 class="img-fluid rounded pet-img-main" 
                                 alt="<?= htmlspecialchars($mascota['nombre'] ?? '') ?>"
                                 onerror="this.src='<?= $ROOT ?>assets/images/avatar-placeholder.svg'">
                        </div>
                        <div class="col-md-6">
                            <h5>Información</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Especie:</strong></td>
                                    <td><?= htmlspecialchars($mascota['especie'] ?? 'No especificada') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Raza:</strong></td>
                                    <td><?= htmlspecialchars($mascota['raza'] ?? 'No especificada') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Edad:</strong></td>
                                    <td><?= htmlspecialchars($mascota['edad'] ?? 'No especificada') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Registrado:</strong></td>
                                    <td><?= isset($mascota['fecha_registro']) ? date('d/m/Y', strtotime($mascota['fecha_registro'])) : 'N/A' ?></td>
                                </tr>
                            </table>
                            
                            <?php if (!empty($mascota['descripcion'])): ?>
                                <h6>Descripción</h6>
                                <p class="text-muted"><?= htmlspecialchars($mascota['descripcion']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Fotos adicionales -->
            <?php 
            // Obtener fotos adicionales si existe la tabla
            $fotos = [];
            try {
                $fotos = DataBase::obtenerRegistros('SELECT url, descripcion FROM fotos_mascotas WHERE id_mascota = ?', [$mascota['id_mascota']]);
            } catch (Throwable $e) {
                $fotos = [];
            }
            ?>
            
            <?php if (!empty($fotos)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Galería de fotos</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($fotos as $foto): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <?php 
                                    // Normalizar URL de cada foto adicional (compatible PHP < 8)
                                    $fotoUrl = trim($foto['url'] ?? '');
                                    if ($fotoUrl !== '') {
                                        $fotoUrl = str_replace('\\', '/', $fotoUrl);
                                        $lower = strtolower($fotoUrl);
                                        $isAbsoluteHttp = (strpos($lower, 'http://') === 0) || (strpos($lower, 'https://') === 0) || (strpos($lower, 'data:') === 0);
                                        $isWindowsPath = preg_match('/^[a-z]:\//i', $fotoUrl) === 1;
                                        if ($isWindowsPath) {
                                            $fotoUrl = '';
                                        } elseif ($isAbsoluteHttp) {
                                            // OK
                                        } elseif (strpos($fotoUrl, '/') === 0) {
                                            // absoluta del sitio; si es /assets/... usar base del proyecto
                                            if (strpos($fotoUrl, '/assets/') === 0) {
                                                $fotoUrl = $ROOT . ltrim($fotoUrl, '/');
                                            }
                                        } else {
                                            $fotoUrl = $ROOT . ltrim($fotoUrl, '/');
                                        }
                                    }
                                    if ($fotoUrl === '') {
                                        $fotoUrl = $ROOT . 'assets/images/avatar-placeholder.svg';
                                    }
                                    ?>
                                    <img src="<?= htmlspecialchars($fotoUrl) ?>" 
                                         class="card-img-top" 
                                         alt="Foto de <?= htmlspecialchars($mascota['nombre']) ?>"
                                 style="height: 200px; object-fit: cover;"
                                 onerror="this.src='<?= $ROOT ?>assets/images/avatar-placeholder.svg'">
                                    <?php if (!empty($foto['descripcion'])): ?>
                                        <div class="card-body">
                                            <p class="card-text small"><?= htmlspecialchars($foto['descripcion']) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="col-md-4">
            <!-- Información del propietario -->
            <?php 
            $ownerId = $mascota['usuario_id'] ?? $mascota['id'] ?? null;
            $owner = null;
            if ($ownerId) {
                try {
                    $owner = DataBase::obtenerRegistro('SELECT id, nombre, apellido, email, foto_url FROM usuarios WHERE id = ?', [$ownerId]);
                } catch (Throwable $e) {
                    $owner = null;
                }
            }
            ?>
            
            <?php if ($owner): ?>
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Propietario</h6>
                </div>
                <div class="card-body text-center">
                    <?php if (!empty($owner['foto_url'])): ?>
                        <?php 
                        // Normalizar URL de foto del propietario (compatible PHP < 8)
                        $ownerUrl = trim((string)$owner['foto_url']);
                        $ownerUrl = str_replace('\\', '/', $ownerUrl);
                        $lower = strtolower($ownerUrl);
                        $isAbsoluteHttp = (strpos($lower, 'http://') === 0) || (strpos($lower, 'https://') === 0) || (strpos($lower, 'data:') === 0);
                        $isWindowsPath = preg_match('/^[a-z]:\//i', $ownerUrl) === 1;
                        $BASE = Controller::path();
                        if ($isWindowsPath) {
                            $ownerUrl = $ROOT . 'assets/images/avatar-placeholder.svg';
                        } elseif ($isAbsoluteHttp) {
                            // OK
                        } else {
                            $p = ltrim($ownerUrl, '/');
                            if (strpos($p, 'public/assets/') === 0) {
                                $ownerUrl = $BASE . substr($p, strlen('public/'));
                            } elseif (strpos($p, 'assets/usuarios/') === 0) {
                                $ownerUrl = $BASE . $p;
                            } elseif (strpos($ownerUrl, '/assets/usuarios/') === 0) {
                                $ownerUrl = $BASE . ltrim($ownerUrl, '/');
                            } elseif (strpos($p, 'assets/') === 0) {
                                $ownerUrl = $ROOT . $p;
                            } else {
                                $ownerUrl = $ROOT . $p;
                            }
                        }
                        ?>
                    <img src="<?= htmlspecialchars($ownerUrl) ?>" 
                             alt="Foto de <?= htmlspecialchars($owner['nombre']) ?>" 
                             class="rounded-circle mb-2" 
                        style="width: 80px; height: 80px; object-fit: cover;"
                        onerror="this.src='<?= $ROOT ?>assets/images/avatar-placeholder.svg'">
                    <?php else: ?>
                        <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center mb-2" 
                             style="width: 80px; height: 80px;">
                            <span class="text-white">
                                <?= strtoupper(substr($owner['nombre'] ?? 'U', 0, 1)) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <h6><?= htmlspecialchars($owner['nombre'] ?? '') ?> <?= htmlspecialchars($owner['apellido'] ?? '') ?></h6>
                    <p class="text-muted small"><?= htmlspecialchars($owner['email'] ?? '') ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Acciones -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Acciones</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?php if (isset($_SESSION['id'])): ?>
                            <a href="<?= Controller::path() ?>mascota/subir-foto?id_mascota=<?= urlencode($mascota['id_mascota']) ?>" 
                               class="btn btn-outline-primary">Subir foto</a>
                        <?php endif; ?>
                        <a href="<?= Controller::path() ?>mascota/qr?id=<?= urlencode($mascota['id_mascota']) ?>" 
                           class="btn btn-outline-info">Generar QR</a>
                        <a href="<?= Controller::path() ?>mascota" class="btn btn-outline-secondary">Ver todas las mascotas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-danger">
        <h4>Mascota no encontrada</h4>
        <p>La mascota que buscas no existe o no está disponible.</p>
    <a href="<?= Controller::path() ?>mascota" class="btn btn-primary">Ver todas las mascotas</a>
    </div>
    <?php endif; ?>
</div>