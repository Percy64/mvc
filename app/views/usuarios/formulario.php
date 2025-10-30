<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Registro de Usuario</h3>
                    
                    <?php if (!empty($errores)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errores as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?= Controller::path() ?>usuario/doRegister">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required 
                                   value="<?= htmlspecialchars($data['nombre'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" required 
                                   value="<?= htmlspecialchars($data['apellido'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono (opcional)</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" 
                                   value="<?= htmlspecialchars($data['telefono'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required 
                                   value="<?= htmlspecialchars($data['email'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección (opcional)</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="2"><?= htmlspecialchars($data['direccion'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">Mínimo 6 caracteres</div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Registrarse</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>¿Ya tienes cuenta? <a href="<?= Controller::path() ?>usuario/login">Inicia sesión aquí</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>