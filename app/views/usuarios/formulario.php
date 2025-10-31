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
                                        Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" required 
                                           value="<?= htmlspecialchars($data['email'] ?? '') ?>"
                                           placeholder="tu@email.com">
                                    <div class="form-text">Será tu nombre de usuario para iniciar sesión</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="telefono" class="form-label">
                                        Teléfono <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" required
                                           value="<?= htmlspecialchars($data['telefono'] ?? '') ?>"
                                           placeholder="Ej: 341-1234567">
                                    <div class="form-text">Incluye código de área sin el 0</div>
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
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <span class="fs-6">✨</span> Crear mi cuenta
                                </button>
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