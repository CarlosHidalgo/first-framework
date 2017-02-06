<?php

namespace Controllers;

/**
 * Controlador de roles
 * @author maque
 *
 */
class RoleController extends \Controllers\Controller{
	
	/**
	 * Devuelve object(Models\Role)
	 * @param 
	 */
	
	public function getRole(array $params){
	
	$role = \Models\Role::findOne($params);	
	return $role;
	}
}


?>