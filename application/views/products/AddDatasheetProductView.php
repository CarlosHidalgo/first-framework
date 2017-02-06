<?php

namespace Views\Products;

class AddDatasheetProductView extends \App\View{
	
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
	
	public function bodyContent($params = null) {
		
		$action = \App\Route::getForNameRoute(\App\Route::POST, 'products-add-datasheet', array(1));
		$uriCancel = \App\Route::getForNameRoute(\App\Route::GET, 'products');
		
		
		$product = isset($params) && isset($params->product) ? $params->product: new \Models\Product();
		$idProduct = $product->getId();
		
		$body = <<<BODY
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="panel panel-default">
		        	<div class="panel-heading">
						<div class="pull-left">Asignar Ficha tecnica: </div>
						<div class="widget-icons pull-right">
							<a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> 
							<a href="#" class="wclose"><i class="fa fa-times"></i></a>
						</div>  
						<div class="clearfix"></div>
					</div>
					<div class="panel-body">
				 			<form action="{$action}" method="POST" id="searchForm" enctype="multipart/form-data" >
								<label for="imagen">Ficha tecnica</label>
								<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
								<input type="hidden" class="form-control" id="idProduct" name="idProduct" placeholder="IdProduct" value="{$idProduct}">

								<input class="btn btn-default btn-block" type="file" name="fichero_usuario" id="fichero_usuario" required/>
								<br/>
										
								<button class="btn btn-primary btn-block" type="submit" name="upload" > <span class="glyphicon glyphicon-folder-open"></span>&nbsp Subir Ficha técnica</button>
								<a class="btn btn-danger btn-block" type="button" href="{$uriCancel}"> <span class="glyphicon glyphicon-remove-sign"></span>&nbspCancelar</a>	
							</form>
														
					</div>
					<!-- panel body -->
				</div>
			</div>
		</div>
				
BODY;
		return $body;
	}
}
?>