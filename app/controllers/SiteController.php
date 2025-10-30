<?php 
namespace app\controllers;
use \Controller;
use app\models\CategoriaModel;

class SiteController extends Controller
{
    // Constructor
    public function __construct(){
        // self::$sessionStatus = SessionController::sessionVerificacion();
    }

	public static function head(){
		// Base para assets públicos (css/js) siempre bajo /public
		$base = static::path();
		if (substr($base, -8) !== 'public/') {
			$base .= 'public/';
		}
		$head = file_get_contents(APP_PATH.'/views/inc/head.php');
		$head = str_replace('#PATH#', $base, $head);
		return $head;
	}

	public static function menu(){
	}
}
