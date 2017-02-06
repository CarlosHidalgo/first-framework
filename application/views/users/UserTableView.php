<?php

namespace Views\Users;

class UserTableView extends \Views\Core\TableView {
	
	public function bodyContent($params = null){
		$rows = '';
		if (isset($params)){
		
			$rows =<<<ROWS
		
							<tr class="info" >
								<th><span class="glyphicon glyphicon-menu-hamburger"></span> Clave</th>
                                 <th><span class="glyphicon glyphicon-user"></span> Nombre </th>
                                 <th><span class="glyphicon glyphicon-envelope"></span> Email</th>
         						<th><span class="glyphicon glyphicon-tags"></span> Tipo</th>
                                 <th><span class="glyphicon glyphicon-cog"></span> Operaciones</th>
                            </tr>
ROWS;
			
			$roles = $params->roles;
			foreach ($params->users as $user){
					
					$role = isset($roles[$user->getIdRole()]) ? $roles[$user->getIdRole()]->getName() : '' ;
					$uriDeactivate = \App\Route::getForNameRoute(\App\Route::GET, 'users-activate', array($user->getId(), 0) );
					$uriActivate = \App\Route::getForNameRoute(\App\Route::GET, 'users-activate', array($user->getId(), 1) );
					$uriEditUser = \App\Route::getForNameRoute(\App\Route::GET, 'users-create-edit', array($user->getId()));
					
					$rows .=<<<ROWS
							<tr>
								 <td>{$user->getKeyUser()}</td>
                                 <td>{$user->getName()}</td>
                                 <td>{$user->getEmail()}</td>
                                 <td>{$role}</td>
                                 <td>
                                  <div class="btn-group">

	                                <a class="btn btn-primary glyphicon glyphicon-pencil" title="Editar" 
	                                	href="{$uriEditUser}"></a>

	                                <a class="changeState btn btn-danger glyphicon glyphicon-thumbs-down {$this->condition($user->isActive(), '', 'hidden')}"
	                                	href="{$uriDeactivate}" 
	                                	data-toggle="tooltip" data-placement="top" title="Desactivar"
	                                	msgConfirmation="¿Desea DESACTIVAR al usuario {$user->getName()}?"/>
	                                 
	                                <a class="changeState btn btn-success glyphicon glyphicon-thumbs-up {$this->condition($user->isActive(), 'hidden', '')}" 
	                                	href="{$uriActivate}"
	                                	title="Activando" msgConfirmation="¿Desea ACTIVAR al usuario {$user->getName()}?"/>
	                                 
                                  </div>
                                  </td>
                              </tr>
ROWS;
			}
		}
		
		return $rows;
	}
	
}


?>