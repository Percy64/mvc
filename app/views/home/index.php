<?php 
// Bases de URL para este contenido
$BASE = \Controller::path();
$ROOT = \Controller::rootBase();
?>

<!-- Hero orientado a mascotas -->
<section class="py-5" style="background: radial-gradient(1000px 500px at 10% 10%, rgba(13, 110, 253, .12), transparent 60%), radial-gradient(900px 500px at 90% 0%, rgba(255, 193, 7, .12), transparent 60%), linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
    <div class="container py-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-6 order-2 order-lg-1">
                <h1 class="display-5 fw-bold mb-3">Cuida a tus mascotas con BOTI 🐾</h1>
                <p class="lead text-muted mb-4">Perfiles, fotos y códigos QR para que siempre estén identificadas y seguras.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= $BASE ?>mascota" class="btn btn-primary btn-lg">Explorar mascotas</a>
                    <a href="<?= $BASE ?>mascota/perdidas" class="btn btn-outline-warning btn-lg">Mascotas perdidas</a>
                    <?php if (isset($session) && $session->estaLogueado()): ?>
                        <a href="<?= $BASE ?>mascota/crear" class="btn btn-outline-primary btn-lg">Registrar mi mascota</a>
                    <?php else: ?>
                        <a href="<?= $BASE ?>usuario/login" class="btn btn-outline-primary btn-lg">Iniciar sesión</a>
                        <a href="<?= $BASE ?>usuario/register" class="btn btn-success btn-lg">Registrarse</a>
                    <?php endif; ?>
                </div>
                <ul class="list-unstyled mt-4 text-muted">
                    <li class="mb-1">✔ Identificación con QR</li>
                    <li class="mb-1">✔ Fotos y datos siempre a mano</li>
                    <li class="mb-1">✔ Fácil de usar desde cualquier dispositivo</li>
                </ul>
            </div>
            <div class="col-lg-6 order-1 order-lg-2 text-center">
                <div class="position-relative d-inline-block">
                    <div class="bg-white border rounded-4 shadow p-2">
                        <div class="pet-img-wrapper pet-img--4-3 rounded-3">
                            <img src="<?= $ROOT ?>assets/images/mascotas/mascota_1760488784_819b820b.jfif" alt="Mascotas" class="w-100 h-100" style="object-fit: cover;" onerror="this.src='<?= $ROOT ?>assets/images/avatar-placeholder.svg'">
                        </div>
                    </div>
                    <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-warning text-dark">QR Ready</span>
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-4">
        <div class="row g-3">
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100 text-center border-0">
                    <div class="card-body">
                        <div class="fs-2 mb-2">📸</div>
                        <h6 class="mb-1">Sube fotos</h6>
                        <p class="text-muted small mb-0">Guarda momentos y actualiza el perfil.</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100 text-center border-0">
                    <div class="card-body">
                        <div class="fs-2 mb-2">🔖</div>
                        <h6 class="mb-1">Perfiles completos</h6>
                        <p class="text-muted small mb-0">Especie, raza, color, edad y más.</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100 text-center border-0">
                    <div class="card-body">
                        <div class="fs-2 mb-2">🧾</div>
                        <h6 class="mb-1">Código QR</h6>
                        <p class="text-muted small mb-0">Generá un QR único por mascota.</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card h-100 text-center border-0">
                    <div class="card-body">
                        <div class="fs-2 mb-2">🔐</div>
                        <h6 class="mb-1">Seguro y simple</h6>
                        <p class="text-muted small mb-0">Protegido con sesión y CSRF.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mascotas perdidas: tarjetas generadas desde la tabla `mascotas` -->
