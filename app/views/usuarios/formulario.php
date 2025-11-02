<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
<?php echo \app\controllers\SiteController::head(); ?>

<!-- Hero section con gradiente -->
<section class="py-5" style="background: radial-gradient(1000px 500px at 10% 10%, rgba(13, 110, 253, .12), transparent 60%), radial-gradient(900px 500px at 90% 0%, rgba(255, 193, 7, .12), transparent 60%), linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <div class="card shadow-sm border-0 overflow-hidden">
                    <div class="card-header bg-white border-0 text-center py-4">
                        <h3 class="fw-bold mb-2 d-flex align-items-center justify-content-center gap-2">
                            <span class="fs-2">👋</span> Registro de Usuario
                        </h3>
                        <p class="text-muted mb-0">Completa todos los datos para crear tu cuenta</p>
                        <div class="alert alert-info border-0 mt-3 mb-0">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-5">📱</span>
                                <div>
                                    <strong>Verificación por WhatsApp</strong><br>
                                    <small>Te enviaremos un código a tu número para verificar tu identidad</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($errores)): ?>
                            <div class="alert alert-danger border-0 shadow-sm">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="fs-5">⚠️</span>
                                    <strong>Por favor corrige los siguientes errores:</strong>
                                </div>
                                <ul class="mb-0">
                                    <?php foreach ($errores as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?= Controller::path() ?>usuario/doRegister" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            
                            <!-- Información Personal -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                                    <span class="fs-5">📝</span> Información Personal
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="nombre" class="form-label">
                                            Nombre <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required 
                                               value="<?= htmlspecialchars($data['nombre'] ?? '') ?>"
                                               placeholder="Tu nombre">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="apellido" class="form-label">
                                            Apellido <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="apellido" name="apellido" required 
                                               value="<?= htmlspecialchars($data['apellido'] ?? '') ?>"
                                               placeholder="Tu apellido">
                                    </div>
                                </div>
                            </div>

                            <!-- Información de Contacto -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                                    <span class="fs-5">📞</span> Información de Contacto
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        Email
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= htmlspecialchars($data['email'] ?? '') ?>"
                                           placeholder="tu@email.com (opcional)">
                                    <div class="form-text">Campo opcional - no es necesario para el registro</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">
                                        Teléfono / WhatsApp <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" required
                                           value="<?= htmlspecialchars($data['telefono'] ?? '') ?>"
                                           placeholder="Ej: +543411234567 o 3411234567">
                                    <div class="form-text">
                                        <span class="d-flex align-items-center gap-1">
                                            <span class="fs-6">💬</span>
                                            Número que usaremos para llamadas y WhatsApp
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="direccion" class="form-label">
                                        Dirección Completa <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="direccion" name="direccion" rows="2" required 
                                              placeholder="Calle, número, barrio, ciudad"><?= htmlspecialchars($data['direccion'] ?? '') ?></textarea>
                                    <div class="form-text">Incluye calle, número, barrio y ciudad</div>
                                </div>
                            </div>

                            <!-- Foto de Perfil -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                                    <span class="fs-5">📷</span> Foto de Perfil
                                </h6>
                                <div class="mb-3">
                                    <label for="foto" class="form-label">Subir foto de perfil (opcional)</label>
                                    <input type="file" class="form-control" name="foto" id="foto" 
                                           accept="image/png, image/jpeg, image/jpg, image/gif">
                                    <div class="form-text">
                                        <span class="d-flex align-items-center gap-1">
                                            <span class="fs-6">💡</span>
                                            Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 5MB.
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Seguridad -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                                    <span class="fs-5">🔐</span> Seguridad
                                </h6>
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        Contraseña <span class="text-danger">*</span>
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" required
                                           placeholder="Mínimo 6 caracteres">
                                    <div class="form-text">
                                        <span class="d-flex align-items-center gap-1">
                                            <span class="fs-6">🛡️</span>
                                            Mínimo 6 caracteres. Usa una contraseña segura.
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Términos y Condiciones -->
                            <div class="mb-4">
                                <div class="card border-light bg-light">
                                    <div class="card-body p-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="terminos" name="terminos" required>
                                            <label class="form-check-label small" for="terminos">
                                                <span class="fs-6">📋</span> 
                                                Acepto los 
                                                <a href="<?= Controller::path() ?>legal/terminos" target="_blank" class="text-decoration-none fw-bold">
                                                    Términos y Condiciones
                                                </a> 
                                                y la
                                                <a href="<?= Controller::path() ?>legal/privacidad" target="_blank" class="text-decoration-none fw-bold">
                                                    Política de Privacidad
                                                </a>
                                                para el tratamiento de mis datos personales.
                                            </label>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <span class="fs-6">🔒</span> 
                                                <strong>Tu privacidad es importante:</strong> Solo usamos tu información para facilitar el contacto cuando tu mascota se pierda.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                                    <span class="fs-6">📱</span> Enviar código de verificación
                                </button>
                                <small class="text-muted text-center mt-2">
                                    Al continuar, recibirás un código por WhatsApp para verificar tu número
                                </small>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <div class="bg-light rounded-3 p-3">
                                <p class="mb-2">¿Ya tienes cuenta?</p>
                                <a href="<?= Controller::path() ?>usuario/login" class="btn btn-outline-primary">
                                    <span class="fs-6">🚪</span> Iniciar sesión aquí
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const terminosCheckbox = document.getElementById('terminos');
    const submitBtn = document.getElementById('submitBtn');
    
    // Función para habilitar/deshabilitar botón
    function toggleSubmitButton() {
        if (terminosCheckbox.checked) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-primary');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.remove('btn-primary');
            submitBtn.classList.add('btn-secondary');
        }
    }
    
    // Escuchar cambios en el checkbox
    terminosCheckbox.addEventListener('change', toggleSubmitButton);
    
    // Estado inicial
    toggleSubmitButton();
    
    // Validación adicional antes de enviar
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!terminosCheckbox.checked) {
            e.preventDefault();
            alert('📋 Debes aceptar los términos y condiciones para continuar.');
            terminosCheckbox.focus();
            return false;
        }
    });
});
</script>