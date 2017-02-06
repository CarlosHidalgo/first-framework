<?php

namespace Controllers;

/**
 * Controlador de las entidades (paych, sayrh)
 * @author maque
 *
 */
class EntityController extends \Controllers\Controller{

	/**
	 * Busqueda de estados de la subasta
	 */
	public function getAllEntitys(){

		$allEntities = \Models\Entity::all();

		return $allEntities;

	}
	
	/**
	 * Obtiene las entidades de la subasta dado los id´s proporcioandos
	 * @param unknown $params arreglo de id's de entidades
	 */	
	public function  getEntitysByAuction($params){
		
		$allEntities = \Models\Entity::getByIds($params);
		
		return $allEntities;
	}
}


?>