<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
<?php echo \app\controllers\SiteController::head(); ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h3 class="mb-0">Eliminar Usuario</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h5><i class="bi bi-exclamation-triangle"></i> ¡Atención!</h5>
                        <p class="mb-0">Esta acción no se puede deshacer. ¿Estás seguro de que quieres eliminar este usuario?</p>
                    </div>
                    
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-title">Información del usuario a eliminar:</h6>
                            <p class="card-text">
                                <strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre'] ?? '') ?> <?= htmlspecialchars($usuario['apellido'] ?? '') ?><br>
                                <strong>Email:</strong> <?= htmlspecialchars($usuario['email'] ?? '') ?><br>
                                <strong>ID:</strong> <?= htmlspecialchars($usuario['id'] ?? '') ?>
                            </p>
                        </div>
                    </div>
                    
                    <form method="POST" action="<?= Controller::path() ?>usuario/eliminar">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id'] ?? '') ?>">
                        <input type="hidden" name="confirm" value="yes">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-danger" 
                                            onclick="return confirm('¿Estás completamente seguro de eliminar este usuario?')">
                                        Sí, Eliminar Usuario
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid">
                                    <a href="<?= Controller::path() ?>usuario/lista" class="btn btn-outline-secondary">Cancelar</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>