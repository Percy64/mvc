<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
<?php echo \app\controllers\SiteController::head(); ?>
<section class="py-5" style="background: radial-gradient(1000px 500px at 10% 10%, rgba(13, 110, 253, .12), transparent 60%), radial-gradient(900px 500px at 90% 0%, rgba(255, 193, 7, .12), transparent 60%), linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-start align-items-center gap-2">
                    <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary" title="Volver">Volver</a>
                    <h3 class="mb-0">Registrar Nueva Mascota</h3>
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
                    
                    <form method="POST" action="<?= Controller::path() ?>mascota/store" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre de la mascota</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required 
                                           value="<?= htmlspecialchars($data['nombre'] ?? '') ?>"
                                           placeholder="Ej: Max, Luna, Toby">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="especie" class="form-label">Especie</label>
                                    <select class="form-control" id="especie" name="especie" required>
                                        <option value="">Seleccionar especie</option>
                                        <option value="Perro" <?= (($data['especie'] ?? '') === 'Perro') ? 'selected' : '' ?>>Perro</option>
                                        <option value="Gato" <?= (($data['especie'] ?? '') === 'Gato') ? 'selected' : '' ?>>Gato</option>
                                        <option value="Conejo" <?= (($data['especie'] ?? '') === 'Conejo') ? 'selected' : '' ?>>Conejo</option>
                                        <option value="Hamster" <?= (($data['especie'] ?? '') === 'Hamster') ? 'selected' : '' ?>>Hamster</option>
                                        <option value="Ave" <?= (($data['especie'] ?? '') === 'Ave') ? 'selected' : '' ?>>Ave</option>
                                        <option value="Pez" <?= (($data['especie'] ?? '') === 'Pez') ? 'selected' : '' ?>>Pez</option>
                                        <option value="Otro" <?= (($data['especie'] ?? '') === 'Otro') ? 'selected' : '' ?>>Otro</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="raza" class="form-label">Raza (opcional)</label>
                                    <input type="text" class="form-control" id="raza" name="raza" 
                                           value="<?= htmlspecialchars($data['raza'] ?? '') ?>"
                                           placeholder="Ej: Golden Retriever, Persa">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edad" class="form-label">Edad (opcional)</label>
                                    <input type="number" min="0" step="1" class="form-control" id="edad" name="edad" 
                                           value="<?= htmlspecialchars($data['edad'] ?? '') ?>"
                                           placeholder="Ej: 2">
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
                                        <option value="Macho" <?= (($data['sexo'] ?? '') === 'Macho') ? 'selected' : '' ?>>Macho</option>
                                        <option value="Hembra" <?= (($data['sexo'] ?? '') === 'Hembra') ? 'selected' : '' ?>>Hembra</option>
                                        <option value="Otro" <?= (($data['sexo'] ?? '') === 'Otro') ? 'selected' : '' ?>>Otro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="color" class="form-label">Color (opcional)</label>
                                    <input type="text" class="form-control" id="color" name="color" 
                                           value="<?= htmlspecialchars($data['color'] ?? '') ?>"
                                           placeholder="Ej: Marrón, Blanco y negro">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción (opcional)</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="255"
                                      placeholder="Describe las características especiales de tu mascota..."><?= htmlspecialchars($data['descripcion'] ?? '') ?></textarea>
                            <div class="form-text">Máximo 255 caracteres. Te quedan <span id="descripcion-counter" aria-live="polite">255</span>.</div>
                        </div>
                        <script>
                        (function(){
                            var ta = document.getElementById('descripcion');
                            if (!ta) return;
                            var max = parseInt(ta.getAttribute('maxlength')) || 255;
                            var counterEl = document.getElementById('descripcion-counter');
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
                        
                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto de la mascota (opcional)</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                            <div class="form-text">Formatos permitidos: JPG, PNG, GIF. Máximo 5MB.</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Registrar Mascota</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <a href="<?= Controller::path() ?>mascotgit a" class="btn btn-outline-secondary" onclick="if (document.referrer) { history.back(); return false; }">Cancelar</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</section>