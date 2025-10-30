<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
<?php echo \app\controllers\SiteController::head(); ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Subir Foto</h3>
                    <a href="<?= Controller::path() ?>mascota/perfil?id=<?= urlencode($mascota['id_mascota'] ?? '') ?>" class="btn btn-outline-secondary btn-sm">Volver</a>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    
                    <div class="text-center mb-4">
                        <h5>Subir foto para: <strong><?= htmlspecialchars($mascota['nombre'] ?? 'Mascota') ?></strong></h5>
                        <p class="text-muted"><?= htmlspecialchars($mascota['especie'] ?? '') ?></p>
                    </div>
                    
                    <?php if (!empty($mascota['foto_url'])): ?>
                        <div class="mb-3 text-center">
                            <label class="form-label">Foto actual:</label>
                            <div>
                                <?php 
                                $img = $mascota['foto_url'];
                                if (!empty($img) && strpos($img, 'http://') !== 0 && strpos($img, 'https://') !== 0 && strpos($img, '/') !== 0) {
                                    $img = '/' . ltrim($img, '/');
                                }
                                ?>
                                <img src="<?= htmlspecialchars($img) ?>" 
                                     alt="Foto actual" 
                                     class="img-thumbnail" 
                                     style="max-width: 200px; max-height: 200px;">
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?= Controller::path() ?>mascota/subir-foto" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="id_mascota" value="<?= htmlspecialchars($mascota['id_mascota'] ?? '') ?>">
                        
                        <div class="mb-3">
                            <label for="foto" class="form-label">Seleccionar nueva foto</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*" required>
                            <div class="form-text">
                                Formatos permitidos: JPG, JPEG, PNG, GIF<br>
                                Tamaño máximo: 5MB
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción de la foto (opcional)</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="2" 
                                      placeholder="Describe esta foto..."></textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Subir Foto</button>
                            <a href="<?= Controller::path() ?>mascota/perfil?id=<?= urlencode($mascota['id_mascota']) ?>" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                    
                    <div class="mt-4">
                        <h6>Consejos para mejores fotos:</h6>
                        <ul class="small text-muted">
                            <li>Usa buena iluminación natural</li>
                            <li>Asegúrate de que la mascota esté en el centro</li>
                            <li>Evita fondos muy ocupados</li>
                            <li>Fotos cuadradas funcionan mejor para el perfil</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>