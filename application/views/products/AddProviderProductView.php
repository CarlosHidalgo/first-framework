<?php
namespace Views\Products;

class AddProviderProductView extends \App\View {

	public function jScript($params = null){
		
		$providers = json_encode(isset($params->providers) ? $params->providers : array()); 
		$searchRoute = isset($params->searchRoute) ? $params->searchRoute: '';
		$product = isset($params->product) ? $params->product: new \Models\Product();
		$idProduct = $product->getId(); 
		
		$js = <<<SCRIPT
		
		jQuery(function($){
			$('body').tooltip({
    			selector: '.glyphicon'
			});
			$.setAjax({});
				
			var providers = {$providers};
					
			var route = '{$searchRoute}'; //enviar desde clase que invoca a este script
			var type = "POST";
			var nameForm = false;
			var data = { idProduct: {$idProduct} }; //enviar desde clase que invoca a este script
			var targetDiv = '#resultTable';
			var contentType = 'application/x-www-form-urlencoded';
		
			$.requestAjax(route, type, data, contentType , targetDiv, nameForm);
					
			$("#select_provider").autocomplete({
				source: providers,
				select: function( event, ui ) {
					
					$("#idProvider").val(ui.item.id);
			
				}
			
			});
		});

SCRIPT;
		return $js;
	}

	public function bodyContent($params = null) {

		$action = \App\Route::getForNameRoute(\App\Route::POST, 'save-provider-products', array(1));
		$uriCancel = \App\Route::getForNameRoute(\App\Route::GET, 'products');
		
		$product = isset($params->product) ? $params->product : new \Models\Product();
		$idProduct = $product->getId();
		$nameProduct = $product->getName();
		
		global $msg_error;

		$body = <<<BODY
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="panel panel-default">
		        	<div class="panel-heading">
						<div class="pull-left">Asignar Proveedores para <b>{$nameProduct}</b>: </div>
						<div class="widget-icons pull-right">
							<a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> 
							<a href="#" class="wclose"><i class="fa fa-times"></i></a>
						</div>  
						<div class="clearfix"></div>
					</div>
					<div class="panel-body">
						<form method="POST" action = "{$action}"class="form" role="form" id="searchForm">
							<div class="row">
								
								<div class="form-group col-xs-9 col-sm-9 col-md-9 col-lg-11">
									<label for="name">Nombre:</label>
									<input type="text" class="form-control" id="select_provider" name="select_provider" placeholder="Buscar proveedores" >
									
									<input type="hidden" class="form-control" id="idProvider" name="idProvider" >
									<input type="hidden" class="form-control" id="idProduct" name="idProduct" hidden value="{$idProduct}">
								</div>
								
								<div class="form-group col-xs-3 col-sm-3 col-md-3 col-lg-1 pull-right">
									<label for="name">&nbsp;</label>
									<button type="submit" class="btn btn-success btn-block"> <span class="glyphicon glyphicon glyphicon-plus" > </span></button>
								</div>
								
								<div class="form-group col-xs-12 col-sm-12 col-md-1 col-lg-1 pull-left">
										
									<a class="btn btn-danger" type="button" href="{$uriCancel}"> <span class="glyphicon glyphicon-arrow-left"></span>&nbspCancelar</a>
								</div>

							</div>
								
						</form>			
					</div>
					<!-- panel body -->
				</div>
			</div>
		</div>
		
		<div class="row" id="resultTable">
		

											
		</div> 
		
		<div id="hideMe">
			<div class="alert alert-danger" role="alert" {$this->condition(isset($msg_error), '', 'hidden')}>
		  		<span class="glyphicon glyphicon-exclamation-sign"></span>
		   		<span class="sr-only">Error: </span>
		   		{$msg_error}
	      	</div>		
      	</div>
BODY;
		return $body;
	}
}
?>