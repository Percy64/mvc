<?php

namespace app\controllers;

use Controller;
use Response;
use Session;

class UsuarioController extends Controller {
    protected $session;
    protected $viewsDir = 'usuarios/';

    public function __construct() {
        $this->session = new Session();
    }

    public function actionLogin() {
        if ($this->session->estaLogueado()) {
            $this->redirect('/');
        }
        Response::render($this->viewsDir, 'login', [
            'csrf_token' => $this->generateCsrf(),
            'layout' => 'main',
            'title' => 'Iniciar Sesión · BOTI'
        ]);
    }

    public function actionDologin() {
        $email = $this->input('email');
        $password = $this->input('password');
        $csrf_token = $this->input('csrf_token');

        if (!$this->validateCsrf($csrf_token)) {
            Response::render($this->viewsDir, 'login', [
                'error' => 'Token de seguridad inválido',
                'csrf_token' => $this->generateCsrf()
            ]);
            return;
        }

        if (empty($email) || empty($password)) {
            Response::render($this->viewsDir, 'login', [
                'error' => 'Todos los campos son obligatorios',
                'csrf_token' => $this->generateCsrf()
            ]);
            return;
        }

        $usuario = \app\models\Usuario::authenticate($email, $password);
        if ($usuario) {
            $this->session->iniciarSesion($usuario);
            $this->redirect('/');
        } else {
            Response::render($this->viewsDir, 'login', [
                'error' => 'Credenciales incorrectas',
                'csrf_token' => $this->generateCsrf()
            ]);
        }
    }

    public function actionRegister() {
        if ($this->session->estaLogueado()) {
            $this->redirect('/');
        }
        Response::render($this->viewsDir, 'formulario', [
            'csrf_token' => $this->generateCsrf(),
            'layout' => 'main',
            'title' => 'Registro · BOTI'
        ]);
    }

    public function actionDoregister() {
        $data = $this->input();
        $csrf_token = $this->input('csrf_token');

        if (!$this->validateCsrf($csrf_token)) {
            Response::render($this->viewsDir, 'formulario', [
                'error' => 'Token de seguridad inválido',
                'csrf_token' => $this->generateCsrf(),
                'data' => $data
            ]);
            return;
        }

        // Paso 1: Validar datos y enviar código de verificación por WhatsApp
        $usuario = new \app\models\Usuario();
        $errores = $usuario->validate($data);
        
        // Validar términos y condiciones
        if (empty($data['terminos']) || $data['terminos'] !== 'on') {
            $errores[] = 'Debes aceptar los términos y condiciones para continuar.';
        }

        if (empty($errores)) {
            // Verificar si el teléfono ya ha excedido intentos
            if (\app\models\VerificacionWhatsApp::haExcedidoIntentos($data['telefono'])) {
                $errores[] = 'Has excedido el máximo de intentos. Espera 10 minutos antes de intentar nuevamente.';
            } else {
                // Generar y enviar código
                $codigo = \app\models\VerificacionWhatsApp::generarCodigo($data['telefono']);
                
                if ($codigo) {
                    // Enviar código por WhatsApp
                    $whatsappService = \app\services\WhatsAppService::create();
                    $resultado = $whatsappService->enviarCodigoVerificacion($data['telefono'], $codigo);
                    
                    if ($resultado['success']) {
                        // Guardar datos temporalmente en sesión
                        $this->session->set('registro_temporal', $data);
                        
                        // Redirigir a verificación
                        Response::render($this->viewsDir, 'verificar_whatsapp', [
                            'telefono' => $data['telefono'],
                            'mensaje_envio' => $resultado['message'],
                            'csrf_token' => $this->generateCsrf(),
                            'layout' => 'main',
                            'title' => 'Verificar WhatsApp · BOTI'
                        ]);
                        return;
                    } else {
                        $errores[] = 'Error al enviar código de verificación: ' . $resultado['message'];
                    }
                } else {
                    $errores[] = 'Error al generar código de verificación';
                }
            }
        }

        Response::render($this->viewsDir, 'formulario', [
            'errores' => $errores,
            'csrf_token' => $this->generateCsrf(),
            'data' => $data
        ]);
    }

