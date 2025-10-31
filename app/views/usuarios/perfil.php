<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
<?php echo \app\controllers\SiteController::head(); ?>

<!-- Hero section con gradiente -->
<section class="py-5" style="background: radial-gradient(1000px 500px at 10% 10%, rgba(13, 110, 253, .12), transparent 60%), radial-gradient(900px 500px at 90% 0%, rgba(255, 193, 7, .12), transparent 60%), linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
    <div class="container py-4">
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
            <h1 class="display-6 fw-bold mb-0">Mi Perfil 👤</h1>
            <div class="d-flex gap-2">
                <?php if (!empty($is_admin)): ?>
                    <a href="<?= Controller::path() ?>usuario/panel" class="btn btn-outline-primary btn-sm">
                        <span class="fs-6">🔧</span> Panel
                    </a>
                <?php endif; ?>
                <a href="<?= Controller::path() ?>" class="btn btn-outline-secondary btn-sm">
                    <span class="fs-6">🏠</span> Inicio
                </a>
                <a href="<?= Controller::path() ?>usuario/logout" class="btn btn-danger btn-sm">
                    <span class="fs-6">🚪</span> Cerrar sesión
                </a>
            </div>
        </div>

        <?php if ($usuario): ?>
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card h-100 shadow-sm border-0 overflow-hidden">
                    <div class="card-body text-center p-4">
                        <div class="mb-4">
                            <?php if (!empty($usuario['foto_url'])): ?>
                                <?php 
                                // Normalizar URL de la foto guardada del usuario
                                $foto = trim($usuario['foto_url']);
                                $foto = str_replace('\\', '/', $foto);
                                $lower = strtolower($foto);
                                $isAbs = (strpos($lower, 'http://') === 0) || (strpos($lower, 'https://') === 0) || (strpos($lower, 'data:') === 0);
                                if (!$isAbs) {
                                    // Compatibilidad con distintas ubicaciones
                                    $p = ltrim($foto, '/');
                                    
                                    // Caso 1: Rutas que empiezan con 'public/assets/'
                                    if (strpos($p, 'public/assets/') === 0) {
                                        $foto = $BASE . substr($p, strlen('public/'));
                                    }
                                    // Caso 2: Rutas 'assets/usuarios/' (nuevas, guardadas en public) - CORREGIDO
                                    elseif (strpos($p, 'assets/usuarios/') === 0) {
                                        $foto = $BASE . 'public/' . $p; // Agregar 'public/' aquí
                                    }
                                    // Caso 3: Rutas '/assets/usuarios/' (con barra inicial)
                                    elseif (strpos($foto, '/assets/usuarios/') === 0) {
                                        $foto = $BASE . 'public' . $foto; // Agregar 'public' aquí
                                    }
                                    // Caso 4: Rutas legacy 'assets/images/usuarios/' (guardadas fuera de public)
                                    elseif (strpos($p, 'assets/images/usuarios/') === 0) {
                                        $foto = $ROOT . $p;
                                    }
                                    // Caso 5: Otras rutas genéricas 'assets/...'
                                    elseif (strpos($p, 'assets/') === 0) {
                                        $foto = $ROOT . $p;
                                    }
                                    // Caso 6: Rutas absolutas del sitio '/algo': dejar tal cual
                                }
                                ?>
                                <div class="position-relative d-inline-block">
                                    <div class="bg-white border rounded-4 shadow-sm p-2">
                                        <img src="<?= htmlspecialchars($foto) ?>" 
                                             alt="Foto de perfil" 
                                             class="rounded-3" 
                                             style="width: 160px; height: 160px; object-fit: cover;"
                                             onerror="this.src='<?= $ROOT ?>assets/images/avatar-placeholder.svg'; console.log('Error loading image: <?= htmlspecialchars($foto) ?>');">
                                    </div>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                                        <span class="fs-6">✓</span>
                                    </span>
                                </div>
                            <?php else: ?>
                                <div class="position-relative d-inline-block">
                                    <div class="bg-primary rounded-4 d-inline-flex align-items-center justify-content-center shadow-sm" 
                                         style="width: 160px; height: 160px;">
                                        <span class="text-white display-6 fw-bold">
                                            <?= strtoupper(substr($usuario['nombre'] ?? 'U', 0, 1)) ?>
                                        </span>
                                    </div>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                                        <span class="fs-6">📷</span>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h4 class="card-title fw-bold mb-2"><?= htmlspecialchars($usuario['nombre'] ?? '') ?> <?= htmlspecialchars($usuario['apellido'] ?? '') ?></h4>
                        <p class="card-text text-muted fs-6 mb-3"><?= htmlspecialchars($usuario['email'] ?? '') ?></p>
                        <div class="bg-light rounded-3 p-3 mb-4">
                            <div class="d-flex align-items-center justify-content-center gap-2 text-muted">
                                <span class="fs-5">📅</span>
                                <span class="small">
                                    Miembro desde: <?= isset($usuario['fecha_creacion']) ? date('d/m/Y', strtotime($usuario['fecha_creacion'])) : (isset($usuario['fecha_registro']) ? date('d/m/Y', strtotime($usuario['fecha_registro'])) : 'N/A') ?>
                                </span>
                            </div>
                        </div>
                        <a href="<?= Controller::path() ?>usuario/editar?id=<?= urlencode($usuario['id']) ?>" class="btn btn-primary btn-lg w-100">
                            <span class="fs-6">✏️</span> Editar Perfil
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                            <span class="fs-4">🐾</span> Mis Mascotas
                        </h5>
                        <a href="<?= Controller::path() ?>mascota/crear" class="btn btn-success btn-sm">
                            <span class="fs-6">➕</span> Agregar Mascota
                        </a>
                    </div>
                    <div class="card-body p-4">
                        <?php /* Las mascotas ahora vienen del controlador en $mascotas */ ?>
                        
                        <?php if (empty($mascotas)): ?>
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" 
                                         style="width: 80px; height: 80px;">
                                        <span class="display-6">🐕</span>
                                    </div>
                                </div>
                                <h6 class="text-muted mb-3">No tienes mascotas registradas aún</h6>
                                <p class="text-muted small mb-4">¡Registra a tu primera mascota y comienza a disfrutar de todas las funcionalidades!</p>
                                <a href="<?= Controller::path() ?>mascota/crear" class="btn btn-primary btn-lg">
                                    <span class="fs-6">🎯</span> Registrar mi primera mascota
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($mascotas as $mascota): ?>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card h-100 shadow-sm border-0 overflow-hidden">
                                            <div class="position-relative">
                                                <?php if (!empty($mascota['foto_url'])): ?>
                                                    <div class="pet-img-wrapper" style="height: 180px; overflow: hidden;">
                                                        <img src="<?= htmlspecialchars($ROOT . ltrim($mascota['foto_url'], '/')) ?>" 
                                                             class="card-img-top w-100 h-100" 
                                                             alt="<?= htmlspecialchars($mascota['nombre']) ?>"
                                                             style="object-fit: cover;">
                                                    </div>
                                                <?php else: ?>
                                                    <div class="bg-gradient d-flex align-items-center justify-content-center" 
                                                         style="height: 180px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                        <span class="text-white display-4">🐾</span>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($mascota['especie'])): ?>
                                                    <span class="badge bg-white text-dark position-absolute top-0 start-0 m-2 shadow-sm">
                                                        <?= htmlspecialchars($mascota['especie']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-body p-3">
                                                <h6 class="card-title fw-bold mb-2"><?= htmlspecialchars($mascota['nombre'] ?? 'Sin nombre') ?></h6>
                                                <p class="card-text text-muted small mb-3">
                                                    <?= htmlspecialchars($mascota['especie'] ?? '') ?>
                                                    <?php if (!empty($mascota['edad'])): ?>
                                                        · <?= htmlspecialchars($mascota['edad']) ?>
                                                    <?php endif; ?>
                                                </p>
                                                <div class="d-grid gap-2">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?= Controller::path() ?>mascota/perfil?id=<?= urlencode($mascota['id_mascota']) ?>" 
                                                           class="btn btn-outline-primary">
                                                            <span class="fs-6">👁️</span> Ver
                                                        </a>
                                                        <a href="<?= Controller::path() ?>mascota/editar?id=<?= urlencode($mascota['id_mascota']) ?>" 
                                                           class="btn btn-outline-warning">
                                                            <span class="fs-6">✏️</span> Editar
                                                        </a>
                                                        <a href="<?= Controller::path() ?>mascota/subirfoto?id_mascota=<?= urlencode($mascota['id_mascota']) ?>" 
                                                           class="btn btn-outline-info">
                                                            <span class="fs-6">📷</span> Foto
                                                        </a>
                                                    </div>
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
            <div class="text-center py-5">
                <div class="alert alert-danger border-0 shadow-sm">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <span class="fs-4">⚠️</span>
                        <span>No se pudo cargar la información del usuario.</span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

 