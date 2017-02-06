<?php

namespace Views\Products;

class CreateEditProductView extends \App\View{
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
		
		$action = \App\Route::getForNameRoute(\App\Route::POST, 'products-create-edit');
		$urlCancel = \App\Route::getForNameRoute(\App\Route::GET, 'products');
		
		$product = new \Models\Product();
	
		if (isset($params)){
				
			if (isset($params->product)){
				$product = $params->product;
			}
		}
		
		$body = <<<BODY
		<div class="panel panel-default">
  			<div class="panel-body">
				<div class="container">
					<div class="col-lg-10">
						<fieldset>
			    		<legend>Agregar Productos</legend>
							<form class="form-horizontal" role="form" id="searchForm" action='{$action}' method="post">
							 <div class="form-group">
							    <label for="firstName" class="col-lg-3 control-label">Nombre</label>
							    <div class="col-lg-6">							  	  
							      <input class="form-control" id="id" name="id" type="hidden" value="{$product->getId()}" required />
                                  <input class="form-control" id="name" name="name" type="text" value="{$product->getName()}" required />
							    </div>
							  </div>	
							  <div class="form-group">
							    <label for="lastName" class="col-lg-3 control-label">Clave</label>
							    <div class="col-lg-6">
							      <input class="form-control" id="keyProduct" name="keyProduct" type="text" value="{$product->getkeyproduct()}" required />
							    </div>
							  </div>
							  
							  <div class="form-group">
							    <div class="col-lg-offset-3 col-lg-10">
							      <button type="submit" class="btn btn-success">Guardar</button> <a href="{$urlCancel}" class="btn btn-primary">Cancelar</a>
							    </div>
							  </div>
							</form>
						</fieldset>
			        </div>
				</div>
			</div>
		</div>

BODY;
		return $body;	
	}
} 

?>