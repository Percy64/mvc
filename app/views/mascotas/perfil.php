<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
<?php echo \app\controllers\SiteController::head(); ?>

<!-- Hero section con gradiente -->
<section class="py-5" style="background: radial-gradient(1000px 500px at 10% 10%, rgba(13, 110, 253, .12), transparent 60%), radial-gradient(900px 500px at 90% 0%, rgba(255, 193, 7, .12), transparent 60%), linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
    <div class="container py-4">
        <?php if ($mascota): ?>
        <?php
        // Calcular cantidad de imágenes existentes (principal + galería) para mostrar mensajes y bloquear 'Agregar fotos' al llegar al máximo
        $MAX_IMGS = 3;
        $norm = function($u){
            $u = trim((string)$u);
            if ($u === '') return '';
            $u = str_replace('\\', '/', $u);
            $u = strtolower($u);
            return ltrim($u, '/');
        };
        $existing = [];
        $main = $norm($mascota['foto_url'] ?? '');
        if ($main !== '') { $existing[$main] = true; }
        try {
            $galCountRows = DataBase::obtenerRegistros('SELECT url FROM fotos_mascotas WHERE id_mascota = ?', [$mascota['id_mascota']]);
        } catch (Throwable $e) {
            $galCountRows = [];
        }
        foreach ($galCountRows as $r) { $u = $norm($r['url'] ?? ''); if ($u !== '') { $existing[$u] = true; } }
        $totalImgs = count($existing);
        $restantes = max(0, $MAX_IMGS - $totalImgs);
        $tieneMax = ($restantes <= 0);
        ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="display-6 fw-bold mb-0">
                <span class="fs-4">🐾</span> <?= htmlspecialchars($mascota['nombre'] ?? 'Sin nombre') ?>
                <?php if (isset($mascota['perdido']) && $mascota['perdido']): ?>
                    <span class="badge bg-danger ms-2">
                        <span class="fs-6">🚨</span> PERDIDA
                    </span>
                <?php endif; ?>
            </h1>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="<?= Controller::path() ?>" class="btn btn-outline-primary btn-sm">
                    <span class="fs-6">🏠</span> Inicio
                </a>
                <a href="<?= Controller::path() ?>usuario/perfil" class="btn btn-outline-secondary btn-sm">
                    <span class="fs-6">↩️</span> Mi perfil
                </a>
                <?php if (isset($_SESSION['id']) && ($_SESSION['id'] == ($mascota['usuario_id'] ?? $mascota['id']))): ?>
                    <a href="<?= Controller::path() ?>mascota/editar?id=<?= urlencode($mascota['id_mascota']) ?>" class="btn btn-warning btn-sm">
                        <span class="fs-6">✏️</span> Editar
                    </a>
                    <?php if ($tieneMax): ?>
                        <a href="#" class="btn btn-primary btn-sm" onclick="alert('Esta mascota ya tiene el máximo de 3 fotos en su perfil.'); return false;">
                            <span class="fs-6">📷</span> Agregar fotos
                        </a>
                    <?php else: ?>
                        <a href="<?= Controller::path() ?>mascota/subirfoto?id_mascota=<?= urlencode($mascota['id_mascota']) ?>" class="btn btn-primary btn-sm">
                            <span class="fs-6">📷</span> Agregar fotos
                        </a>
                    <?php endif; ?>
                    <?php 
                    $esPerdida = isset($mascota['perdido']) && $mascota['perdido'];
                    ?>
                    <?php if (!$esPerdida): ?>
                        <form method="POST" action="<?= Controller::path() ?>mascota/marcarperdida" onsubmit="return confirm('¿Seguro que deseas marcar esta mascota como perdida? Esto la destacará en las búsquedas.');" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                            <input type="hidden" name="id_mascota" value="<?= htmlspecialchars($mascota['id_mascota']) ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <span class="fs-6">🚨</span> Reportar perdida
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="<?= Controller::path() ?>mascota/marcarencontrada" onsubmit="return confirm('¿Seguro que deseas marcar esta mascota como encontrada?');" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                            <input type="hidden" name="id_mascota" value="<?= htmlspecialchars($mascota['id_mascota']) ?>">
                            <button type="submit" class="btn btn-success btn-sm">
                                <span class="fs-6">✅</span> Marcar encontrada
                            </button>
                        </form>
                    <?php endif; ?>
                    <form method="POST" action="<?= Controller::path() ?>mascota/eliminar" onsubmit="return confirm('¿Seguro que deseas eliminar esta mascota? Esta acción no se puede deshacer.');" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                        <input type="hidden" name="id_mascota" value="<?= htmlspecialchars($mascota['id_mascota']) ?>">
                        <button type="submit" class="btn btn-danger btn-sm">
                            <span class="fs-6">🗑️</span> Eliminar
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (isset($mascota['perdido']) && $mascota['perdido']): ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-3">
                <div class="flex-shrink-0">
                    <span class="fs-1">🚨</span>
                </div>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-2">
                        <span class="fs-6">⚠️</span> Esta mascota está reportada como PERDIDA
                    </h5>
                    <p class="mb-2">
                        Si has visto a <strong><?= htmlspecialchars($mascota['nombre'] ?? 'esta mascota') ?></strong> 
                        o tienes información sobre su paradero, por favor contacta inmediatamente al propietario.
                    </p>
                    <hr class="my-2">
                    <p class="mb-0 small">
                        <strong>¿Cómo ayudar?</strong> Revisa las fotos, verifica la información y usa los datos de contacto del propietario para reportar cualquier avistamiento.
                    </p>
                </div>
            </div>
        </div>

        <!-- Mapa de última ubicación reportada -->
        <?php 
            $uTxt = $mascota['ultima_ubicacion'] ?? null;
            $uLat = isset($mascota['ultima_lat']) ? (float)$mascota['ultima_lat'] : null;
            $uLng = isset($mascota['ultima_lng']) ? (float)$mascota['ultima_lng'] : null;
            $hasCoords = is_numeric($mascota['ultima_lat'] ?? null) && is_numeric($mascota['ultima_lng'] ?? null);
        ?>
        <?php if ($uTxt || $hasCoords): ?>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <span class="fs-5">📍</span> Última ubicación reportada
                </h6>
                <a class="btn btn-sm btn-outline-primary" href="<?= Controller::path() ?>mascota/mapa">Ver en mapa general</a>
            </div>
            <div class="card-body">
                <?php if ($uTxt): ?>
                <p class="small mb-2"><strong>Referencia:</strong> <?= htmlspecialchars($uTxt) ?></p>
                <?php endif; ?>
                <div id="map-ultima" style="width:100%; height:320px; border-radius: 8px; border:1px solid #ddd;"></div>
            </div>
        </div>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
        (function(){
            const hasCoords = <?= $hasCoords ? 'true' : 'false' ?>;
            const lat = <?= $hasCoords ? json_encode($uLat) : 'null' ?>;
            const lng = <?= $hasCoords ? json_encode($uLng) : 'null' ?>;
            const ubicText = <?= json_encode($uTxt, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
            const map = L.map('map-ultima').setView([-32.9587, -60.6930], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
            function putMarker(c){
                const mk = L.marker(c).addTo(map);
                mk.bindPopup(ubicText ? ('<div class="small">' + escapeHtml(ubicText) + '</div>') : 'Última ubicación reportada');
                map.setView(c, 15);
            }
            function escapeHtml(str){ return String(str || '').replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[s])); }
            async function geocode(q){
                const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}`;
                const res = await fetch(url, { headers: { 'Accept-Language': 'es' } });
                if (!res.ok) return null; const data = await res.json();
                if (data && data.length) return [parseFloat(data[0].lat), parseFloat(data[0].lon)];
                return null;
            }
            (async function(){
                if (hasCoords) { putMarker([lat, lng]); return; }
                if (ubicText) {
                    const c = await geocode(ubicText);
                    if (c) { putMarker(c); }
                }
            })();
        })();
        </script>
        <?php endif; ?>

        <?php if (isset($_SESSION['id']) && ($_SESSION['id'] == ($mascota['usuario_id'] ?? $mascota['id'])) && ($mascota['perdido'] ?? 0)): ?>
        <!-- Formulario para actualizar última ubicación (solo propietario) -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                    <span class="fs-5">🗺️</span> Actualizar ubicación donde se perdió
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= Controller::path() ?>mascota/actualizarubicacionperdida" class="row g-3">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <input type="hidden" name="id_mascota" value="<?= htmlspecialchars($mascota['id_mascota']) ?>">
                    <div class="col-12">
                        <label class="form-label small">Referencia de lugar (dirección o punto)</label>
                        <input type="text" name="ultima_ubicacion" value="<?= htmlspecialchars($uTxt ?? '') ?>" class="form-control form-control-sm" placeholder="Ej: San Martín 1234, Rosario" />
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Latitud</label>
                        <input type="text" name="ultima_lat" id="ult-lat" value="<?= htmlspecialchars($uLat ?? '') ?>" class="form-control form-control-sm" placeholder="-32.95">
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Longitud</label>
                        <input type="text" name="ultima_lng" id="ult-lng" value="<?= htmlspecialchars($uLng ?? '') ?>" class="form-control form-control-sm" placeholder="-60.64">
                    </div>
                    <div class="col-12 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="ultGeo()">Usar mi ubicación</button>
                        <button type="submit" class="btn btn-primary btn-sm">Guardar ubicación</button>
                    </div>
                </form>
                <script>
                function ultGeo(){
                    if (!navigator.geolocation) { alert('Geolocalización no disponible'); return; }
                    navigator.geolocation.getCurrentPosition(function(pos){
                        try {
                            document.getElementById('ult-lat').value = pos.coords.latitude.toFixed(6);
                            document.getElementById('ult-lng').value = pos.coords.longitude.toFixed(6);
                        } catch(e){}
                    }, function(err){
                        alert('No se pudo obtener tu ubicación (' + (err && err.message ? err.message : 'error') + ')');
                    }, { enableHighAccuracy: true, timeout: 8000, maximumAge: 60000 });
                }
                </script>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card h-100 shadow-sm border-0 overflow-hidden">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <?php 
                                // Normalizar URL de imagen principal de la mascota
                                $img = trim($mascota['foto_url'] ?? '');
                                if ($img !== '') {
                                    $img = str_replace('\\', '/', $img);
                                    $lower = strtolower($img);
                                    $isAbsoluteHttp = (strpos($lower, 'http://') === 0) || (strpos($lower, 'https://') === 0) || (strpos($lower, 'data:') === 0);
                                    $isWindowsPath = preg_match('/^[a-z]:\//i', $img) === 1;
                                    if ($isWindowsPath) {
                                        $img = '';
                                    } elseif ($isAbsoluteHttp) {
                                        // Dejar tal cual
                                    } elseif (strpos($img, '/') === 0) {
                                        if (strpos($img, '/assets/') === 0) {
                                            $img = $ROOT . ltrim($img, '/');
                                        }
                                    } else {
                                        $img = $ROOT . ltrim($img, '/');
                                    }
                                }
                                if ($img === '') {
                                    $img = $ROOT . 'assets/images/avatar-placeholder.svg';
                                }
                                ?>
                                <div class="position-relative d-inline-block w-100">
                                    <div class="bg-white border rounded-4 shadow-sm p-2">
                                        <?php
                                        // Construir slides para carrusel (principal + galería)
                                        $slides = [];
                                        $mainRaw = trim((string)($mascota['foto_url'] ?? ''));
                                        $mainNorm = $img; // ya normalizado arriba
                                        $placeholder = $ROOT . 'assets/images/avatar-placeholder.svg';
                                        if ($mainRaw !== '' && $mainNorm !== $placeholder) {
                                            $slides[] = ['url' => $mainNorm, 'desc' => ''];
                                        }
                                        try {
                                            $slidesData = DataBase::obtenerRegistros('SELECT id_foto, url, descripcion FROM fotos_mascotas WHERE id_mascota = ? ORDER BY id_foto DESC', [$mascota['id_mascota']]);
                                        } catch (Throwable $e) {
                                            $slidesData = [];
                                        }
                                        foreach ($slidesData as $f) {
                                            $fUrl = trim((string)($f['url'] ?? ''));
                                            if ($fUrl === '') continue;
                                            $norm = str_replace('\\', '/', $fUrl);
                                            $lower = strtolower($norm);
                                            $isAbs = (strpos($lower, 'http://') === 0) || (strpos($lower, 'https://') === 0) || (strpos($lower, 'data:') === 0);
                                            $isWin = preg_match('/^[a-z]:\//i', $norm) === 1;
                                            if ($isWin) { $norm = ''; }
                                            elseif ($isAbs) { /* ok */ }
                                            elseif (strpos($norm, '/') === 0) { if (strpos($norm, '/assets/') === 0) { $norm = $ROOT . ltrim($norm, '/'); } }
                                            else { $norm = $ROOT . ltrim($norm, '/'); }
                                            if ($norm === '') { $norm = $placeholder; }
                                            if ($norm === $mainNorm) continue; // evitar duplicado de principal
                                            $slides[] = ['url' => $norm, 'desc' => (string)($f['descripcion'] ?? '')];
                                        }
                                        // Limitar a máximo 3 imágenes en el carrusel
                                        if (count($slides) > 3) {
                                            $slides = array_slice($slides, 0, 3);
                                        }
                                        ?>
                                        <?php if (!empty($slides)): ?>
                                            <?php $carouselId = 'petCarousel-' . (int)$mascota['id_mascota']; ?>
                                            <style>
                                            /* Altura fija del carrusel por slide para unificar medidas */
                                            #<?= $carouselId ?> .carousel-item { height: 300px; }
                                            #<?= $carouselId ?> .carousel-item img { width: 100%; height: 100%; object-fit: cover; }
                                            @media (max-width: 768px) { #<?= $carouselId ?> .carousel-item { height: 240px; } }
                                            @media (max-width: 576px) { #<?= $carouselId ?> .carousel-item { height: 200px; } }
                                            </style>
                                            <div id="<?= $carouselId ?>" class="carousel slide" data-bs-ride="carousel">
                                                <?php if (count($slides) > 1): ?>
                                                <div class="carousel-indicators">
                                                    <?php foreach ($slides as $i => $_): ?>
                                                        <button type="button" data-bs-target="#<?= $carouselId ?>" data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>" aria-current="<?= $i === 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $i+1 ?>"></button>
                                                    <?php endforeach; ?>
                                                </div>
                                                <?php endif; ?>
                                                <div class="carousel-inner rounded-3">
                                                    <?php foreach ($slides as $i => $s): ?>
                                                        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                                                            <img src="<?= htmlspecialchars($s['url']) ?>" class="d-block w-100 h-100" alt="Slide" style="object-fit: cover;" onerror="this.src='<?= $placeholder ?>'">
                                                            <?php if (!empty($s['desc'])): ?>
                                                            <div class="carousel-caption d-none d-md-block">
                                                                <p class="small bg-dark bg-opacity-50 rounded px-2 py-1"><?= htmlspecialchars($s['desc']) ?></p>
                                                            </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <?php if (count($slides) > 1): ?>
                                                <button class="carousel-control-prev" type="button" data-bs-target="#<?= $carouselId ?>" data-bs-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Anterior</span>
                                                </button>
                                                <button class="carousel-control-next" type="button" data-bs-target="#<?= $carouselId ?>" data-bs-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Siguiente</span>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <img src="<?= htmlspecialchars($img) ?>" class="rounded-3 w-100" alt="<?= htmlspecialchars($mascota['nombre'] ?? '') ?>" style="height: 300px; object-fit: cover;" onerror="this.src='<?= $ROOT ?>assets/images/avatar-placeholder.svg'">
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($mascota['especie'])): ?>
                                        <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-primary">
                                            <?= htmlspecialchars($mascota['especie']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                                    <span class="fs-4">📝</span> Información
                                </h5>
                                <div class="bg-light rounded-3 p-3 mb-3">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <div class="fs-5 mb-1">🐕</div>
                                                <small class="text-muted d-block">Especie</small>
                                                <strong class="small"><?= htmlspecialchars($mascota['especie'] ?? 'No especificada') ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <div class="fs-5 mb-1">🔖</div>
                                                <small class="text-muted d-block">Raza</small>
                                                <strong class="small"><?= htmlspecialchars($mascota['raza'] ?? 'No especificada') ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <div class="fs-5 mb-1">⏰</div>
                                                <small class="text-muted d-block">Edad</small>
                                                <strong class="small"><?= htmlspecialchars($mascota['edad'] ?? 'No especificada') ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <div class="fs-5 mb-1">📅</div>
                                                <small class="text-muted d-block">Registrado</small>
                                                <strong class="small"><?= isset($mascota['fecha_registro']) ? date('d/m/Y', strtotime($mascota['fecha_registro'])) : 'N/A' ?></strong>
                                            </div>
                                        </div>
                                        <?php if (isset($mascota['perdido']) && $mascota['perdido']): ?>
                                        <div class="col-12">
                                            <div class="text-center">
                                                <div class="fs-5 mb-1">🚨</div>
                                                <small class="text-muted d-block">Estado</small>
                                                <span class="badge bg-danger">PERDIDA</span>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <?php if (!empty($mascota['descripcion'])): ?>
                                    <div class="bg-light rounded-3 p-3">
                                        <h6 class="fw-bold mb-2 d-flex align-items-center gap-2">
                                            <span class="fs-6">💭</span> Descripción
                                        </h6>
                                        <p class="text-muted mb-0 small"><?= htmlspecialchars($mascota['descripcion']) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
            <!-- Información del propietario -->
            <?php 
            $ownerId = $mascota['usuario_id'] ?? $mascota['id'] ?? null;
            $owner = null;
            if ($ownerId) {
                try {
                    $owner = DataBase::obtenerRegistro('SELECT id, nombre, apellido, email, telefono, direccion, foto_url FROM usuarios WHERE id = ?', [$ownerId]);
                } catch (Throwable $e) {
                    $owner = null;
                }
            }
            ?>
            
            <?php if ($owner): ?>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <span class="fs-5">👤</span> Propietario
                    </h6>
                </div>
                <div class="card-body text-center p-4">
                    <?php if (!empty($owner['foto_url'])): ?>
                        <?php 
                        // Normalizar URL de foto del propietario usando la misma lógica que en usuario/perfil.php
                        $ownerUrl = trim((string)$owner['foto_url']);
                        $ownerUrl = str_replace('\\', '/', $ownerUrl);
                        $lower = strtolower($ownerUrl);
                        $isAbsoluteHttp = (strpos($lower, 'http://') === 0) || (strpos($lower, 'https://') === 0) || (strpos($lower, 'data:') === 0);
                        $isWindowsPath = preg_match('/^[a-z]:\//i', $ownerUrl) === 1;
                        if ($isWindowsPath) {
                            $ownerUrl = $ROOT . 'assets/images/avatar-placeholder.svg';
                        } elseif ($isAbsoluteHttp) {
                            // OK
                        } else {
                            $p = ltrim($ownerUrl, '/');
                            if (strpos($p, 'public/assets/') === 0) {
                                $ownerUrl = $BASE . substr($p, strlen('public/'));
                            } elseif (strpos($p, 'assets/usuarios/') === 0) {
                                $ownerUrl = $BASE . 'public/' . $p; // Corregido como en usuario/perfil
                            } elseif (strpos($ownerUrl, '/assets/usuarios/') === 0) {
                                $ownerUrl = $BASE . 'public' . $ownerUrl; // Corregido como en usuario/perfil
                            } elseif (strpos($p, 'assets/images/usuarios/') === 0) {
                                $ownerUrl = $ROOT . $p;
                            } elseif (strpos($p, 'assets/') === 0) {
                                $ownerUrl = $ROOT . $p;
                            } else {
                                $ownerUrl = $ROOT . $p;
                            }
                        }
                        ?>
                        <div class="position-relative d-inline-block mb-3">
                            <div class="bg-white border rounded-4 shadow-sm p-2">
                                <img src="<?= htmlspecialchars($ownerUrl) ?>" 
                                     alt="Foto de <?= htmlspecialchars($owner['nombre']) ?>" 
                                     class="rounded-3" 
                                     style="width: 100px; height: 100px; object-fit: cover;"
                                     onerror="this.src='<?= $ROOT ?>assets/images/avatar-placeholder.svg'">
                            </div>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                                <span class="fs-6">✓</span>
                            </span>
                        </div>
                    <?php else: ?>
                        <div class="position-relative d-inline-block mb-3">
                            <div class="bg-primary rounded-4 d-inline-flex align-items-center justify-content-center shadow-sm" 
                                 style="width: 100px; height: 100px;">
                                <span class="text-white fs-2 fw-bold">
                                    <?= strtoupper(substr($owner['nombre'] ?? 'U', 0, 1)) ?>
                                </span>
                            </div>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                                <span class="fs-6">📷</span>
                            </span>
                        </div>
                    <?php endif; ?>
                    <h6 class="fw-bold mb-3"><?= htmlspecialchars($owner['nombre'] ?? '') ?> <?= htmlspecialchars($owner['apellido'] ?? '') ?></h6>
                    
                    <!-- Información de contacto -->
                    <div class="bg-light rounded-3 p-3 mb-3">
                        <div class="row g-2 text-start">
                            <div class="col-12">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="fs-6">📧</span>
                                    <div>
                                        <small class="text-muted d-block">Email</small>
                                        <a href="mailto:<?= htmlspecialchars($owner['email'] ?? '') ?>" 
                                           class="text-decoration-none small fw-medium">
                                            <?= htmlspecialchars($owner['email'] ?? '') ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (!empty($owner['telefono'])): ?>
                            <div class="col-12">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="fs-6">📞</span>
                                    <div>
                                        <small class="text-muted d-block">Teléfono</small>
                                        <a href="tel:<?= htmlspecialchars($owner['telefono']) ?>" 
                                           class="text-decoration-none small fw-medium">
                                            <?= htmlspecialchars($owner['telefono']) ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="col-12">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="fs-6">💬</span>
                                    <div>
                                        <small class="text-muted d-block">WhatsApp</small>
                                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $owner['telefono']) ?>?text=Hola%20<?= urlencode($owner['nombre']) ?>,%20vi%20a%20tu%20mascota%20<?= urlencode($mascota['nombre']) ?>%20en%20la%20plataforma" 
                                           class="text-decoration-none small fw-medium" target="_blank" rel="noopener">
                                            <?= htmlspecialchars($owner['telefono']) ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (!empty($owner['direccion'])): ?>
                            <div class="col-12">
                                <div class="d-flex align-items-start gap-2">
                                    <span class="fs-6">📍</span>
                                    <div>
                                        <small class="text-muted d-block">Dirección</small>
                                        <span class="small fw-medium"><?= htmlspecialchars($owner['direccion']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Botones de contacto rápido -->
                    <div class="d-grid gap-2">
                        <a href="mailto:<?= htmlspecialchars($owner['email'] ?? '') ?>?subject=Consulta sobre <?= urlencode($mascota['nombre'] ?? 'tu mascota') ?>&body=Hola, me pongo en contacto contigo por tu mascota <?= urlencode($mascota['nombre'] ?? '') ?>." 
                           class="btn btn-primary btn-sm">
                            <span class="fs-6">📧</span> Enviar Email
                        </a>
                        <?php if (!empty($owner['telefono'])): ?>
                        <a href="tel:<?= htmlspecialchars($owner['telefono']) ?>" 
                           class="btn btn-success btn-sm">
                            <span class="fs-6">📞</span> Llamar
                        </a>
                        <?php endif; ?>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $owner['telefono']) ?>?text=Hola%20<?= urlencode($owner['nombre']) ?>,%20vi%20a%20tu%20mascota%20<?= urlencode($mascota['nombre']) ?>%20en%20la%20plataforma.%20Me%20gustar%C3%ADa%20hacer%20una%20consulta." 
                           class="btn btn-success btn-sm" target="_blank" rel="noopener">
                            <span class="fs-6">💬</span> Contactar por WhatsApp
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($mascota['perdido']) && $mascota['perdido']): ?>
            <!-- Información adicional para encontrar mascotas -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <span class="fs-5">🆘</span> ¿Encontraste esta mascota?
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="text-center">
                        <div class="mb-3">
                            <span class="fs-1">🐾</span>
                        </div>
                        <p class="small text-muted mb-3">
                            Si has encontrado a <strong><?= htmlspecialchars($mascota['nombre'] ?? 'esta mascota') ?></strong>, 
                            puedes contactar directamente con su dueño usando la información de arriba.
                        </p>
                        <div class="bg-warning bg-opacity-10 border border-warning rounded-3 p-3">
                            <h6 class="text-warning-emphasis mb-2">
                                <span class="fs-6">⚠️</span> Importante
                            </h6>
                            <ul class="small text-warning-emphasis text-start mb-0">
                                <li>Verifica que sea realmente la mascota comparando con las fotos</li>
                                <li>Contacta al propietario lo antes posible</li>
                                <li>Mantén a la mascota en un lugar seguro</li>
                                <li>Si no puedes contactar al dueño, lleva la mascota a un veterinario</li>
                            </ul>
                        </div>
                        
                        <!-- Reportar que encontraste la mascota: con ubicación opcional -->
                        <div class="mt-3">
                            <button class="btn btn-success btn-sm mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#frmReporteEncontrada" aria-expanded="false" aria-controls="frmReporteEncontrada">
                                <span class="fs-6">✅</span> ¡La encontré! (agregar ubicación)
                            </button>
                            <div class="collapse" id="frmReporteEncontrada">
                                <div class="card card-body p-3 text-start">
                                    <form method="POST" action="<?= Controller::path() ?>mascota/reportarencontrada" class="">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                        <input type="hidden" name="id_mascota" value="<?= htmlspecialchars($mascota['id_mascota']) ?>">
                                        <div class="mb-2">
                                            <label class="form-label small">Ubicación (dirección o referencia)</label>
                                            <input type="text" name="ubicacion" class="form-control form-control-sm" placeholder="Ej: Bv. Oroño y Córdoba, Rosario">
                                        </div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <label class="form-label small">Latitud</label>
                                                <input type="text" name="lat" id="rep-lat" class="form-control form-control-sm" placeholder="-32.95">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small">Longitud</label>
                                                <input type="text" name="lng" id="rep-lng" class="form-control form-control-sm" placeholder="-60.64">
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small">Descripción (opcional)</label>
                                            <textarea name="descripcion" class="form-control form-control-sm" rows="2" placeholder="Ej: La vi corriendo por el parque, parece asustada"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small">Tu contacto (opcional)</label>
                                            <input type="text" name="contacto" class="form-control form-control-sm" placeholder="Ej: 341-5551212 o email">
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="geoUbicacion()">Usar mi ubicación</button>
                                            <button type="submit" class="btn btn-primary btn-sm">Enviar reporte</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <script>
                            function geoUbicacion(){
                                if (!navigator.geolocation) { alert('Geolocalización no disponible'); return; }
                                navigator.geolocation.getCurrentPosition(function(pos){
                                    try {
                                        document.getElementById('rep-lat').value = pos.coords.latitude.toFixed(6);
                                        document.getElementById('rep-lng').value = pos.coords.longitude.toFixed(6);
                                    } catch(e){ /* ignore */ }
                                }, function(err){
                                    alert('No se pudo obtener tu ubicación (' + (err && err.message ? err.message : 'error') + ')');
                                }, { enableHighAccuracy: true, timeout: 8000, maximumAge: 60000 });
                            }
                            </script>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Acciones -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <span class="fs-5">⚡</span> Acciones
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-3">
                        <a href="<?= Controller::path() ?>mascota/qr?id=<?= urlencode($mascota['id_mascota']) ?>" 
                           class="btn btn-outline-info d-flex align-items-center gap-2">
                            <span class="fs-6">🔖</span> Generar QR
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="alert alert-danger border-0 shadow-sm">
                <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                    <span class="fs-2">😿</span>
                    <div>
                        <h4 class="mb-1">Mascota no encontrada</h4>
                        <p class="mb-0">La mascota que buscas no existe o no está disponible.</p>
                    </div>
                </div>
                <a href="<?= Controller::path() ?>mascota" class="btn btn-primary">
                    <span class="fs-6">🔍</span> Ver todas las mascotas
                </a>
            </div>
        </div>
    <?php endif; ?>
    </div>
</section>