<?php

namespace Views\Users;

class CreateEditUserView extends \App\View{
	
public function jScript($params = null){
		$js = <<<SCRIPT
				
		jQuery(function($){	
			$('body').tooltip({
    			selector: '.glyphicon'
			});
			$.setAjax({});
		});
		
SCRIPT;
		return $js;
	}
	
	public function bodyContent($params = null){
		
		$uriNewEditUser = \App\Route::getForNameRoute(\App\Route::POST, 'users-create-edit');
		$uriCancel = \App\Route::getForNameRoute(\App\Route::GET, 'users');
		
		$user = new \Models\User();
		$rolesView = '<option value="">Seleccione tipo</option>';
		$statusView = '';
		if (isset($params)){
			
			if (isset($params->user)){
				$user = $params->user;
			}
			
			// [[ ROLES ]]
			if (isset($params->roles)){
				$selected = 'selected';
				foreach ($params->roles as $rol){
					if ($user->getIdRole() == $rol->getId()){
						$selected = 'selected';
					}else{
						$selected = '';
					}
					$rolesView.= "<option $selected value='{$rol->getId()}'>{$rol->getName()}</option>"; 
				}
			}
			
			// [[ STATUS ]}
			if(isset($params->status)){
				$selected = 'selected';
				foreach ($params->status as $key => $status){
					if ($user->isActive() == $key){
						$selected = 'selected';
					}else{
						$selected = '';
					}
					$statusView.= "<option $selected value='{$key}'>{$status}</option>";
				}
			}
		}
		
		$body = <<<BODY
		
		<!-- Form validations -->              
              <div class="row">
                  <div class="col-lg-12">
					
                      <div class="panel panel-info">
                          <div class="panel-heading"> <h4>Crear/Editar Usuario</h4></div>
                          <div class="panel-body">
                              		
                          			<form id="searchForm" class="form-horizontal" role="form" action="{$uriNewEditUser}" method="post">
                          		
                                      <div class="form-group ">
                                          <label for="name" class="control-label col-lg-2">Nombre: <span class="required">*</span></label>
                                          <div class="col-lg-10">
                                          	  <input class="form-control" id="id" name="id" type="hidden" value="{$user->getId()}" required />
                                              <input class="form-control" id="name" name="name" type="text" value="{$user->getName()}" required />
                                          </div>
                                      </div>
                                      <div class="form-group ">
                                          <label for="email" class="control-label col-lg-2">Correo electrónico: <span class="required">*</span></label>
                                          <div class="col-lg-10">
                                              <input class="form-control " id="mail" type="email" name="email" value="{$user->getEmail()}" required />
                                          </div>
                                      </div>
                                      <div class="form-group ">
                                          <label for="key" class="control-label col-lg-2" required>Clave:*</label>
                                          <div class="col-lg-10">
                                              <input class="form-control " id="key" type="text" name="key" value="{$user->getKeyUser()}" required/>
                                          </div>
                                      </div>
                                      
                                      <div class="form-group ">
                                          <label for="password" class="control-label col-lg-2" required>Contraseña:</label>
                                          <div class="col-lg-10">
                                              <input class="form-control " id="password" type="password" name="password" value="" />
                                          </div>
                                      </div>
                                      
                                      <div class="form-group ">
                                          <label for="active" class="control-label col-lg-2">Estado*:</label>
                                          <div class="col-lg-10">
                                              <select class="form-control" name="active" required>
												  {$statusView}
											</select>
                                          </div>
                                      </div>
                                                                            
                                      <div class="form-group ">
                                          <label for="idRole" class="control-label col-lg-2">Tipo*:</label>
                                          <div class="col-lg-10">
                                              <select class="form-control" name="idRole" required>
												  {$rolesView}
											</select>
                                          </div>
                                      </div>
                                      <div class="form-group">
                                          <div class="col-lg-offset-2 col-lg-10">
                                          	 <input class="btn btn-success" name="submit" type="submit" value="Aceptar"/>
                                              
                                              <a class="btn btn-danger" type="button" href="{$uriCancel}">Cancelar</a>
                                          </div>
                                      </div>
                                  </form>
                              

                          </div>
                      </div>
                  </div>
              </div>	
BODY;
		
		return $body;
	}
	
}
?>