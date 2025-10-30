<?php 
/**
 * Clase encargada de la carga automática de clases
 */
class Autoloader
{
    public function __construct()
    {
        $this->loadAppClasses();
    }

    /**
     * Registra la función de autoload para cargar clases automáticamente
     */
    private function loadAppClasses()
    {
        spl_autoload_register(function ($nombreClase) {
            // Clases del core
            $clasesCore = ['App', 'Controller', 'Model', 'Response', 'DataBase', 'Session'];
            $esCoreClass = false;

            foreach ($clasesCore as $clase) {
                if (strstr($nombreClase, $clase)) {
                    $esCoreClass = true;
                    break;
                }
            }

            if ($esCoreClass) {
                // Cargar clases del core
                $archivo = CORE_PATH . $nombreClase . ".php";
                if (file_exists($archivo)) {
                    require_once $archivo;
                    return;
                }
            }

            // Manejar namespaces de la aplicación
            if (strpos($nombreClase, 'app\\') === 0) {
                // Remover el namespace 'app\' y reemplazar \ con /
                $rutaRelativa = str_replace('app\\', '', $nombreClase);
                $rutaRelativa = str_replace('\\', '/', $rutaRelativa);
                $archivo = APP_PATH . $rutaRelativa . ".php";
                
                if (file_exists($archivo)) {
                    require_once $archivo;
                    return;
                }
            }

            // Si no se encuentra, lanzar excepción
            throw new Exception("No se pudo cargar la clase: $nombreClase");
        }, true, false);
    }
}

// Instanciamos el Autoloader
new Autoloader();
