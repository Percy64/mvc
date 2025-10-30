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

        $usuario = new \app\models\Usuario();
        $errores = $usuario->validate($data);

        if (empty($errores)) {
            $usuario->fill($data);
            if ($usuario->save()) {
                $this->session->iniciarSesion($usuario->toArray());
                $this->redirect('/');
                return;
            } else {
                $errores[] = 'Error al crear el usuario';
            }
        }

        Response::render($this->viewsDir, 'formulario', [
            'errores' => $errores,
            'csrf_token' => $this->generateCsrf(),
            'data' => $data
        ]);
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
            'is_admin' => $this->isAdmin()
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
     * Determinar si el usuario actual es administrador por email.
     * Se valida contra una lista blanca de emails en app/config/admins.php que debe devolver un array de emails.
     */
    private function isAdmin(): bool {
        $uid = $this->session->getUserId();
        if (!$uid) return false;
        $u = \app\models\Usuario::findById($uid);
        if (!$u || empty($u['email'])) return false;
        $email = strtolower((string)$u['email']);
        $allowed = $this->adminEmails();
        return in_array($email, $allowed, true);
    }

    /**
     * Obtener lista de emails administradores desde config opcional.
     */
    private function adminEmails(): array {
        $configPath = __DIR__ . '/../config/admins.php';
        if (file_exists($configPath)) {
            $data = include $configPath;
            if (is_array($data)) {
                return array_values(array_unique(array_map('strtolower', $data)));
            }
        }
        return [];
    }
}
