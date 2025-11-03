<?php 
$BASE = \Controller::path();
$ROOT = \Controller::rootBase();
?>

<!-- Hero Perdidas -->
<section class="py-5" style="background: radial-gradient(1000px 500px at 10% 10%, rgba(220, 53, 69, .10), transparent 60%), radial-gradient(900px 500px at 90% 0%, rgba(255, 193, 7, .12), transparent 60%), linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
  <div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
      <div>
        <h1 class="display-6 fw-bold mb-1">Mascotas perdidas</h1>
        <p class="text-muted mb-0">Ayudá a reunir a las mascotas con sus familias. Si reconocés alguna, entrá a su perfil para más info.</p>
      </div>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="<?= $BASE ?>">
          <span class="fs-6">🏠</span> Inicio
        </a>
      </div>
    </div>

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
  </div>
</section>

<!-- Grid de perdidas -->
<div class="container my-4">
  <?php if (empty($mascotas)): ?>
    <div class="text-center py-5">
      <div class="mb-3">
        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width:90px;height:90px;">
          <span class="fs-1">🔍</span>
        </div>
      </div>
      <h4 class="mb-2">No hay reportes de mascotas perdidas</h4>
      <p class="text-muted mb-4">Cuando alguien marque una mascota como perdida, aparecerá en este listado.</p>
      <div class="d-flex justify-content-center gap-2">
        <a href="<?= $BASE ?>" class="btn btn-outline-secondary">Volver al inicio</a>
        <a href="<?= $BASE ?>mascota" class="btn btn-primary">Explorar mascotas</a>
      </div>
    </div>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($mascotas as $m): ?>
        <?php
          // Normalizar imagen
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
                $img = $ROOT . $p; // assets fuera de /public
              } elseif (strpos($img, '/assets/') === 0) {
                $img = $ROOT . ltrim($img, '/');
              } else {
                $img = $ROOT . $p;
              }
            }
          } else {
            $img = $ROOT . 'assets/images/avatar-placeholder.svg';
          }
          $id = $m['id_mascota'] ?? '';
          $nombre = $m['nombre'] ?? 'Sin nombre';
          $especie = $m['especie'] ?? '';
          $edad = $m['edad'] ?? '';
          $desc = $m['descripcion'] ?? '';
        ?>
        <div class="col-12 col-sm-6 col-lg-4">
          <div class="card h-100 shadow-sm border-0 overflow-hidden">
            <div class="position-relative">
              <div class="pet-img-wrapper pet-img--1-1">
                <img src="<?= htmlspecialchars($img) ?>" class="card-img-top w-100 h-100" alt="<?= htmlspecialchars($nombre) ?>" style="object-fit: cover;" onerror="this.src='<?= $ROOT ?>assets/images/avatar-placeholder.svg'">
              </div>
              <span class="badge bg-danger position-absolute top-0 end-0 m-2">Perdida</span>
              <?php if (!empty($especie)): ?>
                <span class="badge bg-light text-dark position-absolute top-0 start-0 m-2"><?= htmlspecialchars($especie) ?></span>
              <?php endif; ?>
            </div>
            <div class="card-body d-flex flex-column p-3">
              <h6 class="fw-bold mb-1"><?= htmlspecialchars($nombre) ?></h6>
              <p class="text-muted small mb-2">
                <?= htmlspecialchars($especie) ?><?php if ($edad) echo ' · ' . htmlspecialchars($edad); ?>
              </p>
              <p class="small text-truncate mb-3"><?= htmlspecialchars($desc) ?></p>
              <div class="mt-auto text-center">
                <a href="<?= $BASE ?>mascota/qrinfo?id=<?= urlencode((string)$id) ?>" class="btn btn-sm btn-primary">
                  📞 Contactar dueño
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
