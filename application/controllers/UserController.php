<?php

/**
 * Controlador para los usuarios del sistema.
 * @author clopezh
 *
 */
namespace Controllers;

class UserController extends \Controllers\Controller{
	
	/**
	 * Despliega la vista de búsqueda de usuarios
	 */
	public function showView(){
		
		$allRoles = \Models\Role::all(); 
		
		return \App\Utils::makeView(new \Views\Users\SearchUserView(), array('roles' => $allRoles));
	}
	
	/**
	 * Búsqueda de usuarios por número de página
	 * @param unknown $page
	 */
	public function search($page){
		
		$parameters = array();
		
		if (isset($_POST['email']) && strcmp("", trim($_POST['email'])) != 0){
			$parameters['email'] =  array("%".$_POST['email']."%", 'LIKE');
		}
		
		if (isset($_POST['name']) && strcmp("", trim($_POST['name'])) != 0 ){
			$parameters['name'] =  array("%".$_POST['name']."%", 'LIKE');
		}
		
		if (isset($_POST['key']) && strcmp("", trim($_POST['key'])) != 0){
			$parameters['keyUser'] =  array("%".$_POST['key']."%", 'LIKE');
		}
		
		if (isset($_POST['role']) && strcmp("", trim($_POST['role'])) != 0){
			$parameters['idRole'] =  array($_POST['role'], '=');
		}
		
		$uri = 'users-search';
		$dtc = new \Controllers\UserDataTableController($uri, \Models\User::class);
		
		$users = $dtc->search($page, $parameters);
		
		$paginator = $dtc->getPaginator();
		
		$total = $dtc->getTotal();
		
		$allRoles = \Models\Role::all();
		
		$roles = array();
		
		foreach($allRoles as $role){
			$roles[$role->getId()] = $role;
		}
		
		$table = new \Views\Users\UserTableView();
		
		return \App\Utils::makeView($table, array ('users' => $users  , 'roles' => $roles, 'paginator' => $paginator, 'total' => $total) );
		
	}
	
	/**
	 * Activa o desactiva un usuario,
	 * @param int $id del usuario
	 * @param int $activate booleano que indica si se activa o desactiva al usuario.
	 */
	public function activate($id, $activate){
		$user = \Models\User::findById($id);
		
		$user->setActive($activate);
		$response = $user->saveOrUpdate();
		$mensaje = '';
		$msgActive = $activate ? 'ACTIVADO' : 'DESACTIVADO';
		$exito = "¡Usuario ".$msgActive." correctamente!";
		
		if ($response){
			$mensaje = json_encode(array('msgSuccess'=>$exito));
		}else{
			$mensaje = json_encode(array('msgError'=>$response));
		}
		
		return $mensaje;
	}
	
	/**
	 * Crear o editar un usuario 
	 */
	public function createEditUserView($id){
		$user = \Models\User::findById($id);
		
		$allRoles = \Models\Role::all();
		
		$roles = array();
		foreach($allRoles as $role){
			$roles[$role->getId()] = $role;
		}
		
		$usertStatus = array(1 => 'Activo', 0 => 'Inactivo');
		
		\App\Utils::makeView(new \Views\Users\CreateEditUserView(), array(  'user' => $user, 'roles' => $roles, 'status' => $usertStatus) );
	}
	
	/**
	 * Crear o editar usuario
	 */
	public function createEditUser(){
		
		$user = null;
		$isValid = true;
		$errors = 'Los siguientes campos presentan errores: <ul>';
		
		if (isset($_POST['id']) && strcmp("", $_POST['id']) != 0 ){
			$user = \Models\User::findById($_POST['id']);
		}
		
		if (!isset($user)){
			$user = new \Models\User();
		}
		
		if (isset($_POST['email']) && strcmp("", $_POST['email']) != 0){
			$user->setEmail(trim($_POST['email']));
		}else{
			$errors.= "<li> email </li>";
			$isValid = false;
		}
		
		if (isset($_POST['name']) && strcmp("", $_POST['name']) != 0 ){
			$user->setName(trim($_POST['name']));
		}else{
			$errors.= "<li> nombre no debe ser nulo </li>";
			$isValid = false;
		}
		
		if (isset($_POST['password']) && strcmp("", $_POST['password']) != 0 ){
			$psw = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
			$user->setPassword($psw);
		} 
		
		if (isset($_POST['key']) && strcmp("", $_POST['key']) != 0){
			$user->setKeyUser(trim($_POST['key']));
		}else{
			$errors.= "<li> clave de usuario no debe ser nulo</li>";
			$isValid = false;
		}
		
		if (isset($_POST['idRole']) && strcmp("", $_POST['idRole']) != 0){
			$user->setIdRole(trim($_POST['idRole']));
		}else{
			$errors.= "<li> identificador de tipo (rol) no debe ser nulo</li>";
			$isValid = false;
		}
		
		if (isset($_POST['active']) && strcmp("", $_POST['active']) != 0){
			$user->setActive(trim($_POST['active']));
		}else{
			$errors.= "<li> activo o inactivo no puede ser nulo</li>";
			$isValid = false;
		}
		
		if ($isValid){
			
			$response = $user->saveOrUpdate();
			
			if ( $response === true || $response == 1){
				$keyU = $user->getKeyUser();
				$uriCancel = \App\Route::getForNameRoute(\App\Route::GET, 'users');
				
				$msg = "¡Usuario $keyU creado/actualizado correctamente! <br><br/> 
					<a class='btn btn-danger' href='$uriCancel' >Regresar a búsqueda</a> ";
				
				return json_encode(array('msgDefault'=> $msg));
			}else{
				
				return json_encode(array('msgError'=>$response));
			}
		}else{
			$errors.= '</ul>';
			
			return json_encode(array('msgError'=>$errors));
		}
		
	}
	
	/** 
	 * Carga la información de rol del usuario
	 * @param \Models\User $user
	 */
	public function loadRole(\Models\User $user){
				
		$pk = \Models\Role::PRIMARY_KEY;
		$role = \Models\Role::findOne(array(  $pk => $user->getIdRole() ));
		
		$user->setRole($role);
		
	}
	
	/**
	 * Busca todos los usuarios de un rol dado
	 * @param $keyRole clave del Rol (MANAGER, AUDITOR, SUPPLIER)
	 */
	public function getProvidersByRol($keyRole){
		
		$rolController= new \Controllers\RoleController();
		$rol = $rolController->getRole(array('keyRole' => $keyRole));
		$allProviders = \Models\User::find(array('idRole' => $rol->getId()));		
		return $allProviders;
	}
	
	/**
	 * Busca todos los usuarios confirmados de una subasta
	 * @param $parameters = idAuction
	 */
	
	public function getConfirmedProvidersByAuction($parameters){
		$providers = new \Models\User();
		$providers = $providers->getConfirmedProvidersByAuction($parameters);
		
		return $providers;
	}
	
	/**
	 * Busca usuarios por su ID
	 * @param $parameters = idUser
	 * @return object \Models\User
	 */
	
	public function findByidUser($params){
		
		$user = \Models\User::findById($params);
		
		return  $user;
	}
}
?>