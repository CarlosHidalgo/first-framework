<?php

/**
 * Excepción para las claves primarias en los modelos.
 * @author clopezh
 *
 */
namespace Exceptions;

class PrimaryKeyNotFoundException extends \Exception{
	
	public function __construct($className, $id){
		$message = " La clase $className no contiene la propiedad $id definida";
		parent::__construct($message);
	}
}
?>