    /**
     * Verificar código de WhatsApp y completar registro
     */
    public function actionVerificarWhatsapp() {
        $codigo = $this->input('codigo');
        $csrf_token = $this->input('csrf_token');
        
        if (!$this->validateCsrf($csrf_token)) {
            Response::render($this->viewsDir, 'verificar_whatsapp', [
                'error' => 'Token de seguridad inválido',
                'csrf_token' => $this->generateCsrf()
            ]);
            return;
        }

        // Obtener datos temporales del registro
        $datosRegistro = $this->session->get('registro_temporal');
        if (!$datosRegistro) {
            $this->redirect('/usuario/register');
            return;
        }

        if (empty($codigo)) {
            Response::render($this->viewsDir, 'verificar_whatsapp', [
                'error' => 'El código de verificación es obligatorio',
                'telefono' => $datosRegistro['telefono'],
                'csrf_token' => $this->generateCsrf()
            ]);
            return;
        }

        // Verificar código
        if (\app\models\VerificacionWhatsApp::verificarCodigo($datosRegistro['telefono'], $codigo)) {
            // Código válido, crear usuario
            $usuario = new \app\models\Usuario();
            $usuario->fill($datosRegistro);
            
            if ($usuario->save()) {
                // Limpiar datos temporales
                $this->session->remove('registro_temporal');
                
                // Iniciar sesión automáticamente
                $this->session->iniciarSesion($usuario->toArray());
                
                // Mostrar mensaje de éxito y redirigir
                $this->session->setFlash('success', '¡Registro completado exitosamente! Tu número de WhatsApp ha sido verificado.');
                $this->redirect('/');
                return;
            } else {
                $error = 'Error al crear la cuenta. Inténtalo nuevamente.';
            }
        } else {
            $error = 'Código de verificación incorrecto o expirado.';
        }

        Response::render($this->viewsDir, 'verificar_whatsapp', [
            'error' => $error,
            'telefono' => $datosRegistro['telefono'],
            'csrf_token' => $this->generateCsrf(),
            'tiempo_restante' => \app\models\VerificacionWhatsApp::getTiempoRestante($datosRegistro['telefono'])
        ]);
    }

