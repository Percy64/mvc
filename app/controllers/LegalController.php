<?php

namespace app\controllers;

use Controller;
use Response;

class LegalController extends Controller {
    protected $viewsDir = 'legal/';

    public function actionTerminos() {
        Response::render($this->viewsDir, 'terminos', [
            'layout' => 'main',
            'title' => 'Términos y Condiciones · BOTI Pet'
        ]);
    }

    public function actionPrivacidad() {
        Response::render($this->viewsDir, 'privacidad', [
            'layout' => 'main',
            'title' => 'Política de Privacidad · BOTI Pet'
        ]);
    }
}