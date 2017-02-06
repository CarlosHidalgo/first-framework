<?php
/**
 * Utilities for development
 * @author clopezh
 */

namespace App;

class Utils{
	
	/**
	 * Redirigir a otra dirección
	 * @param string $url a redirigir
	 * @param array $queryData valores enviados a la página como una consulta codificada.
	 * @param number $statusCode -> estado HTTP
	 */
	public static function redirect($url,  array $queryData = null, $statusCode = 303){
		
		$vars = '';
		if (isset($queryData)){
			$vars.= '?'.http_build_query($queryData);
		}
		
		$newUrl = $url.$vars;
		
		if (!headers_sent()){
			header('Location: ' . $newUrl, false, $statusCode);
		}
		else {
			$redirect = '<head><meta http-equiv="refresh" content="0;url='.$newUrl.'" /></head>';
		}
		exit();
	}
	
	/**
	 * Imprime el contenido de un archivo pasado en la $uri
	 * @param string $uri ubicación del templete application/views/X
	 */
	public static function makeHtml($uri){
		$view = file_get_contents($uri);
		echo $view;
	}
	
	/**
	 * Genera el código Html para la vista indicada
	 * la clase debe ser instancia de \App\Schema
	 * @param Schema $view
	 * @param $params  parámetros que serán enviados a pageHtml
	 */
	public static function makeView(\App\Schema $view, $params = null){
		try {
			$view = $view->getPageHtml($params);
			echo $view;
		}catch(\Exception $e){
			echo "Ah ocurrido un error al dibujar la vista: ".$e->getMessage(); 
		}
		
	}
	
	/**
	 * Determina si el argumento $function es una instancia de Closuere y puede ser ejecutado. 
	 * @param unknown $function -> función a evaluar
	 * @return boolean
	 */
	public static function isFunction($function){
		if (is_callable($function) && $function instanceof \Closure) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Cambia los valores de un array a referencias
	 * @param array $arr
	 * @return array 
	 */
	public static function makeArrayValuesToReferenced(array &$arr){
		$refs = array();
		foreach($arr as $key => $value){
			$refs[$key] = &$arr[$key];
		}
		return $refs;
	}
	
	/**
	 * Se verifica si la fecha se encuentra en rango
	 * @param unknown $firstToCompare fecha inicio de rango en cadena Y-m-d H:i:s
	 * @param unknown $secondToCompare fecha fin de rango en cadena Y-m-d H:i:s
	 * @param unknown $dateToCompare fecha a comparar en el rango en cadena Y-m-d H:i:s
	 * @return boolean
	 */
	public static function checkInRangeDate($firstToCompare, $secondToCompare, $dateToCompare){
	
		$firstDate = strtotime($firstToCompare);
		$secondDate = strtotime($secondToCompare);
		$compareDate = strtotime($dateToCompare);
	
		return (($compareDate >= $firstDate) && ($compareDate <= $secondDate));
	}
	
	
	/**
	 * Retorna un objeto DateTime con microsegundos para el formato Y-m-d H:i:s.u,
	 * con fecha y hora actual
	 * @return \DateTime
	 */
	public static function getDateTimeWithMillis(){
		$time = microtime(true);
		$micro = sprintf("%06d",($time - floor($time)) * 1000000);
		$dateTWM = new \DateTime( date('Y-m-d H:i:s.'.$micro, $time) );
		
		return $dateTWM;
	}
	
	/**
	 * Retorna la fecha y hora acutal del sistema
	 * @param $format 'yyyy/MM/dd HH:mm:ss'
	 * @return \date()
	 */
	public static function getDate($format = DATE_RFC2822){
	
		$dateTWM = date($format);
	
		return $dateTWM;
	}
	
	/**
	 * Generá una contraseña aleatoria
	 * @param number $length 
	 * @param string $encrypt
	 * @return string
	 */
	public static function randomPassword( $length = 8) {
		
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
		$password = substr( str_shuffle( $chars ), 0, $length );
		
		return $password;
	}
	/**
	 * Encripta la contraseña 
	 * @param unknown $password
	 * @return string
	 */
	public static function encryptPassword($password){
		$encrypt = $password = password_hash($password, PASSWORD_DEFAULT);
		return $encrypt;
	}
	
	
}
?>
