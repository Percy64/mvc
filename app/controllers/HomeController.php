<?php

namespace app\controllers;

use Controller;
use Response;
use Session;

class HomeController extends Controller {
    protected $session;
    
    public function __construct() {
        $this->session = new Session();
    }
    
    /**
     * Página de inicio
     */
    public function actionIndex($var = null) {
        // Obtener mascotas perdidas para mostrar en inicio
        $mascotas = [];
        try {
            $mascotas = \app\models\Mascota::getMascotasPerdidas(12);
        } catch (\Throwable $e) {
            // Si hay error con la tabla de mascotas, continuar sin mascotas
            $mascotas = [];
        }
        
        $viewDir = $this->viewDir('app\\controllers\\');
        Response::render($viewDir, 'index', [
            'mascotas' => $mascotas,
            'session' => $this->session,
            'layout' => 'main',
            'title' => 'Inicio · BOTI'
        ]);
    }
    
    /**
     * Página 404
     */
    public function action404() {
        http_response_code(404);
        $viewDir = $this->viewDir('app\\controllers\\');
        Response::render($viewDir, '404', []);
    }
}