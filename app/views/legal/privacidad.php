<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidad - BOTI Pet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
    <?php echo \app\controllers\SiteController::head(); ?>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white text-center">
                        <h1 class="h3 mb-0">🔒 Política de Privacidad</h1>
                        <p class="mb-0 mt-2">Cómo protegemos y usamos tu información</p>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="alert alert-info">
                            <strong>📅 Última actualización:</strong> <?= date('d/m/Y') ?><br>
                            <strong>🔐 En resumen:</strong> Solo usamos tu información para ayudarte a recuperar tu mascota si se pierde.
                        </div>

                        <h3>🛡️ Compromiso con tu Privacidad</h3>
                        <p>En BOTI Pet respetamos tu privacidad y nos comprometemos a proteger tu información personal. Esta política explica cómo recolectamos, usamos y protegemos tus datos.</p>

                        <h3>📊 Información que Recolectamos</h3>
                        <ul>
                            <li><strong>Datos personales:</strong> Nombre, apellido, email (opcional)</li>
                            <li><strong>Contacto:</strong> Número de teléfono/WhatsApp</li>
                            <li><strong>Ubicación:</strong> Dirección (opcional, para contexto)</li>
                            <li><strong>Mascotas:</strong> Nombre, especie, raza, descripción, foto</li>
                        </ul>

                        <h3>🎯 Cómo Usamos tu Información</h3>
                        <ul>
                            <li>✅ Verificar tu identidad mediante WhatsApp</li>
                            <li>✅ Generar códigos QR únicos para tus mascotas</li>
                            <li>✅ Permitir que personas que encuentren tu mascota te contacten</li>
                            <li>✅ Mejorar nuestros servicios</li>
                        </ul>

                        <h3>🚫 Lo que NO Hacemos</h3>
                        <ul>
                            <li>❌ NO vendemos tu información a terceros</li>
                            <li>❌ NO enviamos spam o publicidad</li>
                            <li>❌ NO rastreamos tu ubicación</li>
                            <li>❌ NO compartimos datos con redes sociales</li>
                        </ul>

                        <h3>🔐 Protección de Datos</h3>
                        <ul>
                            <li>🛡️ Encriptación de datos sensibles</li>
                            <li>🔒 Acceso restringido a información personal</li>
                            <li>📱 Verificación por WhatsApp para mayor seguridad</li>
                            <li>🗑️ Derecho a eliminar tu cuenta y datos en cualquier momento</li>
                        </ul>

                        <h3>📱 Uso de WhatsApp</h3>
                        <p>Usamos WhatsApp para:</p>
                        <ul>
                            <li>📲 Enviar códigos de verificación de 6 dígitos</li>
                            <li>🔗 Permitir contacto directo cuando encuentren tu mascota</li>
                            <li>⏰ Los mensajes de verificación se eliminan automáticamente</li>
                        </ul>

                        <h3>🍪 Cookies y Tecnologías</h3>
                        <p>Usamos tecnologías básicas para:</p>
                        <ul>
                            <li>🔐 Mantener tu sesión iniciada</li>
                            <li>⚡ Mejorar el rendimiento del sitio</li>
                            <li>📊 Estadísticas básicas de uso (anónimas)</li>
                        </ul>

                        <h3>👤 Tus Derechos</h3>
                        <p>Tienes derecho a:</p>
                        <ul>
                            <li>✏️ Editar tu información en cualquier momento</li>
                            <li>👁️ Ver qué datos tenemos sobre ti</li>
                            <li>🗑️ Eliminar tu cuenta y todos tus datos</li>
                            <li>📧 Contactarnos sobre cualquier duda de privacidad</li>
                        </ul>

                        <div class="alert alert-success mt-4">
                            <h5>✅ Resumen Simple</h5>
                            <p class="mb-0">Guardamos tu información solo para ayudarte a recuperar tu mascota. No la vendemos, no enviamos spam, y puedes eliminarla cuando quieras.</p>
                        </div>

                        <div class="text-center mt-4">
                            <button onclick="window.history.back()" class="btn btn-success btn-lg">
                                ✅ Entendido
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>