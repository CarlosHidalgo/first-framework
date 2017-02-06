<?php

namespace Controllers;

/**
 * Controlador para el login de sesión.
 * @author clopezh
 */
class LoginController extends \Controllers\Controller{
	
	/**
	 * Despliega la vista de login
	 */
	public function showView(){
		
		if (\Security\Auth::check()){
			
			\App\Utils::redirect('principal');
		}
		return \App\Utils::makeView(new \Views\Core\LoginView());
	}
	
	/**
	 * Valida las credenciales para permitir acceso al sistema
	 */
	public function login(){
		
		$email = isset($_POST['inputEmail']) ? $_POST['inputEmail'] : '';
		$password = isset($_POST['inputPassword']) ? $_POST['inputPassword']: '';
		$aceptTC = isset($_POST['inputAcept']) ? boolval($_POST['inputAcept']): false;
		
		//aceptar terminos y condiciones
		if (!$aceptTC){
			$uri = \App\Route::getForNameRoute('GET', 'login');
			\App\Utils::redirect($uri, array("msg_error" => "Debe aceptar terminos y condiciones."));
		}
		
		$credentials = array('email' => $email, 'password' => $password);
		if (\Security\Auth::attempt($credentials)){
			$uri = \App\Route::getForNameRoute('GET', 'principal');
			return \App\Utils::redirect($uri);
		}else{
			$uri = \App\Route::getForNameRoute('GET', 'login');
			return \App\Utils::redirect($uri, array("msg_error" => "Usuario y/o contraseña invalida."));
		}
	}
	
	/**
	 * Salir del sistema y pantalla principal
	 */
	public function logout(){
		\Security\Auth::logout();
		\App\Utils::redirect('login');
	}
}
?>