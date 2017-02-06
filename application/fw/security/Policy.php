<?php
namespace Security;
/**
 * Lógica de permisos sobre acciones en el sistema
 * @author clopezh
 *
 */
class Policy{
	
	/**
	 * Verifica que las claves de Rol enviadas sean válidas para el usuario indicado
	 * @param \Models\User $user usario a validar, debe tener cargado el role
	 * @param unknown $keysRoles clave o claves de rol a validar
	 * @return boolean
	 */
	public static function hasPermission(\Models\User $user, $keysRoles){
		
		$can = false;
		
		if (!is_array($keysRoles)){
			$keys = array($keysRoles);
		}else{
			$keys = $keysRoles;
		}
		
		foreach ($keys as $k){
			if (strcasecmp($user->getRole()->getKeyRole(), $k) == 0 || strcasecmp(\Models\Role::ALL_ROLES, $k) == 0){
				$can = true;
				break;
			}
		}
		
		return $can;
		
	}
	
	/**
	 * Verifica si una acción (nombre de uri) puede ejecutarse para el usuario indicado
	 * @param unknown $uriName clave de la ruta
	 * @param unknown $method POST | GET 
	 */
	public static function can(\Models\User $user, $method, $uriName){
		
		$route = \App\Route::getRoute($method, $uriName);
		$can = false;
		
		if (isset($route)){
			
			$keysRoles = $route[\App\Route::AUTHORIZATION];
			
			return  self::hasPermission($user, $keysRoles);
			
		}
		
		return $can;
	}
}

?>
