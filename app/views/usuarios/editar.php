<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
<?php echo \app\controllers\SiteController::head(); ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Editar Usuario</h3>
                    <div>
                        <?php if (!empty($is_admin)): ?>
                            <a href="<?= Controller::path() ?>usuario/lista" class="btn btn-outline-secondary btn-sm">Volver a Lista</a>
                        <?php endif; ?>
                        <a href="<?= Controller::path() ?>usuario/perfil" class="btn btn-outline-primary btn-sm">Mi Perfil</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($errores)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errores as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($mensaje)): ?>
                        <?= $mensaje ?>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?= Controller::path() ?>usuario/editar" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id'] ?? '') ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required 
                                           value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" required 
                                           value="<?= htmlspecialchars($usuario['apellido'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required 
                                   value="<?= htmlspecialchars($usuario['email'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="form-text">Deja en blanco si no quieres cambiar la contraseña</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Foto de perfil</label>
                            <div class="d-flex align-items-center gap-3">
                                <?php 
                                $foto = trim($usuario['foto_url'] ?? '');
                                $ROOT = Controller::rootBase();
                                $BASE = Controller::path();
                                if ($foto !== '') {
                                    $foto = str_replace('\\', '/', $foto);
                                    $lower = strtolower($foto);
                                    $isAbs = (strpos($lower, 'http://') === 0) || (strpos($lower, 'https://') === 0) || (strpos($lower, 'data:') === 0);
                                    if (!$isAbs) {
                                        $p = ltrim($foto, '/');
                                        if (strpos($p, 'public/assets/') === 0) {
                                            $foto = $BASE . substr($p, strlen('public/'));
                                        } elseif (strpos($p, 'assets/usuarios/') === 0) {
                                            $foto = $BASE . $p;
                                        } elseif (strpos($foto, '/assets/usuarios/') === 0) {
                                            $foto = $BASE . ltrim($foto, '/');
                                        } elseif (strpos($p, 'assets/') === 0) {
                                            $foto = $ROOT . $p;
                                        } else {
                                            $foto = $ROOT . $p;
                                        }
                                    }
                                } else {
                                    $foto = $ROOT . 'assets/images/avatar-placeholder.svg';
                                }
                                ?>
                                <img src="<?= htmlspecialchars($foto) ?>" alt="Foto actual" class="rounded" style="width:64px;height:64px;object-fit:cover;" onerror="this.src='<?= $ROOT ?>assets/images/avatar-placeholder.svg'">
                                <input type="file" class="form-control" name="foto" id="foto" accept="image/png, image/jpeg, image/jpg, image/gif" style="max-width: 300px;">
                            </div>
                            <div class="form-text">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 5MB.</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <a href="<?= Controller::path() ?>usuario/lista" class="btn btn-outline-secondary">Cancelar</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>