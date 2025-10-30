<?php

namespace app\controllers;

use Controller;
use Response;
use Session;

class MascotaController extends Controller {
    protected $session;
    protected $viewsDir = 'mascotas/';
    
    public function __construct() {
        $this->session = new Session();
    }
    
    /**
     * Lista de mascotas
     */
    public function actionIndex($var = null) {
        $mascotas = \app\models\Mascota::getAll();
        Response::render($this->viewsDir, 'index', [
            'mascotas' => $mascotas
        ]);
    }
    
    /**
     * Mostrar perfil de mascota
     */
    public function actionPerfil() {
        $id = $this->input('id');
        $mascota = \app\models\Mascota::findById($id);
        
        if (!$mascota) {
            $this->redirect('/mascota');
        }
        
        Response::render($this->viewsDir, 'perfil', [
            'mascota' => $mascota,
            'csrf_token' => $this->generateCsrf()
        ]);
    }
    
    /**
     * Mostrar formulario de nueva mascota
     */
    public function actionCrear() {
        $this->requireAuth();
        Response::render($this->viewsDir, 'formulario', [
            'csrf_token' => $this->generateCsrf()
        ]);
    }
    
    /**
     * Procesar creación de mascota
     */
    public function actionStore() {
        $this->requireAuth();
        
        $data = $this->input();
        $csrf_token = $this->input('csrf_token');
        
        // Validar CSRF
        if (!$this->validateCsrf($csrf_token)) {
            Response::render($this->viewsDir, 'formulario', [
                'error' => 'Token de seguridad inválido',
                'csrf_token' => $this->generateCsrf(),
                'data' => $data
            ]);
            return;
        }
        // Procesar foto opcional si se subió un archivo válido
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['foto'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                $maxSize = 5 * 1024 * 1024; // 5MB
                $allowedExt = ['jpg','jpeg','png','gif'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if ($file['size'] <= $maxSize && in_array($ext, $allowedExt, true)) {
                    $uploadDir = __DIR__ . '/../../assets/images/mascotas/';
                    if (!is_dir($uploadDir)) {
                        @mkdir($uploadDir, 0755, true);
                    }
                    $fileName = 'mascota_' . time() . '_' . uniqid() . '.' . $ext;
                    $uploadPath = $uploadDir . $fileName;
                    if (@move_uploaded_file($file['tmp_name'], $uploadPath)) {
                        // Guardar ruta relativa utilizada por las vistas
                        $data['foto_url'] = 'assets/images/mascotas/' . $fileName;
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

        $mascota = new \app\models\Mascota();
        $errores = $mascota->validate($data);
        if (!empty($erroresUpload ?? [])) {
            $errores = array_merge($errores, $erroresUpload);
        }
        
        if (empty($errores)) {
            // BASE.sql usa columna `id` para el dueño
            $data['usuario_id'] = $this->session->getUserId(); // mapeada a `id` por el modelo
            $mascota->fill($data);
            
            if ($mascota->save()) {
                $this->redirect('mascota/perfil?id=' . $mascota->id_mascota);
            } else {
                $errores[] = 'Error al crear la mascota';
            }
        }
        
        Response::render($this->viewsDir, 'formulario', [
            'errores' => $errores,
            'csrf_token' => $this->generateCsrf(),
            'data' => $data
        ]);
    }
    
    /**
     * Mostrar formulario de edición
     */
    public function actionEditar() {
        $this->requireAuth();
        
        $id = $this->input('id');
        $mascota = \app\models\Mascota::findById($id);
        
        if (!$mascota) {
            $this->redirect('/mascota');
        }
        
        // Verificar que el usuario sea el propietario o admin
        $ownerId = $mascota['usuario_id'] ?? ($mascota['id'] ?? null);
        if ($ownerId != $this->session->getUserId()) {
            $this->redirect('/mascota');
        }
        
        Response::render($this->viewsDir, 'editar', [
            'mascota' => $mascota,
            'csrf_token' => $this->generateCsrf()
        ]);
    }
    
    /**
     * Procesar edición
     */
    public function actionUpdate() {
        $this->requireAuth();
        
        $id = $this->input('id');
        $data = $this->input();
        $csrf_token = $this->input('csrf_token');
        
        // Validar CSRF
        if (!$this->validateCsrf($csrf_token)) {
            $this->redirect('/mascota/editar?id=' . $id);
        }
        
        $mascota = new \app\models\Mascota(\app\models\Mascota::findById($id));
        
        if (!$mascota->id_mascota || $mascota->usuario_id != $this->session->getUserId()) {
            $this->redirect('/mascota');
        }
        // Procesar foto opcional si se sube en edición
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['foto'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                $maxSize = 5 * 1024 * 1024; // 5MB
                $allowedExt = ['jpg','jpeg','png','gif'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if ($file['size'] <= $maxSize && in_array($ext, $allowedExt, true)) {
                    $uploadDir = __DIR__ . '/../../assets/images/mascotas/';
                    if (!is_dir($uploadDir)) {
                        @mkdir($uploadDir, 0755, true);
                    }
                    $fileName = 'mascota_' . time() . '_' . uniqid() . '.' . $ext;
                    $uploadPath = $uploadDir . $fileName;
                    if (@move_uploaded_file($file['tmp_name'], $uploadPath)) {
                        // Guardar ruta relativa utilizada por las vistas
                        $data['foto_url'] = 'assets/images/mascotas/' . $fileName;
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

        $errores = $mascota->validate($data);
        if (!empty($erroresUpload ?? [])) {
            $errores = array_merge($errores, $erroresUpload);
        }
        
        if (empty($errores)) {
            $mascota->fill($data);
            
            if ($mascota->save()) {
                $this->redirect('/mascota/perfil?id=' . $mascota->id_mascota);
            } else {
                $errores[] = 'Error al actualizar la mascota';
            }
        }
        
        Response::render($this->viewsDir, 'editar', [
            'mascota' => $mascota->toArray(),
            'errores' => $errores,
            'csrf_token' => $this->generateCsrf()
        ]);
    }
    
    /**
     * Subir foto de mascota
     */
    public function actionSubirFoto() {
        $this->requireAuth();
        
        $id = $this->input('id_mascota');
        $mascota = \app\models\Mascota::findById($id);
        
        if (!$mascota) {
            $this->redirect('/mascota');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf_token = $this->input('csrf_token');
            
            if (!$this->validateCsrf($csrf_token)) {
                Response::render($this->viewsDir, 'subir_foto', [
                    'mascota' => $mascota,
                    'error' => 'Token de seguridad inválido',
                    'csrf_token' => $this->generateCsrf()
                ]);
                return;
            }
            
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../assets/images/mascotas/';
                $fileName = 'mascota_' . time() . '_' . uniqid() . '.' . pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $uploadPath = $uploadDir . $fileName;
                
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath)) {
                    $photoUrl = 'assets/images/mascotas/' . $fileName;
                    $mascotaObj = new \app\models\Mascota($mascota);
                    
                    if ($mascotaObj->updatePhoto($photoUrl)) {
                        $this->redirect('/mascota/perfil?id=' . $id);
                    } else {
                        $error = 'Error al actualizar la foto en la base de datos';
                    }
                } else {
                    $error = 'Error al subir el archivo';
                }
            } else {
                $error = 'No se seleccionó ningún archivo válido';
            }
            
            Response::render($this->viewsDir, 'subir_foto', [
                'mascota' => $mascota,
                'error' => $error ?? null,
                'csrf_token' => $this->generateCsrf()
            ]);
        } else {
            Response::render($this->viewsDir, 'subir_foto', [
                'mascota' => $mascota,
                'csrf_token' => $this->generateCsrf()
            ]);
        }
    }
    
    /**
     * Generar QR de mascota
     */
    public function actionQr() {
        $id = $this->input('id');
        $mascota = \app\models\Mascota::findById($id);
        
        if (!$mascota) {
            $this->redirect('/mascota');
        }
        
        Response::render($this->viewsDir, 'qr', [
            'mascota' => $mascota
        ]);
    }

    /**
     * Eliminar mascota y redirigir al perfil del usuario
     */
    public function actionEliminar() {
        $this->requireAuth();
        $id = $this->input('id_mascota') ?? $this->input('id');
        $token = $this->input('csrf_token');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Solo permitir POST
            $this->redirect('/mascota/perfil?id=' . urlencode((string)$id));
        }

        if (!$this->validateCsrf($token)) {
            $this->redirect('/mascota/perfil?id=' . urlencode((string)$id));
        }

        $mascota = \app\models\Mascota::findById($id);
        if (!$mascota) {
            $this->redirect('/usuario/perfil');
        }

        // Verificar propiedad
        $ownerId = $mascota['usuario_id'] ?? ($mascota['id'] ?? null);
        if ((string)$ownerId !== (string)$this->session->getUserId()) {
            $this->redirect('/mascota/perfil?id=' . urlencode((string)$id));
        }

        // Eliminar y redirigir al perfil del usuario con mensaje
        $deleted = \app\models\Mascota::deleteById($id);
        if ($deleted) {
            $_SESSION['flash_success'] = 'Mascota eliminada con éxito';
        } else {
            $_SESSION['flash_error'] = 'No se pudo eliminar la mascota';
        }
        $this->redirect('/usuario/perfil');
    }
}