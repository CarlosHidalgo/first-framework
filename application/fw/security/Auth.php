<?php
/**
 * Autentificación de usuario.
 */
namespace Security;

use App\Log;

class Auth{
	
	/**
	 * Intenta la autentificación del usuario
	 * @param array $credentials email y password
	 * @return boolean
	 */
	public static function attempt(array $credentials){
		
		$parameters = array('email' => $credentials['email']);
		unset($credentials['email']);
		
		// Hacer la validación con usuario  y contraseña!
		$user = \Models\User::findOne($parameters);
		
		if (isset($user)){
			if (password_verify($credentials['password'], $user->getPassword())){
				self::generateTokenSesion($user->getKeyUser());	
				
				$uc = new \Controllers\UserController();
				$uc->loadRole($user);
				
				$user->setPassword(null);
				$serializeUser = serialize($user);
				
				\Security\Session::put(\Models\User::TABLE, $serializeUser);
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Función para filtrar rutas que merecen estar logueado
	 */
	public static function authenticityFilter(){
		if (!self::check()){
			self::logout();
			return false;
		}
		return true;
	}
	
	/**
	 * Verifica que el token de usuario sea válido para mantener sessión.
	 * @param token identificador de sessión
	 * @return boolean
	 */
	public static function check($token = null){
		$isValid = false;
		$genConfigs = \App\Configuration::getGeneralConfigs();
		
		if (is_null($token) && isset($_COOKIE[$genConfigs['cookieName']])){
			
			$token =   $_COOKIE[$genConfigs['cookieName']]; 
		}
		
		try{
			$jws        = \Namshi\JOSE\SimpleJWS::load( $token );
			$certtt = \App\Configuration::getRootPath().'\application\config\key\pyo_cert.pem';
			$public_key = openssl_pkey_get_public("file://".$certtt);
				
			// Verificar el token y verificar que es el mismo que se género al crear el cookie
			if ($jws->isValid($public_key, 'RS256')) {
				$isValid = true;
			}
		}catch (\Exception $e){
			Log::info($e->getMessage(), Auth::class);
			$isValid = false;
		}
		
		return $isValid;
	}
	
	/**
	 * Forza la destrucción del token de sesión actual así como de todas las variables almacenadas
	 */
	public static function logout(){
		$genConfigs = \App\Configuration::getGeneralConfigs();
		
		if (isset($_COOKIE[$genConfigs['cookieName']]) ){
			setcookie($genConfigs['cookieName'], '', time() - 3600,'/', \App\Router::getDomain());
		}
		\Security\Session::flush();
		
	}
	
	
	/**
	 * Genera los token del servidor y el cliente para la autentificación de usuarios
	 * JWT
	 * @param resource $keyUser
	 * @param optional string $password 
	 */
	private static function generateTokenSesion($keyUser, $password = null){
		$genConfigs = \App\Configuration::getGeneralConfigs();
		$jws  = new \Namshi\JOSE\SimpleJWS(array(
				'alg' => 'RS256'
		));
		
		$expiration       = time() + (60 * $genConfigs['lifetime']);
		$jws->setPayload(array(
				'uid' => $keyUser,
				'exp' => $expiration
		));
		
		$dirPrivkey = \App\Configuration::getRootPath().'/application/config/key/pyo_privateKey.pem';
        $fo = fopen($dirPrivkey, 'r');
		$privKey = fread($fo, filesize($dirPrivkey));
		fclose($fo);
            
		$privateKey = openssl_pkey_get_private($privKey, $genConfigs['passphrase']);
		
		$jws->sign($privateKey,$password);
		setcookie($genConfigs['cookieName'], $jws->getTokenString(), $expiration,'/', \App\Router::getDomain());
	}
	
	/**
	 * Retorna el usuario de la sessión actual
	 * @return mixed
	 */
	public static function getCurrentUser(){
		
		$user = unserialize(\Security\Session::get(\Models\User::TABLE));
		
		return $user;
	}
	
}
?>