    /**
     * Reenviar código de verificación
     */
    public function actionReenviarCodigo() {
        $csrf_token = $this->input('csrf_token');
        
        if (!$this->validateCsrf($csrf_token)) {
            echo json_encode(['success' => false, 'message' => 'Token inválido']);
            return;
        }

        $datosRegistro = $this->session->get('registro_temporal');
        if (!$datosRegistro) {
            echo json_encode(['success' => false, 'message' => 'Sesión expirada']);
            return;
        }

        // Verificar si puede reenviar (no ha excedido intentos)
        if (\app\models\VerificacionWhatsApp::haExcedidoIntentos($datosRegistro['telefono'])) {
            echo json_encode(['success' => false, 'message' => 'Has excedido el máximo de intentos']);
            return;
        }

        // Generar nuevo código
        $codigo = \app\models\VerificacionWhatsApp::generarCodigo($datosRegistro['telefono']);
        
        if ($codigo) {
            $whatsappService = \app\services\WhatsAppService::create();
            $resultado = $whatsappService->enviarCodigoVerificacion($datosRegistro['telefono'], $codigo);
            
            echo json_encode([
                'success' => $resultado['success'],
                'message' => $resultado['success'] ? 'Código reenviado correctamente' : $resultado['message']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al generar nuevo código']);
        }
    }

    public function actionLogout() {
        $this->session->cerrarSesion();
        $this->redirect('/');
    }

    public function actionPanel() {
        $this->requireAuth();
        if (!$this->isAdmin()) {
            $this->redirect('/usuario/perfil');
        }
        $userId = $this->session->getUserId();
        $mascotas = \app\models\Mascota::getByUserId($userId);
        $usuario = \app\models\Usuario::findById($userId);
        Response::render($this->viewsDir, 'panel', [
            'usuario' => $usuario,
            'mascotas' => $mascotas,
            'is_admin' => true
        ]);
    }

    public function actionPerfil() {
        $this->requireAuth();
        $userId = $this->session->getUserId();
        $mascotas = \app\models\Mascota::getByUserId($userId);
        // Cargar usuario completo desde la BD para incluir foto_url y otros campos
        $usuario = \app\models\Usuario::findById($userId);
        Response::render($this->viewsDir, 'perfil', [
            'usuario' => $usuario,
            'mascotas' => $mascotas,
            'is_admin' => $this->isAdmin(),
            'layout' => 'main',
            'title' => 'Mi Perfil · BOTI'
        ]);
    }

    public function actionLista() {
        $this->requireAuth();
        if (!$this->isAdmin()) {
            // Solo administradores pueden ver la lista de usuarios
            $this->redirect('/usuario/perfil');
        }
        $usuarios = \app\models\Usuario::getAll();
        Response::render($this->viewsDir, 'lista', [
            'usuarios' => $usuarios
        ]);
    }

    public function actionEditar() {
        $this->requireAuth();
        $id = $this->input('id');
        $userId = $this->session->getUserId();
        if ($id != $userId) {
            $this->redirect('/usuario/perfil');
        }
        $usuario = \app\models\Usuario::findById($id);
        if (!$usuario) {
            $this->redirect('/usuario/perfil');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->input();
            $csrf_token = $this->input('csrf_token');
            if (!$this->validateCsrf($csrf_token)) {
                Response::render($this->viewsDir, 'editar', [
                    'usuario' => $usuario,
                    'error' => 'Token de seguridad inválido',
                    'csrf_token' => $this->generateCsrf()
                ]);
                return;
            }
            $usuarioObj = new \app\models\Usuario($usuario);

            // Procesar una foto opcional si se sube en edición
            $erroresUpload = [];
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['foto'];
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $maxSize = 5 * 1024 * 1024; // 5MB
                    $allowedExt = ['jpg','jpeg','png','gif'];
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    if ($file['size'] <= $maxSize && in_array($ext, $allowedExt, true)) {
                        // Guardar bajo public/assets/usuarios para servir directo desde el document root
                        $uploadDir = __DIR__ . '/../../public/assets/usuarios/';
                        if (!is_dir($uploadDir)) {
                            @mkdir($uploadDir, 0755, true);
                        }
                        $fileName = 'usuario_' . time() . '_' . uniqid() . '.' . $ext;
                        $uploadPath = $uploadDir . $fileName;
                        if (@move_uploaded_file($file['tmp_name'], $uploadPath)) {
                            // Guardar ruta relativa dentro de /public para las vistas
                            // Usaremos $BASE (Controller::path) para componer /MVC/public/ + esta ruta
                            $data['foto_url'] = 'assets/usuarios/' . $fileName;
                        } else {
                            $erroresUpload[] = 'No se pudo guardar la imagen subida.';
                        }
                    } else {
                        $erroresUpload[] = 'Imagen inválida (tipo o tamaño). Máximo 5MB. Permitidos: JPG, PNG, GIF.';
                    }
                } else {
                    $erroresUpload[] = 'Error al subir la imagen (código ' . intval($file['error']) . ').';
                }
            }

            $errores = $usuarioObj->validate($data, true);
            if (!empty($erroresUpload)) {
                $errores = array_merge($errores, $erroresUpload);
            }
            if (empty($errores)) {
                $usuarioObj->fill($data);
                if ($usuarioObj->save()) {
                    $this->redirect('/usuario/perfil');
                    return;
                } else {
                    $errores[] = 'Error al actualizar el usuario';
                }
            }
            Response::render($this->viewsDir, 'editar', [
                'usuario' => $usuarioObj->toArray(),
                'errores' => $errores,
                'csrf_token' => $this->generateCsrf(),
                'is_admin' => $this->isAdmin()
            ]);
        } else {
            Response::render($this->viewsDir, 'editar', [
                'usuario' => $usuario,
                'csrf_token' => $this->generateCsrf(),
                'is_admin' => $this->isAdmin()
            ]);
        }
    }

    /**
     * Determinar si el usuario actual es administrador por ID.
     * Se valida contra una lista blanca de IDs en app/config/admins.php que debe devolver un array de IDs.
     */
    private function isAdmin(): bool {
        $uid = $this->session->getUserId();
        if (!$uid) return false;
        $u = \app\models\Usuario::findById($uid);
        if (!$u) return false;
        $userId = (int)$u['id'];
        $allowed = $this->adminIds();
        return in_array($userId, $allowed, true);
    }

    /**
     * Obtener lista de IDs administradores desde config opcional.
     */
    private function adminIds(): array {
        $configPath = __DIR__ . '/../config/admins.php';
        if (file_exists($configPath)) {
            $data = include $configPath;
            if (is_array($data)) {
                return array_values(array_unique(array_map('intval', $data)));
            }
        }
        return [];
    }
}
