<?php
/**
 * @author clopezh
 *
 */

namespace App;

/**
 * Procesa todas la entradas de Routes
 * @author clopezh
 *
 */
class Router {
	
	private static $router;
	private static $rootUrl;
	private $requestUri;
	private $requestMethod;
	private $query;
	private $ajax;
	
	private function __construct(){}
	
	private function __clone(){}
	
	private function __wakeup(){}
	
	/**
	 * Retorna una solo instancia de Router
	 * @return \App\Router
	 */
	public static function getRouter(){
		if (self::$router == null){
			self::$router = new Router();
		}
		return self::$router;
	}

	
	/**
	 * Obtiene la petición iniciada por el usuario
	 * P.E. www.pyo.com/serch/book/duperyon
	 * Return search/book/dupeyron
	 */
	public function buildRequestUri(){
		
		$url =  $_SERVER ['REQUEST_URI'];
		
		$parser = parse_url($url);
		$basepath = implode ( '/', array_slice ( explode ( '/', $_SERVER ['SCRIPT_NAME'] ), 0, -1 ) ) . '/';
		
		$rUri =   substr( $parser["path"], strlen ( $basepath ) );
		$rUri = trim ( $rUri, '/' );
		
		$this->requestMethod = $_SERVER['REQUEST_METHOD'];
		$this->requestUri = $rUri;
		$this->query = isset($parser["query"]) ?  $parser["query"] : null;
		$this->ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) ? true : false;
		 
	}
	
	/**
	 * Retorna la url principal de la aplicación.
	 */
	public static function getRootUrl(){
		
		if (!isset(self::$rootUrl)){
			$rurl = self::prepareBaseUrl();
			self::$rootUrl = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
			
			if ( parse_url($_SERVER['HTTP_HOST'],PHP_URL_PORT ) == null && isset($_SERVER['SERVER_PORT'])){
				self::$rootUrl .=':'.$_SERVER['SERVER_PORT'];
			}
			
			self::$rootUrl .=self::removeIndex($rurl);
		}
		return self::$rootUrl;
	}
	
	/**
	 * Retorna el nombre de dominio de la aplicación sin puerto
	 * @return string
	 */
	public static function getDomain(){
		
		$domain =  isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] :  php_uname('n');
		
		$path = parse_url($domain,PHP_URL_PATH);
		
		if ($path != null){
			$domain = $path; 
		}else{
			$host = parse_url($domain,PHP_URL_HOST);
			
			$domain = $host;
		}
		
		return $domain;
	}
	
	/**
	 * Búsca el directorio público báse
	 * @return string
	 */
	private static function prepareBaseUrl(){
		$fileName = basename($_SERVER['SCRIPT_FILENAME']);
		$baseUrl = '';
		if (basename($_SERVER['SCRIPT_NAME']) === $fileName) {
			$baseUrl = $_SERVER['SCRIPT_NAME'];
		} elseif ( basename($_SERVER['PHP_SELF']) === $fileName) {
			$baseUrl = $_SERVER['PHP_SELF'];
		} elseif ( basename($_SERVER['ORIG_SCRIPT_NAME']) === $fileName) {
			$baseUrl = $_SERVER['ORIG_SCRIPT_NAME']; // 1and1 shared hosting compatibility
		}
		
		return rtrim($baseUrl, '/');
	}
	
	/**
	 * Remove the index.php file from a path.
	 *
	 * @param  string  $root
	 * @return string
	 */
	private static function removeIndex($root)
	{
		$i = 'index.php';
	
		return strpos($root, $i) ? str_replace('/'.$i, '', $root) : $root;
	}
	
	/**
	 * Retorna la url solicitada p.e. de www.pyo.com/search/auction/0001
	 * se obtendrá -> search/auction/0001
	 * @return string
	 */
	public function getRequestUri() {
		return $this->requestUri;
	}
	
	public function getRequestMethod(){
		return $this->requestMethod;
	}
	
	/**
	 * Retorna los campos de busqueda para solicitudes GET
	 * p.e. www.pyo.com/?parametro1=2&parametros2=2
	 * obtendriamos parametro1=2&parametros2=2
	 * @return String
	 */
	public function getQuery(){
		return $this->query;
	}
	
	/**
	 * Retorna la URL enviada en un arreglo, delimitador '/'
	 * @param unknown $base_url uri
	 * @return multitype:
	 */
	public function uriToArray($base_url){
		$route = array ();
		$routesAux = explode ( '/', $base_url );
		
		foreach ( $routesAux as $r ) {
			if (trim ( $r ) != '')
				array_push ( $route, $r );
		}
		return $route;		
	}
	
	/**
	 * Executa el método asociado a la ruta especificada
	 */
	public function executeRoute(){
		
		$uri = $this->requestUri;
		$posibleRoutes = \App\Route::getPosibleRoutes($this->requestMethod);
		$patron = '/{[a-zA-Z0-9]*}/';
		$uri = trim($uri, '/');
		$response = false;
		
		foreach ($posibleRoutes as $route){
			
			$auxRoute =  trim($route[\App\Route::REQUEST_URI], "/");
			$auxUri = $uri;
			$localizedParameters = null;
			preg_match_all($patron, $auxRoute, $localizedParameters, PREG_OFFSET_CAPTURE);
			$parameters = array();
			$matchesRoute = array();
			
			// [[ PARAMETROS ]]
			if ($localizedParameters && $localizedParameters[0]  ){
					
				$pp = strpos($route[\App\Route::REQUEST_URI],"{");
				$matchesRoute = explode("/", substr($auxRoute, $pp));
				$matchesUri = explode("/", substr($auxUri, $pp));

				$auxRoute = substr($auxRoute, 0, $pp);
				$auxRoute = trim($auxRoute, "/");
				
				for ($i = 0; $i < count($matchesRoute); $i++){
					if (preg_match_all($patron, $matchesRoute[$i], $output, PREG_OFFSET_CAPTURE)){
						if (isset($matchesUri[$i])){
							$auxRoute.='/'.$matchesUri[$i];
							$parameters[] = $matchesUri[$i];
						}else{
							break;
						}
					}else{
						$auxRoute.='/'.$matchesRoute[$i];
					}
				}// FinFor
				
			}
			$auxRoute = trim($auxRoute, "/");
			$auxUri = trim($auxUri, "/");
			
			// [[ FOUND ROUTE ]] 
			if ( strcmp($auxRoute, $auxUri) == 0 ){
				
				if ( isset($route[\App\Route::FILTER]) ){
					
					if ( !$this->tryExecute($route[\App\Route::FILTER], array(), false) ){
						
						if ( $this->ajax ){
							exit();
						}else{
							$uri = \App\Route::getForNameRoute('GET', 'login');
							\App\Utils::redirect($uri);
						}
					}
				}
				
				if ( isset( $route[\App\Route::AUTHORIZATION]) ){
					
					$user = unserialize(\Security\Session::get(\Models\User::TABLE));
					
					$permission = \Security\Policy::hasPermission($user, $route[\App\Route::AUTHORIZATION]);
						
					if (!$permission){

						return $this->ouputResponse($permission, false);
					}
					
				}
				
				return $this->tryExecute($route[\App\Route::METHOD], $parameters);
				
			}
		}// Fin For posibleRoutes
		
		if (!$response){
			if (!\Security\Auth::check()){
				$uri = \App\Route::getForNameRoute('GET', 'login');
				\App\Utils::redirect($uri);
			}else{
				$uri = \App\Route::getForNameRoute('GET', 'principal');
				\App\Utils::redirect($uri);
			}
		}else{
			return $response;
		}
	}
	
	/**
	 * Intentará ejecutar un closure o un método de un controller.
	 * @param unknown $possibleMethod
	 * @param array $parameters parámetros del método a ejecutar
	 * @param boolean $httpResponse indica que la respuesta a devolver proviene de una peticion HTTP
	 */ 
	private function tryExecute($possibleMethod, $parameters, $httpResponse = true){
		$response = false;
		if (\App\Utils::isFunction($possibleMethod)){
			
			$response = call_user_func_array($possibleMethod, $parameters );
			
		}else if (is_string($possibleMethod)){
			//Controller@method
			$sMethod = explode('@', $possibleMethod);
			$className = $sMethod[0];
			$methodName = $sMethod[1];
			
			$response =  \Controllers\Controller::executeMethod($className, $methodName, $parameters);
		}
		
		return $this->ouputResponse($response, $httpResponse);
	}
	
	/**
	 * Envia la respuesta obtenida de los controllers, si la petición es HTTP POST o por AJAX se usará
	 * un echo | print , en otros casos se usará un return para poder obtener el valor como respuesta del método
	 * @param unknown $response
	 * @param boolean $httpResponse indica que la respuesta a devolver proviene de una peticion HTTP
	 * @return string
	 */
	private function ouputResponse($response, $httpResponse){
		
		if ( ($this->ajax || strcasecmp($this->requestMethod, \App\Route::GET) == 0 ) && $httpResponse ){
			
			if (is_array($response) || is_object($response)){
				$response = json_encode($response);
			}
			
			echo $response;
	
		}else{
	
			return $response;
		}
	}
}

?>
		