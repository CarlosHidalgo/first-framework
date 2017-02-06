<?php

/**
 * Excepción cuando se intenta executar un servicio sobre el que no se tiene permisos.
 * @author clopezh
 *
 */

namespace Exceptions; 

class MethodNotAllowedException extends \Exception{
	
	public function __construct($name = ''){
		$message = " No tiene permisos para ejecutar este método ".$name;
		parent::__construct($message);
	}
}
?>