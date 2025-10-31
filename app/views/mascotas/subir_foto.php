<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
<?php echo \app\controllers\SiteController::head(); ?>
<section class="py-5" style="background: radial-gradient(1000px 500px at 10% 10%, rgba(13, 110, 253, .12), transparent 60%), radial-gradient(900px 500px at 90% 0%, rgba(255, 193, 7, .12), transparent 60%), linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Subir Fotos</h3>
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
                    
                    <form method="POST" action="<?= Controller::path() ?>mascota/subirfoto" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="id_mascota" value="<?= htmlspecialchars($mascota['id_mascota'] ?? '') ?>">
                        
                        <div class="mb-3">
                            <label for="fotos" class="form-label">Seleccionar fotos</label>
                            <input type="file" class="form-control" id="fotos" name="fotos[]" accept="image/*" multiple required>
                            <div class="form-text">
                                Podés seleccionar varias imágenes a la vez. Formatos: JPG, JPEG, PNG, GIF. Tamaño máximo por imagen: 5MB.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción (opcional)</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="2" maxlength="255" placeholder="Describe esta foto..."></textarea>
                            <div class="form-text">Se aplicará a todas las fotos subidas. Máximo 255 caracteres. Te quedan <span id="foto-descripcion-counter" aria-live="polite">255</span>.</div>
                        </div>
                        <script>
                        (function(){
                            var ta = document.getElementById('descripcion');
                            if (!ta) return;
                            var max = parseInt(ta.getAttribute('maxlength')) || 255;
                            var counterEl = document.getElementById('foto-descripcion-counter');
                            function update(){
                                var len = ta.value ? ta.value.length : 0;
                                var remaining = Math.max(0, max - len);
                                if (counterEl) counterEl.textContent = remaining;
                            }
                            ta.addEventListener('input', update);
                            ta.addEventListener('change', update);
                            update();
                        })();
                        </script>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Subir Fotos</button>
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
</section>