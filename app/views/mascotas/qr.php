<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
<?php echo \app\controllers\SiteController::head(); ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header text-center">
                    <h3 class="mb-0">Código QR</h3>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <h5><?= htmlspecialchars($mascota['nombre'] ?? 'Mascota') ?></h5>
                        <p class="text-muted"><?= htmlspecialchars($mascota['especie'] ?? '') ?></p>
                    </div>
                    
                    <?php 
                    // URL del perfil de la mascota con base dinámica
                    $perfilUrl = 'http://' . $_SERVER['HTTP_HOST'] . Controller::path() . 'mascota/perfil?id=' . urlencode($mascota['id_mascota']);
                    
                    // Generar QR usando una API pública
                    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($perfilUrl);
                    ?>
                    
                    <div class="mb-4">
                        <img src="<?= htmlspecialchars($qrUrl) ?>" 
                             alt="Código QR para <?= htmlspecialchars($mascota['nombre']) ?>" 
                             class="img-fluid border rounded"
                             style="max-width: 300px;">
                    </div>
                    
                    <div class="mb-3">
                        <p class="small text-muted">
                            Escanea este código QR para ver el perfil de <?= htmlspecialchars($mascota['nombre']) ?>
                        </p>
                        <code class="small"><?= htmlspecialchars($perfilUrl) ?></code>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button onclick="window.print()" class="btn btn-primary">
                            🖨️ Imprimir QR
                        </button>
                        <button onclick="descargarQR()" class="btn btn-outline-primary">
                            💾 Descargar QR
                        </button>
                        <a href="<?= Controller::path() ?>mascota/perfil?id=<?= urlencode($mascota['id_mascota']) ?>" class="btn btn-outline-secondary">
                            ← Volver al perfil
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-body">
                    <h6>¿Cómo usar el código QR?</h6>
                    <ul class="small">
                        <li>Imprime el código y pégalo en la correa o collar de tu mascota</li>
                        <li>Si alguien encuentra a tu mascota, puede escanear el código</li>
                        <li>El código llevará directamente al perfil con tu información de contacto</li>
                        <li>Asegúrate de mantener actualizada la información en el perfil</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function descargarQR() {
    const qrImg = document.querySelector('img[alt*="Código QR"]');
    const link = document.createElement('a');
    link.href = qrImg.src;
    link.download = 'qr-<?= htmlspecialchars($mascota['nombre'] ?? 'mascota') ?>.png';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Estilos para impresión
const printStyles = `
@media print {
    body * { visibility: hidden; }
    .card, .card * { visibility: visible; }
    .card { position: absolute; top: 0; left: 0; width: 100%; }
    .btn { display: none !important; }
    .mt-3 { display: none !important; }
}
`;

const style = document.createElement('style');
style.textContent = printStyles;
document.head.appendChild(style);
</script>