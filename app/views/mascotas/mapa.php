<?php
// View: Mapa de mascotas perdidas y reportes de encontradas
// Espera variables: $perdidas (array), $reportes (array)
?>
<div class="container py-4">
  <div class="d-flex align-items-center mb-3">
    <h2 class="me-auto">Mapa de mascotas perdidas y encontradas</h2>
    <div>
      <a href="/mascota/perdidas" class="btn btn-outline-primary btn-sm me-2">Ver lista de perdidas</a>
      <a href="/" class="btn btn-outline-secondary btn-sm">Inicio</a>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-12 col-lg-8">
      <div id="map" style="width:100%; height:70vh; border-radius: 8px; border:1px solid #ddd;"></div>
      <div class="mt-2 small text-muted">Fuente del mapa: © OpenStreetMap contributors</div>
    </div>
    <div class="col-12 col-lg-4">
      <div class="card mb-3">
        <div class="card-body">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="togglePerdidas" checked>
            <label class="form-check-label" for="togglePerdidas">Mostrar perdidas</label>
          </div>
          <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" role="switch" id="toggleReportes" checked>
            <label class="form-check-label" for="toggleReportes">Mostrar reportes de encontradas</label>
          </div>
          <div class="mt-3 small text-muted">
            Nota: Si algunos marcadores no aparecen es porque aún no tienen coordenadas exactas. Puedes editar o reportar ubicaciones con una dirección y el sistema intentará ubicarlas en el mapa.
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">Resumen</div>
        <ul class="list-group list-group-flush" style="max-height: 40vh; overflow: auto;">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Perdidas <span class="badge bg-danger rounded-pill"><?= isset($perdidas) ? count($perdidas) : 0 ?></span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            Reportes encontradas <span class="badge bg-success rounded-pill"><?= isset($reportes) ? count($reportes) : 0 ?></span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
