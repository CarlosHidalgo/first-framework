<?php
namespace App;
/**
 * Clase utilizada para dibujar una página HTML
 * @author clopezh
 *
 */

abstract class View implements Schema{
	
	protected $user;
	
	public function __construct(){
		
		$user = \Security\Auth::getCurrentUser();
	
		if ($user){
			$this->user = $user;
		}else {
			$this->user = new \Models\User();
		}
		
	}
	
	
	
	/**
	 * Wrapper del método \Security\Policy::($user, $method, $uriName)
	 * @param unknown $user
	 * @param unknown $method
	 * @param unknown $uriName
	 * @return boolean
	 */
	protected function can($user, $method, $uriName){
		
		return  \Security\Policy::can($user, $method, $uriName);
		
	}
	
	/**
	 * Generá las uris de los nombres de los scripts proporcionados
	 * Utiliza el directorio public/assets
	 * @param array $scripts array(name1.js, name2.js);
	 */
	public function generateUriFileJS(array $scripts){
		$jss = "";
		$dir = \App\Route::assets('assets/js');
		foreach ($scripts as $script){
			$jss.= "<script src='$dir/$script'></script>";
		}
		return $jss;
	}
	
	/**
	 * Genera las uris de los nombres de los scripts proporcionados
	 * Utiliza el directorio public/assets 
	 * @param array $scripts array(name1.css, name2.css);
	 */
	public function generateUriFileCSS(array $scripts){
		$css = "";
		$dir = \App\Route::assets('assets/css');
		foreach ($scripts as $script){
			$css.= "<link href='$dir/$script' rel='stylesheet'>";
		}
		return $css;
	}
	
	/**
	 * Cargas las variables enviadas al View actual
	 * las almacena en $SUPERGLOBALS
	 */
	public function loadVars(){
		
		foreach ($_POST as $key => $val){
			$GLOBALS[$key] = $val;
		}
		
		foreach ($_GET as $key => $val){
			$GLOBALS[$key] = $val;
		}
	}
	
	/**
	 * Wrapper para aplicar una condición a sintaxis heredoc
	 * @param string $condition condición a ser evaluada
	 * @param string $true valor a devolver si la condición es correcta
	 * @param string $false valor a devolver si la condición es incorrecta
	 * @return string
	 */
	protected function condition($condition, $true, $false){
		return $condition ? $true : $false;
	}
	
	public function getPageHtml($params = null){
		$this->loadVars();
		
		$response = null;
		if (isset($params)){
			if (is_object($params)){
				$response = $params;
			}else{
				$response = (object) $params;
			}
		}
		
		$jsFiles = $this->jScriptFiles();
		$js = $this->jScript($response);
		$css = $this->ccsFiles();
		$body = $this->bodyContent($response);
		$footer = $this->footerContent();
		
		// Resources
		$ico = \App\Route::assets('assets/img/favicon.ico');
		$dirCss = \App\Route::assets('assets/css');
		$dirJs = \App\Route::assets('assets/js');
		$urlLogout = \App\Route::getForNameRoute(\App\Route::GET, 'logout');
		$urlLogin = \App\Route::getForNameRoute(\App\Route::GET, 'login');
		$urlPrincipal = \App\Route::getForNameRoute(\App\Route::GET, 'principal');

		$page=<<<PAGE
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<meta charset="utf-8">
		<title>Reverse Auction</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">	
		<link rel="shortcut icon" href="{$ico}" type="image/x-icon">
		<link rel="icon" href="{$ico}" type="image/x-icon">
		
		<link href="{$dirCss}/bootstrap.min.css" rel="stylesheet">
		<link href="{$dirCss}/jquery-ui-1.11.4.min.css" rel="stylesheet">
		<link href="{$dirCss}/general.css" rel="stylesheet">
		<link rel="stylesheet" href="{$dirCss}/font-awesome.min.css">
		{$css}
    
		<!--[if lt IE 9]>
			<script type="text/javascript" src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<script type="text/javascript" src="{$dirJs}/jquery-2.1.4.min.js"></script>
		<script src="{$dirJs}/jquery-ui-1.11.4.js"></script>
		<script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1.1','packages':['corechart']}]}"></script>
		<script type="text/javascript">
			{$js}
		</script>
		
	</head>
	<body>
	
	<!-- header -->
	<div id="top-nav" class="navbar navbar-inverse navbar-static-top">
	    <div class="container-fluid">
	        <div class="navbar-header">
	            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
	                <span class="icon-bar"></span>
	                <span class="icon-bar"></span>
	                <span class="icon-bar"></span>
	            </button>
	            <a class="navbar-brand" href="{$this->condition($this->user->getId() != null, $urlPrincipal, $urlLogin)}">Subasta Inversa</a>
	        </div>
	        <div class="navbar-collapse collapse">
	        	
	            <ul class="nav navbar-nav navbar-right" {$this->condition( $this->user->getId() != null, '','hidden') }>
	                <li class="dropdown">
	                    <a class="dropdown-toggle" role="menu" data-toggle="dropdown" href="#">
	                    	<i class="glyphicon glyphicon-user"></i> {$this->condition($this->user->getId() != null, $this->user->getName(), 'Usuario')} <span class="caret"></span>
	                    </a>
	                    		<ul class="dropdown-menu" role="menu">
	                        		<li>
	                        			<a href="{$urlLogout}"><i class="glyphicon glyphicon-off"></i> Salir</a>
	                        		</li>
	                    		</ul>
	                </li>
	                
	            </ul>
	        </div>
	    </div>
	    <!-- /container -->
	</div>
	<!-- /Header -->
	
  	
	<!-- Main -->
	<div class="container-fluid">
	    <div id="divLoading"> 
	    </div>
		{$body}
	</div>
	<!-- /Main -->

	
	<!-- FOOTER -->
	<footer class="text-center">
	{$footer}
		<a href="http://neurona.tech/" target="_blank"> Neurona Tech</a>
	</footer>
	<!-- FOOTER -->

	<!-- script references -->
		
    	<script src="{$dirJs}/bootstrap.min.js"></script>
		<script src="{$dirJs}/bootbox.min.js"></script>
		<script src="{$dirJs}/ajaxUtils.js"></script>
				
		{$jsFiles}
	</body>
</html>	
PAGE;
		return $page;
	}
	
	public function footerContent(){}
	public function jScriptFiles(){}
	
	public function ccsFiles(){}
	public function jScript($params = null){}
}
?>