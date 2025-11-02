<?php 
namespace app\controllers;
use \Controller;
use app\models\UserModel;

class UserController extends Controller
{
    // Constructor
    public function __construct(){

    }
    
	public function actionIndex($var = null){
		self::action404();
	}

	/*obtiene todos los datos de un usuarios por id*/
	public static function GetUser($id){
		# obtener datos de usuario por Id
		$userData = UserModel::findId($id);
		return $userData;
	}

	/*obtiene todos los datos de un usuarios token*/
	public static function GetUserbytoken($token){

		# obtener datos de usuario por token
		$userData = UserModel::GetUserbytoken($token);

		return $userData;
	}

	/*Validad si el Usuario se encuentra en estado Activo por ID*/
	public static function checkActivo($userId){
		$datosUsuario = UserModel::findId($userId);
		// var_dump($datosUsuario);
		if ($datosUsuario) {
			if ($datosUsuario->activo == 'si') {
				$result =  true;
			}else{
				// $result = 'El Usuario no se encuentra activo!';
				$result = 'Problemas al acceder a la cuenta! <br>(UC-#42)';
			}
		}else{
			$result = false;
		}
		return $result;
	}


}