<div class="container mt-5">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="mb-0">Mascotas perdidas</h2>
        <a href="<?= $BASE ?>mascota/perdidas" class="btn btn-outline-warning btn-sm">Ver todas</a>
    </div>
    <?php if (empty($mascotas)): ?>
        <div class="alert alert-info">No hay mascotas reportadas como perdidas en este momento.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($mascotas as $m): ?>
                <?php
                    // Normalizar imagen de mascota
                    $img = trim($m['foto_url'] ?? '');
                    if ($img !== '') {
                        $img = str_replace('\\', '/', $img);
                        $lower = strtolower($img);
                        $isAbs = (strpos($lower, 'http://') === 0) || (strpos($lower, 'https://') === 0) || (strpos($lower, 'data:') === 0);
                        if (!$isAbs) {
                            $p = ltrim($img, '/');
                            if (strpos($p, 'public/assets/') === 0) {
                                $img = $BASE . substr($p, strlen('public/'));
                            } elseif (strpos($p, 'assets/mascotas/') === 0 || strpos($p, 'assets/images/mascotas/') === 0) {
                                // assets de mascotas (fuera de /public) -> usar $ROOT
                                $img = $ROOT . $p;
                            } elseif (strpos($img, '/assets/') === 0) {
                                // absoluta del sitio
                                $img = $ROOT . ltrim($img, '/');
                            } else {
                                // por defecto
                                $img = $ROOT . $p;
                            }
                        }
                    } else {
                        $img = $ROOT . 'assets/images/avatar-placeholder.svg';
                    }
                    $nombre = $m['nombre'] ?? 'Sin nombre';
                    $especie = $m['especie'] ?? '';
                    $edad = $m['edad'] ?? '';
                    $desc = $m['descripcion'] ?? '';
                    $id = $m['id_mascota'] ?? '';
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm overflow-hidden">
                        <div class="position-relative">
                            <div class="pet-img-wrapper pet-img--1-1">
                                <img src="<?= htmlspecialchars($img) ?>" class="card-img-top pet-img-home" alt="<?= htmlspecialchars($nombre) ?>" loading="lazy" decoding="async" width="600" height="600" onerror="this.src='<?= $ROOT ?>assets/images/avatar-placeholder.svg'">
                            </div>
                            <?php if (!empty($especie)): ?>
                                <span class="badge bg-light text-dark position-absolute top-0 start-0 m-2"><?= htmlspecialchars($especie) ?></span>
                            <?php endif; ?>
                            <?php if (isset($m['perdido']) && $m['perdido']): ?>
                                <span class="badge bg-danger position-absolute top-0 end-0 m-2">PERDIDA</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($nombre) ?></h5>
                            <p class="card-text text-muted mb-2"><?= htmlspecialchars($especie) ?><?php if ($edad) echo ' · ' . htmlspecialchars($edad); ?></p>
                            <p class="card-text small text-truncate"><?= htmlspecialchars($desc) ?></p>
                            <div class="mt-auto text-center">
                                <a href="<?= $BASE ?>mascota/qrinfo?id=<?= urlencode($id) ?>" class="btn btn-sm btn-primary">📞 Contactar dueño</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- CTA inferior -->
<section class="py-5 bg-light mt-4">
    <div class="container text-center">
        <h3 class="mb-2">¿Listo para empezar?</h3>
        <p class="text-muted mb-4">Registrá a tu mascota y generá su QR en minutos.</p>
        <div class="d-flex justify-content-center gap-2">
            <?php if (isset($session) && $session->estaLogueado()): ?>
                <a href="<?= $BASE ?>mascota/crear" class="btn btn-primary">Registrar mi mascota</a>
                <a href="<?= $BASE ?>mascota" class="btn btn-outline-secondary">Ver mascotas</a>
                <a href="<?= $BASE ?>mascota/perdidas" class="btn btn-outline-warning">Mascotas perdidas</a>
            <?php else: ?>
                <a href="<?= $BASE ?>usuario/login" class="btn btn-primary">Iniciar sesión</a>
                <a href="<?= $BASE ?>usuario/register" class="btn btn-success">Crear cuenta</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- contenido usa el layout 'main' -->