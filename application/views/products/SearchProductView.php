<?php

namespace Views\Products;

class SearchProductView extends \App\View {
	public function jScript($params = null) {
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
		$action = \App\Route::getForNameRoute ( \App\Route::POST, 'products-search', array (
				1 
		) );
		$urlAddProduct = \App\Route::getForNameRoute ( \App\Route::GET, 'products-create-edit', array (
				0 
		) );
		
		$body = <<<BODY
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="panel panel-default">
		        	<div class="panel-heading">
						<div class="pull-left">Buscar productos: </div>
						<div class="widget-icons pull-right">
							<a href="#" class="wminimize"><i class="fa fa-chevron-up"></i></a> 
							<a href="#" class="wclose"><i class="fa fa-times"></i></a>
						</div>  
						<div class="clearfix"></div>
					</div>
					<div class="panel-body">
						<form method="POST" action = "{$action}"class="form" role="form" id="searchForm">
							<div class="row">
								<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
									<label for="name">Nombre:</label>
									<input type="text" class="form-control" id="name" name="name" placeholder="nombre">
								</div>
						
								<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-6">
									<label for="key">Clave:</label>
									<input type="text" class="form-control" id="key" name="key" placeholder="clave">
								</div>		
						
								
							</div>
								
							<div class="row">
								<div class="form-group col-xs-12 col-sm-12 col-md-1 col-lg-1 pull-left">
									<a class="btn btn-success btn-block" href="{$urlAddProduct}" > <span class="glyphicon glyphicon-plus" > </span> Nuevo </a>
								</div>
								
								<div class="form-group col-xs-12 col-sm-12 col-md-1 col-lg-1 pull-right">
									<button type="submit" class="btn btn-sm btn-primary btn-block"> <span class="glyphicon glyphicon-search" > </span> Buscar</button>
								</div>
							</div>
					
						</form>
					
					</div>
					<!-- panel body -->
				</div>
			</div>
		</div>
		
		<div class="row" id="resultTable"></row>
				
BODY;
		return $body;
	}
}
?>