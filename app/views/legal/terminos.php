<?php $BASE = Controller::path(); $ROOT = Controller::rootBase(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos y Condiciones - BOTI Pet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $ROOT ?>assets/css/base.css" rel="stylesheet">
    <?php echo \app\controllers\SiteController::head(); ?>
    <style>
        .terms-section {
            margin-bottom: 2rem;
        }
        .terms-section h3 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        .highlight {
            background-color: #fff3cd;
            padding: 1rem;
            border-left: 4px solid #ffc107;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h1 class="h3 mb-0">📋 Términos y Condiciones</h1>
                        <p class="mb-0 mt-2">Plataforma BOTI Pet - Sistema de Identificación de Mascotas</p>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="highlight">
                            <strong>Fecha de última actualización:</strong> <?= date('d/m/Y') ?><br>
                            <strong>Versión:</strong> 1.0
                        </div>

                        <div class="terms-section">
                            <h3>1. Aceptación de los Términos</h3>
                            <p>Al registrarte y usar la plataforma BOTI Pet, aceptas cumplir con estos términos y condiciones. Si no estás de acuerdo con alguno de estos términos, no debes usar nuestros servicios.</p>
                        </div>

                        <div class="terms-section">
                            <h3>2. Descripción del Servicio</h3>
                            <p>BOTI Pet es una plataforma digital que permite:</p>
                            <ul>
                                <li>📝 Registrar información de mascotas</li>
                                <li>🏷️ Generar códigos QR únicos para identificación</li>
                                <li>📞 Facilitar el contacto entre personas que encuentran mascotas perdidas y sus dueños</li>
                                <li>💬 Comunicación directa por WhatsApp</li>
                                <li>📱 Acceso a perfiles de mascotas mediante códigos QR</li>
                            </ul>
                        </div>

                        <div class="terms-section">
                            <h3>3. Registro y Verificación</h3>
                            <p>Para usar nuestros servicios, debes:</p>
                            <ul>
                                <li>✅ Proporcionar información veraz y actualizada</li>
                                <li>📱 Verificar tu número de WhatsApp mediante código de verificación</li>
                                <li>🔒 Mantener la confidencialidad de tu cuenta</li>
                                <li>📧 Proporcionar un email válido (opcional pero recomendado)</li>
                            </ul>
                        </div>

                        <div class="terms-section">
                            <h3>4. Responsabilidades del Usuario</h3>
                            <p>Al usar BOTI Pet, te comprometes a:</p>
                            <ul>
                                <li>📋 Proporcionar información real y actualizada sobre tus mascotas</li>
                                <li>🐾 Ser el propietario legítimo de las mascotas registradas</li>
                                <li>📞 Mantener actualizados tus datos de contacto</li>
                                <li>🚫 No usar el servicio para fines ilícitos o fraudulentos</li>
                                <li>🤝 Colaborar de buena fe cuando alguien reporte haber encontrado tu mascota</li>
                            </ul>
                        </div>

                        <div class="terms-section">
                            <h3>5. Privacidad y Protección de Datos</h3>
                            <div class="highlight">
                                <strong>⚠️ Información Importante sobre Privacidad:</strong>
                            </div>
                            <ul>
                                <li>🔐 Tu información personal está protegida y encriptada</li>
                                <li>📱 Solo se comparte información de contacto cuando alguien escanea el QR de tu mascota</li>
                                <li>🚫 Nunca vendemos ni compartimos tus datos con terceros para fines comerciales</li>
                                <li>✏️ Puedes editar o eliminar tu información en cualquier momento</li>
                                <li>📍 No rastreamos tu ubicación ni la de tus mascotas</li>
                            </ul>
                        </div>

                        <div class="terms-section">
                            <h3>6. Uso de WhatsApp</h3>
                            <p>Para la verificación y comunicación:</p>
                            <ul>
                                <li>📱 Enviamos códigos de verificación a tu número de WhatsApp</li>
                                <li>🔗 Generamos enlaces directos para contactarte por WhatsApp</li>
                                <li>⏰ Los códigos de verificación expiran en 10 minutos por seguridad</li>
                                <li>🚫 No enviamos mensajes promocionales no solicitados</li>
                            </ul>
                        </div>

                        <div class="terms-section">
                            <h3>7. Códigos QR y Seguridad</h3>
                            <p>Sobre los códigos QR generados:</p>
                            <ul>
                                <li>🏷️ Cada mascota tiene un código QR único e irrepetible</li>
                                <li>📱 Al escanear el QR, solo se muestra información de contacto</li>
                                <li>🔒 No se revela tu dirección exacta, solo datos de contacto</li>
                                <li>⚡ Los códigos funcionan inmediatamente después de ser generados</li>
                            </ul>
                        </div>

                        <div class="terms-section">
                            <h3>8. Limitación de Responsabilidad</h3>
                            <div class="highlight">
                                <strong>⚠️ Importante:</strong> BOTI Pet es una herramienta de asistencia para la identificación de mascotas.
                            </div>
                            <ul>
                                <li>🛡️ No garantizamos la recuperación de mascotas perdidas</li>
                                <li>📞 Facilitamos el contacto pero no mediamos en comunicaciones</li>
                                <li>🤝 No somos responsables de disputas entre usuarios</li>
                                <li>💻 Nos esforzamos por mantener el servicio disponible 24/7, pero pueden ocurrir interrupciones</li>
                            </ul>
                        </div>

                        <div class="terms-section">
                            <h3>9. Modificaciones del Servicio</h3>
                            <p>Nos reservamos el derecho de:</p>
                            <ul>
                                <li>🔄 Actualizar y mejorar la plataforma</li>
                                <li>📝 Modificar estos términos con previo aviso</li>
                                <li>🚫 Suspender cuentas que violen estos términos</li>
                                <li>💡 Agregar nuevas funcionalidades</li>
                            </ul>
                        </div>

                        <div class="terms-section">
                            <h3>10. Contacto y Soporte</h3>
                            <p>Para dudas, sugerencias o problemas:</p>
                            <ul>
                                <li>📧 Contáctanos a través del formulario en la plataforma</li>
                                <li>🔧 Reporta problemas técnicos inmediatamente</li>
                                <li>💡 Tus sugerencias son bienvenidas para mejorar el servicio</li>
                            </ul>
                        </div>

                        <div class="terms-section">
                            <h3>11. Terminación del Servicio</h3>
                            <p>Tanto tú como nosotros podemos terminar el uso del servicio:</p>
                            <ul>
                                <li>🚪 Puedes eliminar tu cuenta en cualquier momento</li>
                                <li>🗑️ Al eliminar tu cuenta, se borran tus datos y códigos QR</li>
                                <li>⚠️ Podemos suspender cuentas que violen estos términos</li>
                                <li>📬 Te notificaremos de cualquier cambio importante</li>
                            </ul>
                        </div>

                        <div class="alert alert-success mt-4">
                            <h5>✅ En Resumen</h5>
                            <p class="mb-0">BOTI Pet te ayuda a proteger a tus mascotas facilitando su identificación y el contacto contigo. Usamos tu información de manera responsable y solo para el propósito de reunirte con tu mascota si se pierde.</p>
                        </div>

                        <div class="text-center mt-4">
                            <button onclick="window.history.back()" class="btn btn-primary btn-lg">
                                ✅ He leído y acepto los términos
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>