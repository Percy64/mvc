<?php 
class Controller
{
    protected $title = 'MVC Proyecto | ';
    protected static $sessionStatus;
    public static $ruta;

    public function actionIndex($var = null){
        $this->action404();
        // echo "funcionando";
    }
    
    // Obtiene el path base para la URL de forma robusta
    // Ej.: si SCRIPT_NAME = /MVC/public/index.php => base = /MVC/public/
    public static function path()
    {
        $script = $_SERVER['SCRIPT_NAME'] ?? '/';
        $base = rtrim(dirname($script), '/');
        // Asegurar barra final
        if ($base !== '') { $base .= '/'; } else { $base = '/'; }
        self::$ruta = $base;
        return self::$ruta;
    }

    // Base del proyecto (carpeta contenedora de /public).
    // Si la base termina en /public/, devolver la base sin ese sufijo.
    public static function rootBase()
    {
        $base = static::path();
        if (substr($base, -8) === 'public/') {
            return substr($base, 0, -8);
        }
        return $base;
    }

    protected function viewDir($nameSpace){
        $replace = array($nameSpace,'Controller');
        $viewDir = str_replace($replace , '', get_class($this)).'/';
        $viewDir = str_replace('\\', '', $viewDir);
        $viewDir = strtolower($viewDir);
        return $viewDir;
    }

    public function action404(){
        // echo "Error 404 - Página no encontrada - CONTROLLER";
        static::path();
        header('Location:'.self::$ruta.'404');
    }

    // Genera un token de seguridad
    public static function generarToken($longitud = 32)
    {
        return bin2hex(random_bytes($longitud));
    }

    // Genera un token de seguridad simplificado
    protected static function tokenSeguro($longitud = 25)
    {
        return self::generarToken($longitud);
    }

    /**
     * Verificar si el usuario está logueado
     */
    protected function requireAuth() {
        $session = new Session();
        if (!$session->estaLogueado()) {
            $this->redirect('usuario/login');
        }
    }

    /**
     * Obtener datos POST/GET
     */
    protected function input($key = null, $default = null) {
        $data = array_merge($_GET, $_POST);
        
        if ($key === null) {
            return $data;
        }
        
        return $data[$key] ?? $default;
    }

    /**
     * Validar token CSRF (básico)
     */
    protected function validateCsrf($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generar token CSRF
     */
    protected function generateCsrf() {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }

    /**
     * Redireccionar
     */
    protected function redirect($url) {
        // Construir URL absoluta basada en la ruta pública detectada
        $baseUrl = static::path(); // e.g. /MVC/public/
        $fullUrl = $baseUrl . ltrim($url, '/');
        header("Location: " . $fullUrl);
        exit;
    }

    /**
     * Devolver JSON
     */
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
