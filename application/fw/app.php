<?php
	
	/**-------------------------------------------------------
	 * Main de la aplicación.
	 * Inicializa los namespaces y el router para el control 
	 * de acceso al sistema.
	 *---------------------------------------------------------*/
	
	include_once 'LoaderClass.php';
	
	// +-------------------------------------------------------------------------+
	// [[GENERAR CERTIFICADOS Y CLAVES ]]
	// Descomente la linea de abajo solo si los certificados han caducado, una vez 
	// generados, vuelva a comentar la función, de lo contrario el sistema 
	// no funcionará correctamente.
	// +-------------------------------------------------------------------------+
	//\App\Configuration::generateSSLCertAndKeys();
	
	\App\Configuration::init();
	
	// +-------------------------------------------------------------------------+
	// [[ EXCEPCIONES ]]
	// +-------------------------------------------------------------------------+
	
	set_error_handler('exceptions_error_handler');
	
	function exceptions_error_handler($severity, $message, $filename, $lineno) {
		
		if (error_reporting() == 0) {
			return;
		}
		if (error_reporting() & $severity) {
			throw new ErrorException($message, 0, $severity, $filename, $lineno);
		}
	}
	
	
	
	// +-------------------------------------------------------------------------+
	// [[ ROUTER ]]
	// +-------------------------------------------------------------------------+
	$router = \App\Router::getRouter();
	$router->buildRequestUri();
	$router->executeRoute();
	
?>