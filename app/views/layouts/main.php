<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'Mi Aplicación de Mascotas' ?></title>
    <?php 
    $BASE = \Controller::path();
    $ROOT = \Controller::rootBase();
    ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
    <?php echo \app\controllers\SiteController::head(); ?>
</head>
<body class="bg-light">
    <?php 
    // Incluir la sesión para el navbar
    require_once __DIR__ . '/../../core/Session.php';
    $ses = new Session(); 
    ?>
    <!-- Navbar -->
    <nav class="navbar navbar-light bg-white shadow-sm navbar-center">
        <div class="container">
            <!-- Left slot: menu icon -->
            <div class="menu-icon-left d-flex align-items-center">
                <button class="btn btn-outline-secondary d-lg-none me-2" type="button" data-bs-toggle="collapse" data-bs-target="#navContent" aria-controls="navContent" aria-expanded="false" aria-label="Toggle navigation">
                    <img src="<?= $ROOT ?>assets/images/menu.png" alt="" class="menu-icon">
                </button>
                <?php $onMascotaPerfil = isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/mascota/perfil') !== false; ?>
                <?php if ($onMascotaPerfil): ?>
                    <!-- En perfil de mascota: sin desplegable, links directos -->
                    <div class="d-none d-lg-flex align-items-center gap-2">
                        <a href="<?= $BASE ?>" class="btn btn-outline-secondary btn-sm">Inicio</a>
                        <a href="<?= $BASE ?>mascota/perdidas" class="btn btn-outline-secondary btn-sm">Mascotas perdidas</a>
                    </div>
                <?php else: ?>
                    <!-- Desktop dropdown (visible en el resto de páginas) -->
                    <div class="d-none d-lg-block dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="menuLargeBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= $ROOT ?>assets/images/menu.png" alt="" class="menu-icon">
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= $BASE ?>">Inicio</a></li>
                            <li><a class="dropdown-item" href="<?= $BASE ?>mascota/perdidas">Mascotas perdidas</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php if ($ses->estaLogueado()): ?>
                            <li class="dropdown-item-text">Hola, <?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= $BASE ?>usuario/logout">Cerrar sesión</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Mobile menu content -->
            <div class="collapse navbar-collapse justify-content-end" id="navContent">
                <ul class="navbar-nav">
                    <?php if (!$ses->estaLogueado()): ?>
                    <li class="nav-item d-lg-none"><a class="nav-link" href="<?= $BASE ?>usuario/login">Iniciar sesión</a></li>
                    <li class="nav-item d-lg-none"><a class="nav-link" href="<?= $BASE ?>usuario/register">Registrarse</a></li>
                    <li class="nav-item d-lg-none"><hr class="dropdown-divider"></li>
                    <?php endif; ?>
                    <li class="nav-item d-lg-none"><a class="nav-link" href="<?= $BASE ?>">Inicio</a></li>
                    <li class="nav-item d-lg-none"><a class="nav-link" href="<?= $BASE ?>mascota/perdidas">Mascotas perdidas</a></li>
                    <?php if ($ses->estaLogueado()): ?>
                    <li class="nav-item d-lg-none"><hr class="dropdown-divider"></li>
                    <li class="nav-item d-lg-none"><a class="nav-link text-danger" href="<?= $BASE ?>usuario/logout">Cerrar sesión</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Logo centered -->
            <div class="brand-center">
                <a href="<?= $BASE ?>">
                    <img src="<?= $ROOT ?>assets/images/logo.png" alt="Logo" class="navbar-logo">
                </a>
            </div>

            <!-- Right actions -->
            <div class="nav-actions ms-auto d-none d-lg-flex align-items-center">
                <?php if ($ses->estaLogueado()): ?>
                    <?php $onPerfil = isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/usuario/perfil') !== false; ?>
                    <?php if (!$onPerfil): ?>
                        <a href="<?= $BASE ?>usuario/perfil" class="btn btn-outline-secondary me-2 btn-sm">Perfil</a>
                    <?php endif; ?>
                    <span class="me-3">Hola, <?= htmlspecialchars($_SESSION['nombre'] ?? '') ?></span>
                <?php else: ?>
                    <a href="<?= $BASE ?>usuario/login" class="btn btn-link">Iniciar sesión</a>
                    <a href="<?= $BASE ?>usuario/register" class="btn btn-primary">Registrarse</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main>
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="mt-5 py-4 bg-light border-top">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="text-muted small mb-2 mb-md-0">
                <img src="<?= $ROOT ?>assets/images/logo.png" alt="Logo" style="height:20px; vertical-align:middle; margin-right:8px;">
                © <?= date('Y') ?> BOTI · PHP MVC
            </div>
            <ul class="nav small">
                <li class="nav-item"><a class="nav-link text-muted" href="<?= $BASE ?>">Inicio</a></li>
                <li class="nav-item"><a class="nav-link text-muted" href="<?= $BASE ?>mascota/perdidas">Mascotas perdidas</a></li>
                <li class="nav-item"><a class="nav-link text-muted" href="<?= $BASE . ($ses->estaLogueado() ? 'usuario/perfil' : 'usuario/login') ?>">Mis Mascotas</a></li>
                <?php if ($ses->estaLogueado()): ?>
                    <li class="nav-item"><a class="nav-link text-muted" href="<?= $BASE ?>usuario/panel">Panel</a></li>
                    <li class="nav-item"><a class="nav-link text-muted" href="<?= $BASE ?>usuario/logout">Salir</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link text-muted" href="<?= $BASE ?>usuario/login">Ingresar</a></li>
                    <li class="nav-item"><a class="nav-link text-muted" href="<?= $BASE ?>usuario/register">Crear cuenta</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="container">
            <p class="text-center text-muted small mt-2 mb-0">Servidor local: <?= htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'localhost') ?></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
