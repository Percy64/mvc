<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
<?php echo \app\controllers\SiteController::head(); ?>
<div class="container mt-4">
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash_error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4">Perfil de Usuario</h1>
        <div>
            <?php if (!empty($is_admin)): ?>
                <a href="<?= Controller::path() ?>usuario/panel" class="btn btn-outline-secondary btn-sm me-2">Panel</a>
            <?php endif; ?>
            <a href="<?= Controller::path() ?>" class="btn btn-outline-secondary btn-sm me-2">Inicio</a>
            <a href="<?= Controller::path() ?>usuario/logout" class="btn btn-danger btn-sm">Cerrar sesión</a>
        </div>
    </div>

    <?php if ($usuario): ?>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <?php if (!empty($usuario['foto_url'])): ?>
                            <?php 
                            // Normalizar URL de la foto guardada del usuario
                            $foto = trim($usuario['foto_url']);
                            $foto = str_replace('\\', '/', $foto);
                            $lower = strtolower($foto);
                            $isAbs = (strpos($lower, 'http://') === 0) || (strpos($lower, 'https://') === 0) || (strpos($lower, 'data:') === 0);
                            if (!$isAbs) {
                                // Compatibilidad con distintas ubicaciones
                                // 1) Si la ruta contiene 'public/assets/...', usar $BASE + 'assets/...'
                                $p = ltrim($foto, '/');
                                if (strpos($p, 'public/assets/') === 0) {
                                    $foto = $BASE . substr($p, strlen('public/'));
                                }
                                // 2) Si inicia con 'assets/usuarios', es un asset bajo /public -> usar $BASE
                                elseif (strpos($p, 'assets/usuarios/') === 0) {
                                    $foto = $BASE . $p;
                                }
                                // 3) Si inicia con '/assets/usuarios', igual que 2
                                elseif (strpos($foto, '/assets/usuarios/') === 0) {
                                    $foto = $BASE . ltrim($foto, '/');
                                }
                                // 4) Rutas relativas genéricas 'assets/...': por defecto usar $ROOT (para assets fuera de /public)
                                elseif (strpos($p, 'assets/') === 0) {
                                    $foto = $ROOT . $p;
                                }
                                // 5) Rutas absolutas del sitio '/algo': dejar tal cual
                            }
                            ?>
                            <img src="<?= htmlspecialchars($foto) ?>" 
                                 alt="Foto de perfil" 
                                 class="rounded-circle img-fluid" 
                                 style="width: 150px; height: 150px; object-fit: cover;"
                                 onerror="this.src='<?= $ROOT ?>assets/images/avatar-placeholder.svg'">
                        <?php else: ?>
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 150px; height: 150px;">
                                <span class="text-white fs-1">
                                    <?= strtoupper(substr($usuario['nombre'] ?? 'U', 0, 1)) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h5 class="card-title"><?= htmlspecialchars($usuario['nombre'] ?? '') ?> <?= htmlspecialchars($usuario['apellido'] ?? '') ?></h5>
                    <p class="card-text text-muted"><?= htmlspecialchars($usuario['email'] ?? '') ?></p>
                    <p class="card-text">
                        <small class="text-muted">
                            Miembro desde: <?= isset($usuario['fecha_creacion']) ? date('d/m/Y', strtotime($usuario['fecha_creacion'])) : (isset($usuario['fecha_registro']) ? date('d/m/Y', strtotime($usuario['fecha_registro'])) : 'N/A') ?>
                        </small>
                    </p>
                    <a href="<?= Controller::path() ?>usuario/editar?id=<?= urlencode($usuario['id']) ?>" class="btn btn-primary">Editar Perfil</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mis Mascotas</h5>
                    <a href="<?= Controller::path() ?>mascota/crear" class="btn btn-success btn-sm">Agregar Mascota</a>
                </div>
                <div class="card-body">
                    <?php /* Las mascotas ahora vienen del controlador en $mascotas */ ?>
                    
                    <?php if (empty($mascotas)): ?>
                        <div class="text-center py-4">
                            <p class="text-muted">No tienes mascotas registradas aún.</p>
                            <a href="<?= Controller::path() ?>mascota/crear" class="btn btn-primary">Registrar mi primera mascota</a>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($mascotas as $mascota): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <?php if (!empty($mascota['foto_url'])): ?>
                                            <img src="<?= htmlspecialchars($ROOT . ltrim($mascota['foto_url'], '/')) ?>" 
                                                 class="card-img-top" 
                                                 alt="<?= htmlspecialchars($mascota['nombre']) ?>"
                                                 style="height: 200px; object-fit: cover;">
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h6 class="card-title"><?= htmlspecialchars($mascota['nombre'] ?? 'Sin nombre') ?></h6>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <?= htmlspecialchars($mascota['especie'] ?? '') ?>
                                                    <?php if (!empty($mascota['edad'])): ?>
                                                        · <?= htmlspecialchars($mascota['edad']) ?>
                                                    <?php endif; ?>
                                                </small>
                                            </p>
                                            <div class="btn-group btn-group-sm w-100">
                                                                <a href="<?= Controller::path() ?>mascota/perfil?id=<?= urlencode($mascota['id_mascota']) ?>" 
                                                   class="btn btn-outline-primary">Ver</a>
                                                                <a href="<?= Controller::path() ?>mascota/editar?id=<?= urlencode($mascota['id_mascota']) ?>" 
                                                   class="btn btn-outline-warning">Editar</a>
                                                                <a href="<?= Controller::path() ?>mascota/subir-foto?id_mascota=<?= urlencode($mascota['id_mascota']) ?>" 
                                                   class="btn btn-outline-info">Foto</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-danger">
        No se pudo cargar la información del usuario.
    </div>
    <?php endif; ?>
</div>