<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
<?php echo \app\controllers\SiteController::head(); ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Lista de Usuarios</h3>
                    <div>
                        <a href="<?= Controller::path() ?>usuario/register" class="btn btn-primary me-2">
                            <i class="bi bi-plus-circle"></i> Nuevo Usuario
                        </a>
                        <a href="<?= Controller::path() ?>usuario/panel" class="btn btn-secondary">Panel</a>
                        <a href="<?= Controller::path() ?>" class="btn btn-outline-secondary">Inicio</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['mensaje'])): ?>
                        <?php if ($_GET['mensaje'] === 'eliminado'): ?>
                            <div class="alert alert-success">Usuario eliminado correctamente</div>
                        <?php elseif ($_GET['mensaje'] === 'actualizado'): ?>
                            <div class="alert alert-success">Usuario actualizado correctamente</div>
                        <?php elseif ($_GET['mensaje'] === 'creado'): ?>
                            <div class="alert alert-success">Usuario creado correctamente</div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (empty($usuarios)): ?>
                        <div class="alert alert-info text-center">
                            <h5>No hay usuarios registrados</h5>
                            <p>Comienza agregando tu primer usuario.</p>
                            <a href="<?= Controller::path() ?>usuario/register" class="btn btn-primary">Registrar Usuario</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Apellido</th>
                                        <th>Email</th>
                                        <th>Fecha Registro</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($usuario['id'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($usuario['nombre'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($usuario['apellido'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($usuario['email'] ?? '') ?></td>
                                            <td><?= isset($usuario['fecha_creacion']) ? date('d/m/Y H:i', strtotime($usuario['fecha_creacion'])) : (isset($usuario['fecha_registro']) ? date('d/m/Y H:i', strtotime($usuario['fecha_registro'])) : 'N/A') ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="<?= Controller::path() ?>usuario/perfil?id=<?= urlencode($usuario['id']) ?>"
                                                       class="btn btn-outline-info btn-sm" title="Ver Perfil">
                                                        👁️
                                                    </a>
                                                    <a href="<?= Controller::path() ?>usuario/editar?id=<?= urlencode($usuario['id']) ?>"
                                                       class="btn btn-outline-warning btn-sm" title="Editar">
                                                        ✏️
                                                    </a>
                                                    <a href="<?= Controller::path() ?>usuario/eliminar?id=<?= urlencode($usuario['id']) ?>"
                                                       class="btn btn-outline-danger btn-sm" title="Eliminar"
                                                       onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?')">
                                                        🗑️
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-light">
                                    <strong>Total de usuarios:</strong> <?= count($usuarios) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>