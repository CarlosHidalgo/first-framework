<?php

namespace Views\Auctions;

class InformationAuctionView extends \App\View{
	
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
	
	protected function condition($condition, $true, $false){
		return $condition ? $true : $false;
	}
	
	public function bodyContent($params = null){
		$auction = $params->auction;
		$product = $params->product;
		$auctionType = $params->auctionType;
		$unitMeasure = $params->unitMeasure;
		$urldownload = \App\Route::getForNameRoute(\App\Route::GET, 'download-datasheet', array($product->getIdDatasheet()));
		$quantitys = $params->quantity;
		$urlInfo = \App\Route::getForNameRoute(\App\Route::GET, 'information-auction', array($auction->getAuctionKey()));
		$action = \App\Route::getForNameRoute(\App\Route::POST, 'search-providers-users', array(1));
		$uriCancel = \App\Route::getForNameRoute(\App\Route::GET, 'auctions');
		
		$body =<<<BODY
		
			<div class="panel panel-default">
				<div class="panel-body">
					<form method="POST" action = "{$action}" class="form" role="form" id="searchForm">
					<a class="btn btn-danger btn-sm" type="button" href="{$uriCancel}"> <span class="glyphicon glyphicon-arrow-left"></span>&nbsp;Regresar</a>
					<h1> <small>Informacion de subasta</small></h1>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="nombre">Nombre:</label>
							    {$auction->getAuctionName()}
							</div>
						 </div>
						 <div class="col-md-4">
						 	<div class="form-group">
						 		<label for="nombre">Clave:</label>
						 		    {$auction->getAuctionKey()}
						 	</div>
						 </div>
						 <div class="col-md-4">
						 	<div class="form-group">
								<label for="nombre">Tipo:</label>
								    {$auctionType->getType()}
							</div>
						 </div>
					</div>			  
					<div class="row">
						<div class="col-md-4">
		  					<div class="form-group">
								<label for="nombre">Fecha de inicio:</label>
			    				{$auction->getStartDate()}
		 					</div>
		  				</div>					    		
						<div class="col-md-4">
						  	<div class="form-group">
								<label for="nombre">Fecha de finalizacion:</label>
							    {$auction->getEndDate()}
						 	</div>
		  				</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="nombre">inicio del plazo de entrega:</label>
								{$auction->getProductStartDeliveryDate()}
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="nombre">Fin del plazo de entrega:</label>
								{$auction->getProductEndDeliveryDate()}
							</div>
						</div>
						<div class="col-md-4">
						  	<div class="form-group">
								<label for="nombre">Fecha maxima de confirmacion:</label>
							    {$auction->getConfirmExpirationDate()}
						 	</div>
		  				</div>
					</div>

					<h1> <small>Producto</small></h1>
					
					 <div class="row" >
					  <div class="col-md-6">
					  	<div class="form-group">
							<label for="nombre">Clave:</label>
							{$product->getKeyProduct()}
					 	</div>
					  </div>
					  <div class="col-md-6">
						  	<div class="form-group">
								<label for="nombre">Cantidad Total:</label>
								{$auction->getQuantity()} {$unitMeasure->getKeyUnitMeasure()}
						 	</div>
					  </div>
					  <div class="col-md-12">
						  	<div class="form-group">
								<label for="nombre">Descripcion:</label>
								{$product->getName()}
						 	</div>
					  </div>
					</div>					

					<div class="row">
						<div class="col-md-12">
							<table class="table">
							    <thead>
							      <tr>
							        <th>Entidad</th>
							        <th>Cantidad</th>
							      </tr>
							    </thead>
							    <tbody>
							     
BODY;
								foreach ($quantitys as $quantity){      
									
									$body .= <<<BODY
									<tr >
										<td>{$quantity->nameEntity}</td>
							        	<td>{$quantity->getQuantity()}</td>
							        </tr>		
BODY;
																						
								}

								$body .= <<<BODY

							     
							    </tbody>
						    </table>
					    </div>
					</div>			
							
				<h1> <small>Ficha Tecnica</small></h1>
					<div class="row">
						<div class="col-md-4">
		  					<div class="form-group">
								<a type="button"   class="btn btn-success" href="{$urldownload}"> <span class="glyphicon glyphicon-download-alt"> </span> Ficha tecnica</a>
		 					</div>
		  				</div>					    		
					</div>
										
				<div>
					<span {$this->condition($this->user->isProvider(), 'hidden', '')}>
						<h1> <small>Proveedores</small></h1>
						<input type="hidden" name="idAuction" value="{$auction->getId()}" />
						<input type="submit" class="btn btn-success" {$this->condition($this->user->isProvider(), 'hidden', '')} value="ver proveedor" />	
					</span>		
				</div>	
				
			<div class="row" id="resultTable">
							
			</row>							
				
			
BODY;
									
									
								
		return  $body;
	}	
}

?>
