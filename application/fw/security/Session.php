<?php

/**
 * Manejo de sessiones
 * @author clopezh
 *
 */
namespace Security;

class Session{
	
	/**
	 * Iniciar sesión válida
	 */
	private static function start(){
		if (!isset($_SESSION)){
			session_start();
		}
	}
	
	/**
	 * destruye todos los datos de la sesión válida
	 */
	private static function destroy(){
		self::start();
		session_destroy();
	}
	
	/**
	 * Ingresa un valor en la sessión actual.
	 * @param string $key clave para identificar a $value
	 * @param string $value
	 */
	public static function put($key, $value){
		self::start();
		$_SESSION[$key] = $value;
	}
	
	/**
	 * Retorna el valor encontrado para la clave indicada en $key
	 * @param string $key
	 * @param object 
	 */
	public static function get($key){
		$value = null;
		if (self::has($key)){
			$value =  $_SESSION[$key];
		}
		return $value;
	}
	
	/**
	 * Determina si existe una clave en la sessión actual.
	 * @param string $key
	 * @return boolean
	 */
	public static function has($key){
		self::start();
		return isset($_SESSION[$key]);
	}
	
	/**
	 * Remueve la clave indicada en la sessión actual.
	 * @param string $key
	 */
	public static function forget($key){
		self::start();
		unset($_SESSION[$key]);
	}
	
	/**
	 * Retorna todas las variables de la sesión actual
	 */
	public static function all(){
		self::start();
		return $_SESSION;
	}
	
	/**
	 * Remueve todas las variables de la sesión actual.
	 */
	public static function flush(){
		self::destroy();
	}
	
}
?>