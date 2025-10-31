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
        Response::render($this->viewsDir, 'lista', [
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
            'csrf_token' => $this->generateCsrf(),
            'layout' => 'main',
            'title' => 'Perfil de ' . ($mascota['nombre'] ?? 'Mascota') . ' · BOTI'
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

        // Solo el propietario puede subir fotos
        $ownerId = $mascota['usuario_id'] ?? ($mascota['id'] ?? null);
        if ((string)$ownerId !== (string)$this->session->getUserId()) {
            $this->redirect('/mascota/perfil?id=' . urlencode((string)$id));
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

            // Regla de negocio: máximo 3 imágenes por perfil (carrusel)
            // Contar imágenes únicas considerando principal + galería
            $existingUrls = [];
            $norm = function($u) {
                $u = trim((string)$u);
                if ($u === '') return '';
                $u = str_replace('\\', '/', $u);
                $u = strtolower($u);
                $u = ltrim($u, '/');
                return $u;
            };
            $mainUrl = $norm($mascota['foto_url'] ?? '');
            if ($mainUrl !== '') { $existingUrls[$mainUrl] = true; }
            try {
                $gal = \DataBase::getRecords('SELECT url FROM fotos_mascotas WHERE id_mascota = ?', [$id]);
            } catch (\Throwable $e) {
                $gal = [];
            }
            foreach ($gal as $g) {
                $u = $norm($g['url'] ?? '');
                if ($u !== '') { $existingUrls[$u] = true; }
            }
            $existingCount = count($existingUrls);
            $MAX_IMAGES = 3;
            $remainingSlots = max(0, $MAX_IMAGES - $existingCount);
            if ($remainingSlots <= 0) {
                Response::render($this->viewsDir, 'subir_foto', [
                    'mascota' => $mascota,
                    'error' => 'Esta mascota ya tiene el máximo de 3 fotos en su perfil.',
                    'csrf_token' => $this->generateCsrf()
                ]);
                return;
            }
            
            $uploadDir = __DIR__ . '/../../assets/images/mascotas/';
            if (!file_exists($uploadDir)) { @mkdir($uploadDir, 0755, true); }

            $maxSize = 5 * 1024 * 1024; // 5MB por archivo
            $allowedExt = ['jpg','jpeg','png','gif'];
            $success = 0; $failed = 0; $messages = [];
            $sharedDesc = trim((string)($this->input('descripcion') ?? ''));

            $filesProcessed = false;

            // Caso 1: múltiples archivos en 'fotos[]' (respetar tope total)
            if (isset($_FILES['fotos']) && is_array($_FILES['fotos']['name'])) {
                $filesProcessed = true;
                $count = count($_FILES['fotos']['name']);
                // Limitar cantidad a los cupos restantes
                $count = min($count, $remainingSlots);
                for ($i = 0; $i < $count; $i++) {
                    $name = $_FILES['fotos']['name'][$i];
                    $tmp  = $_FILES['fotos']['tmp_name'][$i];
                    $err  = $_FILES['fotos']['error'][$i];
                    $size = $_FILES['fotos']['size'][$i];
                    if ($err !== UPLOAD_ERR_OK) { $failed++; $messages[] = "Archivo '$name': error al subir (código $err)"; continue; }
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    if ($size > $maxSize || !in_array($ext, $allowedExt, true)) { $failed++; $messages[] = "Archivo '$name': inválido (tipo o tamaño)"; continue; }
                    $fileName = 'mascota_' . time() . '_' . uniqid() . '.' . $ext;
                    $uploadPath = $uploadDir . $fileName;
                    if (@move_uploaded_file($tmp, $uploadPath)) {
                        $photoUrl = 'assets/images/mascotas/' . $fileName;
                        // Insertar en galería
                        \app\models\Mascota::addGalleryPhoto($id, $photoUrl, $sharedDesc);
                        // Si no tiene foto principal, usar la primera subida
                        if (empty($mascota['foto_url']) && $success === 0) {
                            $mascotaObj = new \app\models\Mascota($mascota);
                            $mascotaObj->updatePhoto($photoUrl);
                            $mascota['foto_url'] = $photoUrl;
                        }
                        $success++;
                        // Reducir slots restantes por cada éxito
                        $remainingSlots = max(0, $remainingSlots - 1);
                        if ($remainingSlots <= 0) {
                            // No procesar más archivos si se llegó al tope
                            break;
                        }
                    } else {
                        $failed++; $messages[] = "Archivo '$name': no se pudo guardar";
                    }
                }
            }

            // Caso 2: compatibilidad con un solo archivo 'foto'
            if (!$filesProcessed && isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                if ($remainingSlots <= 0) {
                    $error = 'Esta mascota ya tiene el máximo de 3 fotos en su perfil.';
                } else {
                $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                if ($_FILES['foto']['size'] <= $maxSize && in_array($ext, $allowedExt, true)) {
                    $fileName = 'mascota_' . time() . '_' . uniqid() . '.' . $ext;
                    $uploadPath = $uploadDir . $fileName;
                    if (@move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath)) {
                        $photoUrl = 'assets/images/mascotas/' . $fileName;
                        // Galería
                        \app\models\Mascota::addGalleryPhoto($id, $photoUrl, $sharedDesc);
                        // Actualizar principal
                        $mascotaObj = new \app\models\Mascota($mascota);
                        $mascotaObj->updatePhoto($photoUrl);
                        $mascota['foto_url'] = $photoUrl;
                        $success++;
                    } else {
                        $failed++; $messages[] = 'No se pudo guardar la imagen subida.';
                    }
                } else {
                    $failed++; $messages[] = 'Imagen inválida (tipo o tamaño). Máximo 5MB. Permitidos: JPG, PNG, GIF.';
                }
                }
            }

            if ($success > 0) {
                $_SESSION['flash_success'] = "Se subieron $success foto(s) correctamente" . ($failed ? ", $failed falló/fallaron" : '');
                $this->redirect('/mascota/perfil?id=' . $id);
            } else {
                $error = !empty($messages) ? implode(' · ', $messages) : 'No se seleccionó ningún archivo válido';
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
     * Vista simplificada para QR - solo información de mascota y dueño
     */
    public function actionQrinfo() {
        $id = $this->input('id');
        $mascota = \app\models\Mascota::findById($id);
        
        if (!$mascota) {
            $this->redirect('/');
        }
        // Cargar información básica del dueño (contacto)
        $owner = null;
        try {
            $ownerId = $mascota['usuario_id'] ?? ($mascota['id'] ?? null);
            if ($ownerId) {
                $owner = \app\models\Usuario::findById($ownerId);
            }
        } catch (\Throwable $e) {
            $owner = null;
        }

        Response::render($this->viewsDir, 'qr_info', [
            'mascota' => $mascota,
            'owner' => $owner
        ]);
    }

    /**
     * Listado público de mascotas perdidas
     */
    public function actionPerdidas() {
        $mascotas = \app\models\Mascota::getMascotasPerdidas();
        Response::render($this->viewsDir, 'perdidas', [
            'mascotas' => $mascotas,
            'layout' => 'main',
            'title' => 'Mascotas perdidas · BOTI'
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
    
    /**
     * Marcar mascota como perdida
     */
    public function actionMarcarPerdida() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mascota');
        }
        
        $id = $this->input('id_mascota');
        $token = $this->input('csrf_token');
        
        if (!$this->validateCsrf($token)) {
            $_SESSION['flash_error'] = 'Token de seguridad inválido';
            $this->redirect('/mascota/perfil?id=' . urlencode((string)$id));
        }
        
        $mascota = \app\models\Mascota::findById($id);
        if (!$mascota) {
            $_SESSION['flash_error'] = 'Mascota no encontrada';
            $this->redirect('/mascota');
        }
        
        // Verificar que el usuario sea el propietario
        $ownerId = $mascota['usuario_id'] ?? ($mascota['id'] ?? null);
        if ((string)$ownerId !== (string)$this->session->getUserId()) {
            $_SESSION['flash_error'] = 'No tienes permisos para realizar esta acción';
            $this->redirect('/mascota/perfil?id=' . urlencode((string)$id));
        }
        
        // Marcar como perdida
        $updated = \app\models\Mascota::marcarComoPerdida($id, true);
        if ($updated) {
            $_SESSION['flash_success'] = 'Mascota marcada como perdida. Esperamos que la encuentres pronto.';
        } else {
            $_SESSION['flash_error'] = 'No se pudo actualizar el estado de la mascota';
        }
        
        $this->redirect('/mascota/perfil?id=' . urlencode((string)$id));
    }
    
    /**
     * Marcar mascota como encontrada (solo propietario)
     */
    public function actionMarcarEncontrada() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mascota');
        }
        
        $id = $this->input('id_mascota');
        $token = $this->input('csrf_token');
        
        if (!$this->validateCsrf($token)) {
            $_SESSION['flash_error'] = 'Token de seguridad inválido';
            $this->redirect('/mascota/perfil?id=' . urlencode((string)$id));
        }
        
        $mascota = \app\models\Mascota::findById($id);
        if (!$mascota) {
            $_SESSION['flash_error'] = 'Mascota no encontrada';
            $this->redirect('/mascota');
        }
        
        // Verificar que el usuario sea el propietario
        $ownerId = $mascota['usuario_id'] ?? ($mascota['id'] ?? null);
        if ((string)$ownerId !== (string)$this->session->getUserId()) {
            $_SESSION['flash_error'] = 'No tienes permisos para realizar esta acción';
            $this->redirect('/mascota/perfil?id=' . urlencode((string)$id));
        }
        
        // Marcar como encontrada
        $updated = \app\models\Mascota::marcarComoPerdida($id, false);
        if ($updated) {
            $_SESSION['flash_success'] = '¡Excelente! Mascota marcada como encontrada.';
        } else {
            $_SESSION['flash_error'] = 'No se pudo actualizar el estado de la mascota';
        }
        
        $this->redirect('/mascota/perfil?id=' . urlencode((string)$id));
    }
    
    /**
     * Reportar que se encontró una mascota perdida (cualquier usuario)
     */
    public function actionReportarEncontrada() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mascota');
        }
        
        $id = $this->input('id_mascota');
        $token = $this->input('csrf_token');
        
        if (!$this->validateCsrf($token)) {
            $_SESSION['flash_error'] = 'Token de seguridad inválido';
            $this->redirect('/mascota/perfil?id=' . urlencode((string)$id));
        }
        
        $mascota = \app\models\Mascota::findById($id);
        if (!$mascota) {
            $_SESSION['flash_error'] = 'Mascota no encontrada';
            $this->redirect('/mascota');
        }
        
        // Verificar que la mascota esté perdida (columna 'perdido')
        if (!isset($mascota['perdido']) || !$mascota['perdido']) {
            $_SESSION['flash_info'] = 'Esta mascota no está reportada como perdida';
            $this->redirect('/mascota/perfil?id=' . urlencode((string)$id));
        }
        
        // Registrar el reporte de encontrada
        $reporteId = \app\models\Mascota::registrarReporteEncontrada($id, $this->session->getUserId());
        if ($reporteId) {
            $_SESSION['flash_success'] = 'Gracias por reportar el hallazgo. El propietario será notificado.';
            
            // Aquí puedes agregar lógica para notificar al propietario
            // Por ejemplo, enviar email, SMS, etc.
            
        } else {
            $_SESSION['flash_error'] = 'No se pudo registrar el reporte';
        }
        
        $this->redirect('/mascota/perfil?id=' . urlencode((string)$id));
    }

    /**
     * Eliminar una foto de la galería (solo propietario)
     */
    public function actionEliminarFoto() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mascota');
        }

        $idFoto = $this->input('id_foto');
        $idMascota = $this->input('id_mascota');
        $token = $this->input('csrf_token');
        if (!$this->validateCsrf($token)) {
            $_SESSION['flash_error'] = 'Token de seguridad inválido';
            $this->redirect('/mascota/perfil?id=' . urlencode((string)$idMascota));
        }

        // Obtener foto
    $foto = \DataBase::getRecord('SELECT id_foto, id_mascota, url FROM fotos_mascotas WHERE id_foto = ?', [$idFoto]);
        if (!$foto) {
            $_SESSION['flash_error'] = 'Foto no encontrada';
            $this->redirect('/mascota/perfil?id=' . urlencode((string)$idMascota));
        }

        // Verificar mascota y propiedad
        $mascota = \app\models\Mascota::findById($foto['id_mascota']);
        if (!$mascota) {
            $_SESSION['flash_error'] = 'Mascota no encontrada';
            $this->redirect('/mascota');
        }
        $ownerId = $mascota['usuario_id'] ?? ($mascota['id'] ?? null);
        if ((string)$ownerId !== (string)$this->session->getUserId()) {
            $_SESSION['flash_error'] = 'No tienes permisos para esta acción';
            $this->redirect('/mascota/perfil?id=' . urlencode((string)$mascota['id_mascota']));
        }

        // Borrar registro
    $deleted = \DataBase::execute('DELETE FROM fotos_mascotas WHERE id_foto = ?', [$idFoto]) > 0;

        // Si era la principal, intentar setear otra o vaciar
        $wasPrincipal = !empty($mascota['foto_url']) && (ltrim(strtolower($mascota['foto_url']), '/') === ltrim(strtolower($foto['url']), '/'));
        if ($deleted && $wasPrincipal) {
            $otra = \DataBase::getRecord('SELECT url FROM fotos_mascotas WHERE id_mascota = ? ORDER BY id_foto DESC LIMIT 1', [$mascota['id_mascota']]);
            $nuevoUrl = $otra['url'] ?? null;
            $m = new \app\models\Mascota($mascota);
            $m->updatePhoto($nuevoUrl ?: null);
        }

        // Intentar borrar archivo físico si es una ruta esperada
        if ($deleted) {
            $url = (string)$foto['url'];
            $p = ltrim(str_replace('\\', '/', $url), '/');
            if (strpos($p, 'assets/images/mascotas/') === 0) {
                $abs = __DIR__ . '/../../' . $p; // app/controllers/../../ -> raiz del proyecto
                if (is_file($abs)) { @unlink($abs); }
            }
        }

        $_SESSION['flash_success'] = $deleted ? 'Foto eliminada' : 'No se pudo eliminar la foto';
        $this->redirect('/mascota/perfil?id=' . urlencode((string)$mascota['id_mascota']));
    }

    /**
     * Hacer una foto de la galería como principal (solo propietario)
     */
    public function actionHacerPrincipal() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mascota');
        }

        $idFoto = $this->input('id_foto');
        $idMascota = $this->input('id_mascota');
        $token = $this->input('csrf_token');
        if (!$this->validateCsrf($token)) {
            $_SESSION['flash_error'] = 'Token de seguridad inválido';
            $this->redirect('/mascota/perfil?id=' . urlencode((string)$idMascota));
        }

    $foto = \DataBase::getRecord('SELECT id_foto, id_mascota, url FROM fotos_mascotas WHERE id_foto = ?', [$idFoto]);
        if (!$foto || (string)$foto['id_mascota'] !== (string)$idMascota) {
            $_SESSION['flash_error'] = 'Foto inválida';
            $this->redirect('/mascota/perfil?id=' . urlencode((string)$idMascota));
        }

        $mascota = \app\models\Mascota::findById($idMascota);
        if (!$mascota) {
            $_SESSION['flash_error'] = 'Mascota no encontrada';
            $this->redirect('/mascota');
        }
        $ownerId = $mascota['usuario_id'] ?? ($mascota['id'] ?? null);
        if ((string)$ownerId !== (string)$this->session->getUserId()) {
            $_SESSION['flash_error'] = 'No tienes permisos para esta acción';
            $this->redirect('/mascota/perfil?id=' . urlencode((string)$idMascota));
        }

        $m = new \app\models\Mascota($mascota);
        $ok = $m->updatePhoto($foto['url']);
        $_SESSION['flash_' . ($ok ? 'success' : 'error')] = $ok ? 'Foto principal actualizada' : 'No se pudo actualizar la foto principal';
        $this->redirect('/mascota/perfil?id=' . urlencode((string)$idMascota));
    }

    /**
     * Actualizar una foto de la galería (descripción y/o reemplazo de archivo)
     */
    public function actionActualizarFoto() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/mascota');
        }

        $idFoto = $this->input('id_foto');
        $idMascota = $this->input('id_mascota');
        $token = $this->input('csrf_token');

        if (!$this->validateCsrf($token)) {
            $_SESSION['flash_error'] = 'Token de seguridad inválido';
            $this->redirect('/mascota/editar?id=' . urlencode((string)$idMascota));
        }

        // Obtener foto y validar pertenencia
        $foto = \DataBase::getRecord('SELECT id_foto, id_mascota, url, descripcion FROM fotos_mascotas WHERE id_foto = ?', [$idFoto]);
        if (!$foto || (string)$foto['id_mascota'] !== (string)$idMascota) {
            $_SESSION['flash_error'] = 'Foto inválida o no pertenece a esta mascota';
            $this->redirect('/mascota/editar?id=' . urlencode((string)$idMascota));
        }

        // Verificar propiedad
        $mascota = \app\models\Mascota::findById($idMascota);
        if (!$mascota) {
            $_SESSION['flash_error'] = 'Mascota no encontrada';
            $this->redirect('/mascota');
        }
        $ownerId = $mascota['usuario_id'] ?? ($mascota['id'] ?? null);
        if ((string)$ownerId !== (string)$this->session->getUserId()) {
            $_SESSION['flash_error'] = 'No tienes permisos para esta acción';
            $this->redirect('/mascota/editar?id=' . urlencode((string)$idMascota));
        }

        $nuevaDescripcion = (string)($this->input('descripcion') ?? '');
        $nuevaDescripcion = trim($nuevaDescripcion);
        if (function_exists('mb_substr')) {
            $nuevaDescripcion = mb_substr($nuevaDescripcion, 0, 255, 'UTF-8');
        } else {
            $nuevaDescripcion = substr($nuevaDescripcion, 0, 255);
        }

        $oldUrl = (string)$foto['url'];
        $newUrl = null;
        $fileChanged = false;

        // Manejar reemplazo de archivo opcional
        if (isset($_FILES['nueva_foto']) && $_FILES['nueva_foto']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['nueva_foto'];
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
                        $newUrl = 'assets/images/mascotas/' . $fileName;
                        $fileChanged = true;
                    } else {
                        $_SESSION['flash_error'] = 'No se pudo guardar la imagen subida.';
                        $this->redirect('/mascota/editar?id=' . urlencode((string)$idMascota));
                    }
                } else {
                    $_SESSION['flash_error'] = 'Imagen inválida (tipo o tamaño). Máximo 5MB. Permitidos: JPG, PNG, GIF.';
                    $this->redirect('/mascota/editar?id=' . urlencode((string)$idMascota));
                }
            } else {
                $_SESSION['flash_error'] = 'Error al subir la imagen (código ' . intval($file['error']) . ').';
                $this->redirect('/mascota/editar?id=' . urlencode((string)$idMascota));
            }
        }

        // Armar y ejecutar actualización
        if ($fileChanged) {
            $ok = \DataBase::execute('UPDATE fotos_mascotas SET url = ?, descripcion = ? WHERE id_foto = ?', [$newUrl, ($nuevaDescripcion !== '' ? $nuevaDescripcion : null), $idFoto]) > 0;
        } else {
            // Solo descripción
            $ok = \DataBase::execute('UPDATE fotos_mascotas SET descripcion = ? WHERE id_foto = ?', [($nuevaDescripcion !== '' ? $nuevaDescripcion : null), $idFoto]) > 0;
        }

        if ($ok && $fileChanged) {
            // Si la principal coincide con la antigua URL, actualizarla a la nueva
            $isPrincipal = !empty($mascota['foto_url']) && (ltrim(strtolower($mascota['foto_url']), '/') === ltrim(strtolower($oldUrl), '/'));
            if ($isPrincipal) {
                $m = new \app\models\Mascota($mascota);
                $m->updatePhoto($newUrl);
            }
            // Intentar eliminar archivo viejo si era local
            $p = ltrim(str_replace('\\', '/', (string)$oldUrl), '/');
            if (strpos($p, 'assets/images/mascotas/') === 0) {
                $abs = __DIR__ . '/../../' . $p;
                if (is_file($abs)) { @unlink($abs); }
            }
        }

        $_SESSION['flash_' . ($ok ? 'success' : 'info')] = $ok ? 'Foto actualizada' : 'No hay cambios para guardar';
        $this->redirect('/mascota/editar?id=' . urlencode((string)$idMascota));
    }
}