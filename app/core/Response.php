<?php 
/**
 * Clase para mostrar las vistas
 */
class Response
{
    // Constructor privado para evitar instanciación
    private function __construct() {}

    // Renderizar una vista con variables
    public static function render($viewDir, $view, $vars = [])
    {
        // Validar las variables antes de asignarlas dinámicamente
        foreach ($vars as $key => $value) {
            if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $key)) {
                $$key = $value;
            }
        }

        $viewPath = APP_PATH . "views/" . $viewDir . $view . ".php";

        // Verificar si la vista existe antes de cargarla
        if (file_exists($viewPath)) {
            // Soporte opcional de layout: si se pasa 'layout' => 'main',
            // renderizamos la vista en $content y luego incluimos el layout.
            $layoutName = isset($vars['layout']) ? trim($vars['layout']) : '';
            if ($layoutName !== '') {
                ob_start();
                require $viewPath;
                $content = ob_get_clean();

                $layoutPath = APP_PATH . "views/layouts/" . $layoutName . ".php";
                if (!file_exists($layoutPath)) {
                    throw new Exception("El layout {$layoutName} no existe en views/layouts");
                }
                require $layoutPath;
            } else {
                require $viewPath;
            }
        } else {
            throw new Exception("La vista $view no se encuentra en el directorio $viewDir");
        }
    }
}
