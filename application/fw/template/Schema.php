<?php
namespace  App;
/**
 * Interfaz para la creación de los templates html
 * @author clopezh
 *
 */
interface Schema{
	
	/**
	 * Retorna el contenido principal de la página
	 * @param array $params parámetros 
	 */
	public function bodyContent($params = null);
	
	/**
	 * Retorna los archivos css necesarios para la página
	 */
	public function ccsFiles();
	
	/**
	 * Retorna los archivos js necesarios para la página
	 */
	public function jScriptFiles();
	
	/**
	 * Retorna código java script embebido.
	 */
	public function jScript($params = null);
	
	/**
	 * Retorna el contenido del footer de la página
	 */
	public function footerContent();
	
	/**
	 * Retorna el código html.
	 * @return string
	 */
	public function getPageHtml($param = null);
	
}
?>