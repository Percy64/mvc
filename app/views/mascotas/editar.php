<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
<?php echo \app\controllers\SiteController::head(); ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <a href="javascript:history.back()" class="btn btn-link text-decoration-none me-2 p-0" title="Volver">&larr;</a>
                        <h3 class="mb-0">Editar Mascota</h3>
                    </div>
                    <div>
                        <a href="<?= Controller::path() ?>mascota/perfil?id=<?= urlencode($mascota['id_mascota'] ?? '') ?>" class="btn btn-outline-info btn-sm">Ver Perfil</a>
                        <a href="<?= Controller::path() ?>mascota" class="btn btn-outline-secondary btn-sm">Lista Mascotas</a>
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
                    
                    <form method="POST" action="<?= Controller::path() ?>mascota/update" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($mascota['id_mascota'] ?? '') ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre de la mascota</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required 
                                           value="<?= htmlspecialchars($mascota['nombre'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="especie" class="form-label">Especie</label>
                                    <select class="form-control" id="especie" name="especie" required>
                                        <option value="">Seleccionar especie</option>
                                        <option value="Perro" <?= (($mascota['especie'] ?? '') === 'Perro') ? 'selected' : '' ?>>Perro</option>
                                        <option value="Gato" <?= (($mascota['especie'] ?? '') === 'Gato') ? 'selected' : '' ?>>Gato</option>
                                        <option value="Conejo" <?= (($mascota['especie'] ?? '') === 'Conejo') ? 'selected' : '' ?>>Conejo</option>
                                        <option value="Hamster" <?= (($mascota['especie'] ?? '') === 'Hamster') ? 'selected' : '' ?>>Hamster</option>
                                        <option value="Ave" <?= (($mascota['especie'] ?? '') === 'Ave') ? 'selected' : '' ?>>Ave</option>
                                        <option value="Pez" <?= (($mascota['especie'] ?? '') === 'Pez') ? 'selected' : '' ?>>Pez</option>
                                        <option value="Otro" <?= (($mascota['especie'] ?? '') === 'Otro') ? 'selected' : '' ?>>Otro</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="raza" class="form-label">Raza (opcional)</label>
                                    <input type="text" class="form-control" id="raza" name="raza" 
                                           value="<?= htmlspecialchars($mascota['raza'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edad" class="form-label">Edad (opcional)</label>
                                    <input type="number" min="0" step="1" class="form-control" id="edad" name="edad" 
                                           value="<?= htmlspecialchars($mascota['edad'] ?? '') ?>">
                                    <div class="form-text">Ingrese un número entero (años).</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sexo" class="form-label">Sexo (opcional)</label>
                                    <select class="form-control" id="sexo" name="sexo">
                                        <option value="">Seleccionar</option>
                                        <option value="Macho" <?= (($mascota['sexo'] ?? '') === 'Macho') ? 'selected' : '' ?>>Macho</option>
                                        <option value="Hembra" <?= (($mascota['sexo'] ?? '') === 'Hembra') ? 'selected' : '' ?>>Hembra</option>
                                        <option value="Otro" <?= (($mascota['sexo'] ?? '') === 'Otro') ? 'selected' : '' ?>>Otro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="color" class="form-label">Color (opcional)</label>
                                    <input type="text" class="form-control" id="color" name="color" 
                                           value="<?= htmlspecialchars($mascota['color'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción (opcional)</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($mascota['descripcion'] ?? '') ?></textarea>
                        </div>
                        
                        <?php if (!empty($mascota['foto_url'])): ?>
                            <div class="mb-3">
                                <label class="form-label">Foto actual</label>
                                <div>
                                    <?php 
                                    $img = $mascota['foto_url'];
                                    if (!empty($img) && strpos($img, 'http://') !== 0 && strpos($img, 'https://') !== 0) {
                                        $img = $ROOT . ltrim($img, '/');
                                    }
                                    ?>
                                    <img src="<?= htmlspecialchars($img) ?>" 
                                         alt="Foto actual" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px; max-height: 200px;">
                                    <div class="mt-2">
                                        <a href="<?= Controller::path() ?>mascota/subir-foto?id_mascota=<?= urlencode($mascota['id_mascota']) ?>" 
                                           class="btn btn-sm btn-outline-primary me-2">Cambiar foto</a>
                                        <label class="btn btn-sm btn-outline-secondary mb-0">
                                            Subir nueva… <input type="file" name="foto" accept="image/*" hidden>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <label class="form-label">Foto</label>
                                <div>
                                    <p class="text-muted">No hay foto cargada</p>
                                    <a href="<?= Controller::path() ?>mascota/subir-foto?id_mascota=<?= urlencode($mascota['id_mascota']) ?>" 
                                       class="btn btn-sm btn-outline-primary me-2">Subir foto</a>
                                    <label class="btn btn-sm btn-outline-secondary mb-0">
                                        Subir en este formulario… <input type="file" name="foto" accept="image/*" hidden>
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Actualizar Mascota</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <a href="<?= Controller::path() ?>mascota/perfil?id=<?= urlencode($mascota['id_mascota']) ?>" class="btn btn-outline-secondary">Cancelar</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>