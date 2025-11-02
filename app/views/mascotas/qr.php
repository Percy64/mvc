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
                    // Obtener la IPv4 de la máquina local
                    function obtenerIPv4Maquina() {
                        $ipv4 = null;
                        
                        // Método 1: Obtener IP del servidor web si es válida
                        $serverIP = $_SERVER['SERVER_ADDR'] ?? null;
                        if ($serverIP && filter_var($serverIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && $serverIP !== '127.0.0.1') {
                            return $serverIP;
                        }
                        
                        // Método 2: Obtener IPv4 directamente de la interfaz de red de la máquina
                        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                            // Windows: usar ipconfig para obtener IPv4 de la máquina
                            $output = shell_exec('ipconfig 2>nul');
                            if ($output) {
                                // Buscar adaptadores activos con IPv4
                                $lines = explode("\n", $output);
                                $currentAdapter = '';
                                $foundValidIP = false;
                                
                                foreach ($lines as $line) {
                                    $line = trim($line);
                                    
                                    // Identificar adaptador
                                    if (strpos($line, 'adaptador') !== false || strpos($line, 'adapter') !== false) {
                                        $currentAdapter = $line;
                                        continue;
                                    }
                                    
                                    // Buscar IPv4
                                    if (preg_match('/IPv4[^:]*:\s*(\d+\.\d+\.\d+\.\d+)/', $line, $matches)) {
                                        $ip = $matches[1];
                                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && $ip !== '127.0.0.1') {
                                            // Priorizar redes privadas típicas
                                            if (preg_match('/^(192\.168\.|10\.|172\.(1[6-9]|2[0-9]|3[01])\.)/', $ip)) {
                                                return $ip; // Retornar inmediatamente si es red privada
                                            } elseif (!$ipv4) {
                                                $ipv4 = $ip; // Guardar como opción si no hay mejor
                                            }
                                        }
                                    }
                                }
                            }
                            
                            // Método alternativo para Windows: usar wmic
                            if (!$ipv4) {
                                $output = shell_exec('wmic NetworkAdapter where "NetConnectionStatus=2" get NetConnectionID /format:list 2>nul');
                                if ($output) {
                                    $output = shell_exec('wmic NetworkAdapterConfiguration where "IPEnabled=true and DHCPEnabled=true" get IPAddress /format:list 2>nul');
                                    if ($output && preg_match('/IPAddress=\{?"?([0-9.]+)/', $output, $matches)) {
                                        $ip = $matches[1];
                                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && $ip !== '127.0.0.1') {
                                            $ipv4 = $ip;
                                        }
                                    }
                                }
                            }
                        } else {
                            // Linux/Mac: obtener IPv4 de la máquina
                            $commands = [
                                // Obtener IP de la ruta por defecto (más confiable)
                                "ip route get 1.1.1.1 2>/dev/null | grep -oP 'src \K[0-9.]+'",
                                // Obtener todas las IPs y filtrar
                                "hostname -I 2>/dev/null | tr ' ' '\n' | grep -E '^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$' | grep -v '127.0.0.1' | head -1",
                                // Método con ifconfig
                                "ifconfig 2>/dev/null | grep 'inet ' | grep -v '127.0.0.1' | awk '{print $2}' | head -1",
                                // Método con ip addr
                                "ip addr show 2>/dev/null | grep 'inet ' | grep -v '127.0.0.1' | awk '{print $2}' | cut -d'/' -f1 | head -1"
                            ];
                            
                            foreach ($commands as $cmd) {
                                $output = shell_exec($cmd);
                                if ($output) {
                                    $ip = trim($output);
                                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && $ip !== '127.0.0.1') {
                                        $ipv4 = $ip;
                                        break;
                                    }
                                }
                            }
                        }
                        
                        // Si no se encontró ninguna IP válida, usar localhost
                        return $ipv4 ?: '127.0.0.1';
                    }
                    
                    // Construir URL usando IPv4 de la máquina
                    $currentIPv4 = obtenerIPv4Maquina();
                    $port = $_SERVER['SERVER_PORT'] ?? '80';
                    
                    // Determinar esquema
                    $scheme = 'http';
                    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
                        $scheme = 'https';
                    } elseif ($port === '443') {
                        $scheme = 'https';
                    }
                    
                    // Construir dominio con IPv4 y puerto si es necesario
                    $domain = $currentIPv4;
                    if (($scheme === 'http' && $port !== '80') || ($scheme === 'https' && $port !== '443')) {
                        $domain .= ':' . $port;
                    }

                    $base = Controller::path();
                    $base = rtrim($base, '/');
                    $perfilUrl = $scheme . '://' . $domain . $base . '/mascota/qrinfo?id=' . urlencode($mascota['id_mascota']);
                    
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
                            Escanea este código QR para ver la información de contacto de <?= htmlspecialchars($mascota['nombre']) ?>
                        </p>
                        <div class="bg-light rounded p-2 mb-2">
                            <small class="text-muted d-block">IPv4 de la Máquina:</small>
                            <code class="small"><?= htmlspecialchars($currentIPv4) ?></code>
                        </div>
                        <div class="bg-light rounded p-2 mb-2">
                            <small class="text-muted d-block">Dominio:</small>
                            <code class="small"><?= htmlspecialchars($domain) ?></code>
                        </div>
                        <div class="bg-light rounded p-2">
                            <small class="text-muted d-block">URL completa:</small>
                            <code class="small"><?= htmlspecialchars($perfilUrl) ?></code>
                        </div>
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
                        <li>El código mostrará solo la información de contacto necesaria</li>
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