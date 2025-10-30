<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
<?php echo \app\controllers\SiteController::head(); ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h2>Panel de Usuario</h2>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Bienvenido, <?= htmlspecialchars($usuario['nombre'] ?? '') ?>!</h5>
                    <p class="card-text">Desde aquí puedes gestionar tu perfil y tus mascotas.</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Mi Perfil</h6>
                                    <p class="card-text">Gestiona tu información personal</p>
                                    <a href="<?= Controller::path() ?>usuario/perfil" class="btn btn-primary">Ver Perfil</a>
                                    <a href="<?= Controller::path() ?>usuario/editar?id=<?= $usuario['id'] ?>" class="btn btn-outline-primary">Editar</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Mis Mascotas</h6>
                                    <p class="card-text">Administra tus mascotas registradas</p>
                                    <a href="<?= Controller::path() ?>mascota" class="btn btn-primary">Ver Mascotas</a>
                                    <a href="<?= Controller::path() ?>mascota/crear" class="btn btn-outline-primary">Agregar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Gestión de Usuarios</h6>
                                    <p class="card-text">Lista de todos los usuarios registrados</p>
                                    <a href="<?= Controller::path() ?>usuario/lista" class="btn btn-primary">Ver Lista</a>
                                    <a href="<?= Controller::path() ?>usuario/register" class="btn btn-outline-primary">Nuevo Usuario</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Navegación</h6>
                                    <p class="card-text">Volver a otras secciones</p>
                                    <a href="<?= Controller::path() ?>" class="btn btn-primary">Página Inicio</a>
                                    <a href="<?= Controller::path() ?>usuario/logout" class="btn btn-outline-danger">Cerrar Sesión</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>