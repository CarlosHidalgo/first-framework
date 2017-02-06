<?php
namespace App;

/**
 * @author clopezh
 *
 */
class Route{
	
	const REQUEST_URI = 'REQUEST_URI';
	const METHOD = 'METHOD';
	const URI_NAME = 'URI_NAME';
	const AUTHORIZATION = 'AUTHORIZATION';
	const FILTER = 'FILTER';
	
	const POST = 'POST';
	const GET = 'GET';
	
	public static $gets = array();
	public static $posts = array();
	
	/**
	 * Agrega una ruta a las peticiones GET
	 * @param string $request URI a la que respondera la petición
	 * @param closure $function clouser o methodo de clase que se ejecutará tras la petición
	 * @param string $uriName nombre que identifique a la uri de forma mas amigable
	 * @param <array, string> $authorization clave de autorización ROLE
	 * @param filter función que se ejecuta antes de la llamada a <function>
	 */
	public static function get($request,$function, $uriName = '', $filter = null, $authorization = null){
		self::$gets[] = array(self::REQUEST_URI => $request, self::METHOD => $function, self::URI_NAME => $uriName, self::AUTHORIZATION => $authorization, self::FILTER => $filter);
	}
	
	/**
	 * Agrega una ruta a las peticiones POST
	 * @param string $request URI a la que respondera la petición
	 * @param closure $function clouser o methodo de clase que se ejecutará tras la petición
	 * @param string $uriName nombre que identifique a la uri de forma mas amigable
	 * @param <array, string> $authorizationclave de autorización ROLE
	 * @param filter función que se ejecuta antes de la llamada a <function>
	 */
	public static function post($request, $function, $uriName = '', $filter = null , $authorization = null){
		self::$posts[] = array(self::REQUEST_URI => $request, self::METHOD => $function, self::URI_NAME => $uriName, self::AUTHORIZATION => $authorization,self::FILTER => $filter);
	}
	
	/**
	 * Retorna la url solicitada si coincide el nombre amigable con una de las rutas del sistema
	 * @param string $method POST o GET
	 * @param string $nameRoute nombre de la ruta a buscar
	 * @param array $params conjunto de parámetros requeridos por la ruta
	 */
	public static function getForNameRoute($method, $nameRoute, $params = array()){
		
		$route = self::getRoute($method, $nameRoute);
		
		if (isset($route)){
			
			$patron = '/{[a-zA-Z0-9]*}/';
			$localizedParameters = null;
			$auxRoute = $route[self::REQUEST_URI];
			
			preg_match_all($patron, $auxRoute, $localizedParameters, PREG_OFFSET_CAPTURE);
			
			if (isset($params) && isset($localizedParameters)){
				foreach ($localizedParameters[0] as $index => $value){
					$auxRoute = str_replace($value[0], $params[$index], $auxRoute);
				}
			}
			
			$auxRoute = trim($auxRoute, '/');
			$request = \App\Router::getRootUrl().'/'.$auxRoute;
			
			return $request;
			
		}
		
		return null;
	}
	
	/**
	 * Obtiene la RUTA{$request,$function, $uri,$filter,$authorization} por clave de nombre $nameRoute
	 * @param unknown $method GET | POST
	 * @param unknown $nameRoute
	 * @return NULL
	 */
	public static function getRoute($method, $nameRoute){
		$routes = self::getPosibleRoutes($method);
		
		foreach ($routes as $route){
	
			if (strcmp($route[static::URI_NAME], $nameRoute) == 0){
				return $route;
			}
		}
		
		return null;
	}
	
	/**
	 * Retorna un recurso público del sistema
	 * localizado en la carpeta assets del directorio público
	 */
	public static function assets($resocurce){
		return \App\Router::getRootUrl().'/'.$resocurce;
	}
	
	
	/**
	 * Retorna las rutas correspondientes al metodo seleccionado
	 * @param unknown $method
	 * @return multitype:
	 */
	public static function getPosibleRoutes($method){
		$posibleRoutes = array();
		switch ($method) {
			case self::GET:
				$posibleRoutes = \App\Route::$gets;
				break;
			case self::POST:
				$posibleRoutes = \App\Route::$posts;
				break;
					
			default:
	
				break;
		}
		return $posibleRoutes;
	}
	
}

?>