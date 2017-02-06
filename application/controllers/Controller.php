<?php

namespace Controllers;
/**
 * Controller base
 * @author clopezh
 *
 */
abstract class Controller{
	
	/**
	 * Ejecuta el método de la clase indicada
	 * @param string $className nombre de la clase en la que existe 'method'
	 * @param string $methodName nombre del método de clase
	 * @param string $parameters parámetros para el método
	 * @return mixed
	 */
	public static function executeMethod($className, $methodName, $parameters =  array()){

		$class = null; 
		$metodoReflexionado = new \ReflectionMethod($className, $methodName);
		
		if (!$metodoReflexionado->isStatic()){
			$class = new $className();
		}
		$response = $metodoReflexionado->invokeArgs($class, $parameters );
		
		return $response;
	}
}
?>