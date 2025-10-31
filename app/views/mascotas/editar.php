<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
<?php echo \app\controllers\SiteController::head(); ?>
<!-- Fondo coherente con el resto (gradiente usado en otras vistas) -->
<section class="py-5" style="background: radial-gradient(1000px 500px at 10% 10%, rgba(13, 110, 253, .12), transparent 60%), radial-gradient(900px 500px at 90% 0%, rgba(255, 193, 7, .12), transparent 60%), linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-start align-items-center gap-2">
                    <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary" title="Volver">Volver</a>
                    <h3 class="mb-0">Editar Mascota</h3>
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

                        <h6 class="text-uppercase text-muted mb-3">Información básica</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre de la mascota</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingresa el nombre" required 
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

                        <h6 class="text-uppercase text-muted mt-4 mb-3">Detalles</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="raza" class="form-label">Raza (opcional)</label>
                                    <input type="text" class="form-control" id="raza" name="raza" placeholder="Ej.: Mestizo, Labrador" 
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

                        <div class="row g-3">
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
                                    <input type="text" class="form-control" id="color" name="color" placeholder="Ej.: Marrón y blanco" 
                                           value="<?= htmlspecialchars($mascota['color'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <h6 class="text-uppercase text-muted mt-4 mb-2">Descripción</h6>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción (opcional)</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="255" placeholder="Señas particulares, comportamiento, indicaciones, etc."><?= htmlspecialchars($mascota['descripcion'] ?? '') ?></textarea>
                            <div class="form-text">Añade detalles útiles para quien la encuentre. Máximo 255 caracteres. Te quedan <span id="descripcion-counter" aria-live="polite">255</span>.</div>
                        </div>
                        <script>
                        (function() {
                            var ta = document.getElementById('descripcion');
                            if (!ta) return;
                            var max = parseInt(ta.getAttribute('maxlength')) || 255;
                            var counterEl = document.getElementById('descripcion-counter');
                            function update() {
                                var len = ta.value ? ta.value.length : 0;
                                var remaining = Math.max(0, max - len);
                                if (counterEl) counterEl.textContent = remaining;
                            }
                            ta.addEventListener('input', update);
                            ta.addEventListener('change', update);
                            update();
                        })();
                        </script>

                        <h6 class="text-uppercase text-muted mt-4 mb-2">Foto</h6>
                        <?php if (!empty($mascota['foto_url'])): ?>
                            <div class="mb-3">
                                <label class="form-label">Foto actual</label>
                                <div class="d-flex align-items-start gap-3 flex-wrap">
                                    <?php 
                                    $img = $mascota['foto_url'];
                                    if (!empty($img) && strpos($img, 'http://') !== 0 && strpos($img, 'https://') !== 0) {
                                        $img = $ROOT . ltrim($img, '/');
                                    }
                                    ?>
                                    <img src="<?= htmlspecialchars($img) ?>" 
                                         alt="Foto actual" 
                                         class="rounded border" 
                                         style="width: 160px; height: 160px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <label class="btn btn-sm btn-outline-secondary mb-2">
                                            Seleccionar imagen… <input type="file" name="foto" accept="image/*" hidden>
                                        </label>
                                        <div class="form-text">Formatos aceptados: JPG, PNG, GIF. Tamaño recomendado cuadrado.</div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <label class="form-label">Foto</label>
                                <div class="bg-light border rounded p-3">
                                    <p class="text-muted mb-2">No hay foto cargada.</p>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <label class="btn btn-sm btn-outline-secondary mb-0">
                                            Seleccionar imagen… <input type="file" name="foto" accept="image/*" hidden>
                                        </label>
                                                     <a href="<?= Controller::path() ?>mascota/subirfoto?id_mascota=<?= urlencode($mascota['id_mascota']) ?>" 
                                                         class="btn btn-sm btn-outline-primary">Subir desde otra página</a>
                                    </div>
                                    <div class="form-text mt-2">Formatos aceptados: JPG, PNG, GIF. Tamaño recomendado cuadrado.</div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="row mt-4 g-3">
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary shadow-sm">Actualizar Mascota</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <a href="<?= Controller::path() ?>mascota/perfil?id=<?= urlencode($mascota['id_mascota']) ?>" class="btn btn-outline-secondary">Cancelar</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <?php
                    // Cargar fotos guardadas en galería para esta mascota
                    $fotos = [];
                    try {
                        $fotos = DataBase::obtenerRegistros('SELECT id_foto, url, descripcion FROM fotos_mascotas WHERE id_mascota = ? ORDER BY id_foto DESC', [($mascota['id_mascota'] ?? null)]);
                    } catch (Throwable $e) {
                        $fotos = [];
                    }
                    // Calcular cupos restantes considerando principal + galería (máx. 3)
                    $norm = function($u){
                        $u = trim((string)$u);
                        if ($u === '') return '';
                        $u = str_replace('\\', '/', $u);
                        $u = strtolower($u);
                        return ltrim($u, '/');
                    };
                    $urls = [];
                    $main = $norm($mascota['foto_url'] ?? '');
                    if ($main !== '') { $urls[$main] = true; }
                    foreach ($fotos as $f) { $u = $norm($f['url'] ?? ''); if ($u !== '') { $urls[$u] = true; } }
                    $maxImgs = 3;
                    $restantes = max(0, $maxImgs - count($urls));
                    ?>

                    <hr class="my-4">
                    <h6 class="text-uppercase text-muted mb-3">Fotos guardadas</h6>

                    <?php if (empty($fotos)): ?>
                        <div class="alert alert-info small">
                            No hay fotos en la galería.
                            <?php if ($restantes > 0): ?>
                                Te quedan <?= (int)$restantes ?> lugar(es). 
                                <a href="<?= Controller::path() ?>mascota/subirfoto?id_mascota=<?= urlencode($mascota['id_mascota'] ?? '') ?>">Subir fotos</a>.
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($fotos as $foto): ?>
                                <?php
                                $imgUrl = trim((string)($foto['url'] ?? ''));
                                if ($imgUrl !== '') {
                                    $imgUrl = str_replace('\\', '/', $imgUrl);
                                    $lower = strtolower($imgUrl);
                                    $absHttp = (strpos($lower, 'http://') === 0) || (strpos($lower, 'https://') === 0) || (strpos($lower, 'data:') === 0);
                                    $isWin = preg_match('/^[a-z]:\//i', $imgUrl) === 1;
                                    if ($isWin) { $imgUrl = $ROOT . 'assets/images/avatar-placeholder.svg'; }
                                    elseif ($absHttp) { /* ok */ }
                                    elseif (strpos($imgUrl, '/') === 0) { if (strpos($imgUrl, '/assets/') === 0) { $imgUrl = $ROOT . ltrim($imgUrl, '/'); } }
                                    else { $imgUrl = $ROOT . ltrim($imgUrl, '/'); }
                                }
                                $esPrincipal = ($norm($mascota['foto_url'] ?? '') !== '' && $norm($mascota['foto_url'] ?? '') === $norm($foto['url'] ?? ''));
                                ?>
                                <div class="col-12">
                                    <div class="border rounded p-3 d-flex gap-3 align-items-start">
                                        <div class="position-relative">
                                            <img src="<?= htmlspecialchars($imgUrl) ?>" alt="Foto" class="rounded" style="width: 120px; height: 120px; object-fit: cover;">
                                            <?php if ($esPrincipal): ?>
                                                <span class="badge bg-primary position-absolute top-0 start-0 translate-middle">Principal</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <form method="POST" action="<?= Controller::path() ?>mascota/actualizarfoto" enctype="multipart/form-data" class="row g-2 align-items-end">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                                <input type="hidden" name="id_foto" value="<?= (int)$foto['id_foto'] ?>">
                                                <input type="hidden" name="id_mascota" value="<?= htmlspecialchars($mascota['id_mascota'] ?? '') ?>">
                                                <div class="col-md-6">
                                                    <label class="form-label small mb-1">Descripción</label>
                                                    <input type="text" name="descripcion" maxlength="255" class="form-control form-control-sm" value="<?= htmlspecialchars($foto['descripcion'] ?? '') ?>" placeholder="Descripción de la foto (opcional)">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small mb-1">Reemplazar imagen</label>
                                                    <input type="file" name="nueva_foto" accept="image/*" class="form-control form-control-sm">
                                                </div>
                                                <div class="col-md-2 d-grid">
                                                    <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                                                </div>
                                            </form>
                                            <div class="d-flex gap-2 mt-2">
                                                <?php if (!$esPrincipal): ?>
                                                <form method="POST" action="<?= Controller::path() ?>mascota/hacerprincipal" onsubmit="return confirm('¿Hacer esta foto la principal?');">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                                    <input type="hidden" name="id_foto" value="<?= (int)$foto['id_foto'] ?>">
                                                    <input type="hidden" name="id_mascota" value="<?= htmlspecialchars($mascota['id_mascota'] ?? '') ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Hacer principal</button>
                                                </form>
                                                <?php endif; ?>
                                                <form method="POST" action="<?= Controller::path() ?>mascota/eliminarfoto" onsubmit="return confirm('¿Eliminar esta foto? Esta acción no se puede deshacer.');">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                                    <input type="hidden" name="id_foto" value="<?= (int)$foto['id_foto'] ?>">
                                                    <input type="hidden" name="id_mascota" value="<?= htmlspecialchars($mascota['id_mascota'] ?? '') ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($restantes > 0): ?>
                            <div class="alert alert-light border mt-3 small">Te quedan <?= (int)$restantes ?> lugar(es) en el carrusel. <a href="<?= Controller::path() ?>mascota/subirfoto?id_mascota=<?= urlencode($mascota['id_mascota'] ?? '') ?>">Subir más fotos</a>.</div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</section>