(function(){
  const perdidas = <?php echo json_encode($perdidas ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
  const reportes = <?php echo json_encode($reportes ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

  // Inicializar mapa centrado en Rosario, AR (fallback)
  const map = L.map('map').setView([-32.9587, -60.6930], 12);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  const perdidasLayer = L.layerGroup().addTo(map);
  const reportesLayer = L.layerGroup().addTo(map);

  // Helpers
  const iconPerdida = L.icon({
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
    iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
  });
  const iconReporte = L.divIcon({
    className: 'custom-div-icon',
    html: '<div style="background:#198754;color:#fff;border-radius:6px;padding:2px 6px;border:1px solid #0f5132;">Encontrada</div>',
    iconSize: [80, 24], iconAnchor: [40, 12]
  });

  function popupMascota(m){
    const img = m.foto_url ? `<img src="/${m.foto_url.replace(/^\/+/, '')}" style="width:100%;max-width:220px;border-radius:6px;object-fit:cover;">` : '';
    const desc = m.descripcion ? `<div class="small text-muted mt-1">${escapeHtml(m.descripcion)}</div>` : '';
    return `<div style="max-width:240px">
      <div class="fw-bold">${escapeHtml(m.nombre || 'Mascota')}</div>
      ${img}
      ${desc}
      <a class="btn btn-sm btn-primary mt-2" href="/mascota/perfil?id=${encodeURIComponent(m.id_mascota)}">Ver perfil</a>
    </div>`;
  }

  function popupReporte(r){
    const title = r.nombre ? `<div class=\"fw-bold\">${escapeHtml(r.nombre)}</div>` : '';
    const ubic = r.ubicacion ? `<div class=\"small\"><span class=\"text-muted\">Ubicación:</span> ${escapeHtml(r.ubicacion)}</div>` : '';
    const desc = r.reporte_descripcion ? `<div class=\"small text-muted mt-1\">${escapeHtml(r.reporte_descripcion)}</div>` : '';
    return `<div style=\"max-width:260px\">${title}${ubic}${desc}
      <a class=\"btn btn-sm btn-outline-primary mt-2\" href=\"/mascota/perfil?id=${encodeURIComponent(r.id_mascota)}\">Ver mascota</a>
    </div>`;
  }

  function escapeHtml(str){
    return String(str || '').replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[s]));
  }

  // Intentar leer coordenadas de campos lat/lng o de una cadena "lat,lng"
  function getCoordsFrom(record){
    // soportar lat/lng genéricos y campos ultima_lat/ultima_lng de mascotas
    if (record.lat && record.lng && isFinite(record.lat) && isFinite(record.lng)) {
      return [Number(record.lat), Number(record.lng)];
    }
    if (record.ultima_lat && record.ultima_lng && isFinite(record.ultima_lat) && isFinite(record.ultima_lng)) {
      return [Number(record.ultima_lat), Number(record.ultima_lng)];
    }
    const cands = [record.ultima_ubicacion, record.ubicacion, record.coords];
    for (const c of cands){
      if (!c || typeof c !== 'string') continue;
      const m = c.trim().match(/^\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*$/);
      if (m) return [parseFloat(m[1]), parseFloat(m[2])];
    }
    return null;
  }

  // Geocoder sencillo contra Nominatim (respetar límites: 1 req/seg aprox)
  async function geocode(q){
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}`;
    const res = await fetch(url, { headers: { 'Accept-Language': 'es' } });
    if (!res.ok) return null;
    const data = await res.json();
    if (data && data.length) {
      return [parseFloat(data[0].lat), parseFloat(data[0].lon)];
    }
    return null;
  }

  const bounds = L.latLngBounds();
  let placed = 0;

  // Colocar perdidas (si tienen coords directas o geocodificables)
  (async function(){
    const queue = [];
    for (const m of perdidas){
      const coords = getCoordsFrom(m);
      if (coords) {
        const mk = L.marker(coords, { icon: iconPerdida }).bindPopup(popupMascota(m));
        mk.addTo(perdidasLayer);
        bounds.extend(coords);
        placed++;
      } else if (m.ubicacion) {
        queue.push({ type: 'perdida', data: m });
      }
    }
    // Geocodificar hasta 5 entradas para no abusar
    let count = 0;
    for (const item of queue){
      if (count >= 5) break;
      await new Promise(r => setTimeout(r, 1000)); // ritmo
      const c = await geocode(item.data.ubicacion);
      if (c) {
        const mk = L.marker(c, { icon: iconPerdida }).bindPopup(popupMascota(item.data));
        mk.addTo(perdidasLayer);
        bounds.extend(c);
        placed++;
      }
      count++;
    }
    fitIfAny();
  })();

  // Colocar reportes (intentar coords directas o geocodificar 'ubicacion')
  ;(async function(){
    const queue = [];
    for (const r of reportes){
      const coords = getCoordsFrom(r);
      if (coords) {
        const mk = L.marker(coords, { icon: iconReporte }).bindPopup(popupReporte(r));
        mk.addTo(reportesLayer);
        bounds.extend(coords);
        placed++;
      } else if (r.ubicacion) {
        queue.push(r);
      }
    }
    let count = 0;
    for (const r of queue){
      if (count >= 8) break; // hasta 8 geocodificaciones
      await new Promise(res => setTimeout(res, 900));
      const c = await geocode(r.ubicacion);
      if (c) {
        const mk = L.marker(c, { icon: iconReporte }).bindPopup(popupReporte(r));
        mk.addTo(reportesLayer);
        bounds.extend(c);
        placed++;
      }
      count++;
    }
    fitIfAny();
  })();

  function fitIfAny(){
    if (placed > 0) {
      map.fitBounds(bounds.pad(0.2));
    }
  }

  // Toggles de capas
  document.getElementById('togglePerdidas').addEventListener('change', (e)=>{
    if (e.target.checked) { perdidasLayer.addTo(map); } else { map.removeLayer(perdidasLayer); }
  });
  document.getElementById('toggleReportes').addEventListener('change', (e)=>{
    if (e.target.checked) { reportesLayer.addTo(map); } else { map.removeLayer(reportesLayer); }
  });
})();
</script>
