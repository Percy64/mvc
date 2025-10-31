<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
<?php echo \app\controllers\SiteController::head(); ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Todas las Mascotas</h2>
        <div>
            <a href="<?= Controller::path() ?>mascota/crear" class="btn btn-success">Agregar Mascota</a>
            <a href="<?= Controller::path() ?>usuario/panel" class="btn btn-outline-secondary">Panel</a>
            <a href="<?= Controller::path() ?>" class="btn btn-outline-primary">Inicio</a>
        </div>
    </div>

    <?php if (empty($mascotas)): ?>
        <div class="alert alert-info text-center">
            <h4>No hay mascotas registradas</h4>
            <p>Sé el primero en agregar una mascota a la comunidad.</p>
            <a href="<?= Controller::path() ?>mascota/crear" class="btn btn-primary">Registrar mi mascota</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($mascotas as $mascota): ?>
                <?php
                    $img = $mascota['foto_url'] ?? '';
                    if (!empty($img)) {
                        $img = str_replace('\\', '/', $img);
                        $lower = strtolower($img);
                        $isAbs = (strpos($lower, 'http://') === 0) || (strpos($lower, 'https://') === 0) || (strpos($lower, 'data:') === 0);
                        if (!$isAbs) {
                            $p = ltrim($img, '/');
                            if (strpos($p, 'public/assets/') === 0) {
                                $img = $BASE . substr($p, strlen('public/'));
                            } elseif (strpos($p, 'assets/mascotas/') === 0 || strpos($p, 'assets/images/mascotas/') === 0) {
                                $img = $ROOT . $p;
                            } elseif (strpos($img, '/assets/') === 0) {
                                $img = $ROOT . ltrim($img, '/');
                            } else {
                                $img = $ROOT . $p;
                            }
                        }
                    }
                    if (empty($img)) {
                        $img = $ROOT . 'assets/images/avatar-placeholder.svg';
                    }
                ?>
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="pet-img-wrapper pet-img--4-3">
                            <img src="<?= htmlspecialchars($img) ?>" 
                                 class="card-img-top pet-img-uniform" 
                                 alt="<?= htmlspecialchars($mascota['nombre'] ?? 'Sin nombre') ?>"
                                 onerror="this.src='<?= $ROOT ?>assets/images/avatar-placeholder.svg'">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title"><?= htmlspecialchars($mascota['nombre'] ?? 'Sin nombre') ?></h6>
                            <p class="card-text text-muted small">
                                <?= htmlspecialchars($mascota['especie'] ?? '') ?>
                                <?php if (!empty($mascota['raza'])): ?>
                                    · <?= htmlspecialchars($mascota['raza']) ?>
                                <?php endif; ?>
                                <?php if (!empty($mascota['edad'])): ?>
                                    <br>Edad: <?= htmlspecialchars($mascota['edad']) ?>
                                <?php endif; ?>
                            </p>
                            <?php if (!empty($mascota['descripcion'])): ?>
                                <p class="card-text small text-truncate"><?= htmlspecialchars($mascota['descripcion']) ?></p>
                            <?php endif; ?>
                            <div class="mt-auto">
                                <div class="btn-group w-100">
                                    <a href="<?= Controller::path() ?>mascota/perfil?id=<?= urlencode($mascota['id_mascota']) ?>" 
                                       class="btn btn-primary btn-sm">Ver perfil</a>
                                    <?php if (isset($_SESSION['id']) && ($_SESSION['id'] == ($mascota['usuario_id'] ?? $mascota['id']))): ?>
                                        <a href="<?= Controller::path() ?>mascota/editar?id=<?= urlencode($mascota['id_mascota']) ?>" 
                                           class="btn btn-outline-warning btn-sm">Editar</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-light text-center">
                    <strong>Total de mascotas:</strong> <?= count($mascotas) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
