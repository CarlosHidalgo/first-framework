<?php
namespace Views\Auctions;

class ConfirmView extends \App\View{
	
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
		$role = $this->user->isManager();
		$action = \App\Route::getForNameRoute(\App\Route::POST, 'confirm-auction', array($role));
		
		
		
		$body=<<<BODY
			
				<div class="container">
				<div class="row">
					<div class="col-xs-12">
					<center><h2>Confirmar participacion</h2></center>
					</div>
				</div>
				<br> <br />
				    <div class="row">
				        <div class="col-md-4 col-md-offset-4">
				            <form method="POST" action = "{$action}" id="searchForm" enctype="multipart/form-data">
				                <div class="form-group">
		    						<label for="auctionKey">Clave de subasta:</label>
		    						<input type="text" class="form-control" id="auctionKey" name="auctionKey" value="{$params->auctionKey}" readonly>
		  						</div>
				            	<div class="form-group">
		    						<label for="email">Correo electr&oacute;nico:</label>
		    						<input type="email" class="form-control" name="email" id="email">
		    						<label for="token">Clave de confirmacion</label>
		    						<input type="text" class="form-control" name="token" id="token">
		  						</div>
		    					<div class="form-group">			
								<label for="imagen">Ficha tecnica</label>
								<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
								
								<input class="btn btn-default btn-block" type="file" name="fichero_usuario" id="fichero_usuario" required/>
								<br/>
								
								<button class="btn btn-primary btn-block" type="submit" name="upload" > Confirmar </button>
								</div>			
  								
				            </form>
				        </div>
				    </div>
				</div>
		    								
		    								
	
BODY;
		
		return  $body;
	}
}
?>