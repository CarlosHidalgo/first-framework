<?php

namespace Views\Auctions;

class CancelAuctionView extends \App\View{
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
		$action = \App\Route::getForNameRoute(\App\Route::POST, 'cancel-auction');
		$urlCancel = \App\Route::getForNameRoute(\App\Route::GET, 'auctions');
		
		$auction = new \Models\Auction();
		

		if (isset($params)){
				
			if (isset($params->auction)){
				$auction = $params->auction;
			}
		}
		$body = <<<BODY
		<div class="panel panel-default">
  			<div class="panel-body">
				<div class="container">
					<div class="col-lg-10">
						<fieldset>
			    		<legend>Cancelar Subasta</legend>
							<form class="form-horizontal" role="form"  id="searchForm" action='{$action}' method="post">
							 <div class="form-group">
							    <label for="firstName" class="col-lg-3 control-label">Clave de subasta</label>
							    <div class="col-lg-6">	
							    <input class="form-control" id="idAuction" name="idAuction" type="hidden" value="{$auction->getId()}" required />						  	  
                                <input class="form-control" id="auctionKey" name="auctionKey" type="text" value="{$auction->getAuctionKey()}" disabled="true" required />
							    </div>
							  </div>	
							  <div class="form-group">
							    <label for="lastName" class="col-lg-3 control-label">Clave</label>
							    <div class="col-lg-6">
							     <!--<input class="form-control" id="keyAuction" name="keyAuction" type="text"  required /> -->
							    <textarea class="form-control" id="reasonCancel" name="reasonCancel" rows="3" placeholder="Motivo de la cancelacion"  maxlength="500" required></textarea> 
							    </div>
							  </div>
							  <div class="form-group">
							    <div class="col-lg-offset-3 col-lg-10">
							      <button type="submit" class="btn btn-success">Aceptar</button> <a href="{$urlCancel}" class="btn btn-primary">Cancelar</a>
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