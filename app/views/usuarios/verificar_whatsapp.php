<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
<?php echo \app\controllers\SiteController::head(); ?>

<section class="py-5" style="background: radial-gradient(1000px 500px at 10% 10%, rgba(37, 211, 102, .12), transparent 60%), radial-gradient(900px 500px at 90% 0%, rgba(13, 110, 253, .12), transparent 60%), linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white text-center">
                    <h3 class="mb-0">📱 Verificación por WhatsApp</h3>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($mensaje_envio)): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="bi bi-check-circle"></i> <?= htmlspecialchars($mensaje_envio) ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mb-4">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="bi bi-whatsapp text-success" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    
                    <div class="text-center mb-4">
                        <h5>Verificación de tu número</h5>
                        <p class="text-muted mb-2">
                            Hemos enviado un código de verificación de 6 dígitos a:
                        </p>
                        <p class="fw-bold text-success">
                            💬 <?= htmlspecialchars($telefono ?? '') ?>
                        </p>
                        <small class="text-muted">
                            Revisa tus mensajes de WhatsApp y ingresa el código a continuación
                        </small>
                    </div>
                    
                    <form method="POST" action="<?= Controller::path() ?>usuario/verificarWhatsapp" id="verificacionForm">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <div class="mb-4">
                            <label for="codigo" class="form-label text-center d-block">
                                <strong>Código de Verificación</strong>
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg text-center" 
                                   id="codigo" 
                                   name="codigo" 
                                   required 
                                   maxlength="6" 
                                   pattern="[0-9]{6}"
                                   placeholder="000000"
                                   style="font-size: 1.5rem; letter-spacing: 0.5rem; font-family: 'Courier New', monospace;"
                                   autocomplete="off">
                            <div class="form-text text-center">
                                Ingresa el código de 6 dígitos que recibiste por WhatsApp
                            </div>
                        </div>
                        
                        <?php if (isset($tiempo_restante) && $tiempo_restante > 0): ?>
                        <div class="alert alert-info text-center" id="tiempoRestante">
                            <small>
                                ⏰ Este código expira en <span id="minutos"><?= $tiempo_restante ?></span> minuto(s)
                            </small>
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2 mb-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                ✅ Verificar Código
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center">
                        <p class="text-muted small mb-2">¿No recibiste el código?</p>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="reenviarBtn">
                            🔄 Reenviar código
                        </button>
                        <div class="mt-3">
                            <a href="<?= Controller::path() ?>usuario/register" class="btn btn-link btn-sm">
                                ← Volver al registro
                            </a>
                        </div>
                    </div>
                    
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="text-muted mb-2">💡 Consejos:</h6>
                        <ul class="text-muted small mb-0">
                            <li>El código puede tardar hasta 1 minuto en llegar</li>
                            <li>Revisa que tu WhatsApp esté funcionando correctamente</li>
                            <li>El código expira en 10 minutos por seguridad</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const codigoInput = document.getElementById('codigo');
    const reenviarBtn = document.getElementById('reenviarBtn');
    const form = document.getElementById('verificacionForm');
    
    // Auto-formatear entrada del código
    codigoInput.addEventListener('input', function() {
        let value = this.value.replace(/[^0-9]/g, '');
        if (value.length > 6) {
            value = value.substring(0, 6);
        }
        this.value = value;
        
        // Auto-submit cuando se completen 6 dígitos
        if (value.length === 6) {
            setTimeout(() => {
                form.submit();
            }, 500);
        }
    });
    
    // Enfocar automáticamente el campo de código
    codigoInput.focus();
    
    // Manejar reenvío de código
    reenviarBtn.addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '⏳ Reenviando...';
        
        fetch('<?= Controller::path() ?>usuario/reenviarCodigo', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'csrf_token=<?= $csrf_token ?>'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                // Reiniciar timer si existe
                if (typeof iniciarTimer === 'function') {
                    iniciarTimer(10);
                }
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            alert('❌ Error al reenviar el código');
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '🔄 Reenviar código';
        });
    });
    
    // Timer de expiración si existe tiempo restante
    <?php if (isset($tiempo_restante) && $tiempo_restante > 0): ?>
    function iniciarTimer(minutos) {
        let tiempoTotal = minutos * 60;
        const minutosSpan = document.getElementById('minutos');
        const tiempoDiv = document.getElementById('tiempoRestante');
        
        const timer = setInterval(() => {
            const minutosRestantes = Math.floor(tiempoTotal / 60);
            const segundosRestantes = tiempoTotal % 60;
            
            if (minutosSpan) {
                minutosSpan.textContent = minutosRestantes + ':' + (segundosRestantes < 10 ? '0' : '') + segundosRestantes;
            }
            
            tiempoTotal--;
            
            if (tiempoTotal < 0) {
                clearInterval(timer);
                if (tiempoDiv) {
                    tiempoDiv.innerHTML = '<small class="text-danger">⚠️ El código ha expirado. Solicita uno nuevo.</small>';
                }
            }
        }, 1000);
    }
    
    iniciarTimer(<?= $tiempo_restante ?>);
    <?php endif; ?>
});
</script>

<style>
.bi {
    font-family: 'bootstrap-icons';
}

.bi-whatsapp::before {
    content: "💬";
}

.bi-check-circle::before {
    content: "✅";
}

.bi-exclamation-triangle::before {
    content: "⚠️";
}

#codigo:focus {
    border-color: #25d366;
    box-shadow: 0 0 0 0.2rem rgba(37, 211, 102, 0.25);
}

.btn-success {
    background-color: #25d366;
    border-color: #25d366;
}

.btn-success:hover {
    background-color: #128c7e;
    border-color: #128c7e;
}
</style>