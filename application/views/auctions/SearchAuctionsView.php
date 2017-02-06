<?php

namespace Views\Auctions;

class SearchAuctionsView extends \App\View{
	
	public function ccsFiles(){
	
		$scripts = array('jquery.countdown.css');
		$css = $this->generateUriFileCSS($scripts);
	
		return $css;
	}
	
	public function jScriptFiles(){
		$scripts = array('jquery.countdown.js');
		$jss = $this->generateUriFileJS($scripts);
	
		return $jss;
	}


	public function jScript($params = null){
		
		$providers = json_encode(isset($params->providers) ? $params->providers : array());
		
		$auction = null;
		
		if (isset($params->currentAuction)){
			$auction = $params->currentAuction;
		}else if (isset($params->nextAuction)){
			$auction = $params->nextAuction;
		}else{
			$auction = new \Models\Auction();
		}
		
		$startDate = new \DateTime($auction->getStartDate());
		$millis = $startDate->getTimestamp() * 1000;
		$keyAuction = $auction->getAuctionKey();
		$currentUser = "";
		
		if($currentUser = $this->user->isProvider()){
			$currentUser = $this->user->getId();
		}
		
		$searchRoute = isset($params->searchRoute) ? $params->searchRoute :'';
		$urlSystemDate = isset($params->urlSystemDate) ? $params->urlSystemDate :'';
			
		$js = <<<SCRIPT
		
		var providers = {$providers};
				
		jQuery(function($){			
			$('body').tooltip({
    			selector: '.glyphicon'
			});
			$.setAjax({});
			
			
			$("#userName").autocomplete({
				source: providers,
				select: function( event, ui ) {
					
					$("#idProvider").val(ui.item.id);
			
				}
			
			});
			
			var urlSystemDate = '{$urlSystemDate}';
			
			// ------------------------------------------
			//  NEXT AUCTION 
			// ------------------------------------------
			var route = '{$searchRoute}'; //enviar desde clase que invoca a este script
			var type = "POST";
			var nameForm = false;
			var data = { auctionKey: '{$keyAuction}', idProvider :'{$currentUser}' }; //enviar desde clase que invoca a este script
			var targetDiv = '#resultTable';
			var contentType = 'application/x-www-form-urlencoded';
			var update = false;
		
			$.requestAjax(route, type, data, contentType , targetDiv, nameForm);
					
			//-------------------------------------------
			// COUNTER
			// -------------------------------------------
			$('#countdown').countdown({
				timestamp	: {$millis},
				urlSystemDate : urlSystemDate,
				callback	: function(endTime, days, hours, minutes, seconds){
						
						
					$.ajax({
				        url: urlSystemDate,
				        cache: false
				    }).then(function(myTime) {
						var now =  new Date(myTime);
						
						if (endTime < now.getTime() && !update){
							
							update = true;
							
							setTimeout(function(){
								
						    	$.requestAjax(route, type, data, contentType , targetDiv, nameForm);
							
							}, 1000);				
						
						}
						
					});
						
	
				}
			});
			
		});
		
		function cleart(idToClean){
		 if(idToClean == "userName"){
		 	$("#idProvider").val('');
		 }
			document.getElementById(idToClean).value="";
		};
		
SCRIPT;
		return $js;
	}
	
	public function bodyContent($params = null) {
		
		$action = \App\Route::getForNameRoute(\App\Route::POST, 'auctions-search', array(1));
		$urlCreateAuction = \App\Route::getForNameRoute(\App\Route::GET, 'auctions-create-edit', array(0));
		$urlAnalyze = \App\Route::getForNameRoute(\App\Route::GET, 'auctions-analyze');
		
		if (isset($params->currentAuction)){
			
			$auction = $params->currentAuction;
			
		}else if ( isset($params->nextAuction) ){
			
			$auction = $params->nextAuction;
		}else{
			
			$auction = new \Models\Auction();
		}
	
		$keyAuction = $auction->getAuctionKey();
		$currentUser = "";
		if($currentUser = $this->user->isProvider()){
			$currentUser = $this->user->getId();
		}
		
		
		
		$body = <<<BODY
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="accordion">
				<div class="panel panel-default">
		        	<div class="panel-heading text-center" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
						<div><Strong>  <label ">Buscar subastas </label></Strong> </div>
						<div class="clearfix"></div>
					</div>
					<div id="collapseOne" class="panel-collapse collapse in">
					<div class="panel-body">
						<form method="POST" action = "{$action}"class="form" role="form" id="searchForm">
							<div class="row">
								<div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
									<label for="auctionKey">Clave subasta:</label>
									<div class="input-group">
									 	<input type="text" 
										class="form-control" id="auctionKey" 
										name="auctionKey" 
										placeholder="Clave subasta" 
										{$this->condition($this->user->isProvider(), 'readonly' ,'')}
										value='{$keyAuction}'>
								      <span class="input-group-btn">
								        <button class="btn btn-default" type="button"  onclick="cleart('auctionKey')" {$this->condition($this->user->isProvider(), 'disabled' ,'')}>
								        <span class = "glyphicon glyphicon-remove"></span></button>
								      </span>
									</div>
								</div>
								
								<div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
									<label for="productKey">Nombre de usuario:</label>
									<div class="input-group">
										<input type="text" 
											class="form-control" id="userName" 
											name="productKey" 
											placeholder="Nombre de usuario"
											{$this->condition($this->user->isProvider(), 'disabled' ,'')}>
										<span class="input-group-btn">
											<input type="hidden" class="form-control" id="idProvider" name="idProvider" value = '{$currentUser}' >
									        <button class="btn btn-default" type="button"  onclick="cleart('userName')" {$this->condition($this->user->isProvider(), 'disabled' ,'')}>
									        <span class = "glyphicon glyphicon-remove"></span></button>
									     </span>
								     </div>
								</div>	
						
								<div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3">
									<label for="productKey">Clave producto:</label>
									<div class="input-group">
										<input type="text" 
											class="form-control" id="productKey" 
											name="productKey" 
											placeholder="Clave producto"
											{$this->condition($this->user->isProvider(), 'disabled' ,'')}>
										<span class="input-group-btn">
									        <button class="btn btn-default" type="button"  onclick="cleart('productKey')" {$this->condition($this->user->isProvider(), 'disabled' ,'')}>
									        <span class = "glyphicon glyphicon-remove"></span></button>
									     </span>
								     </div>
								</div>		
								
								<div class="form-group col-xs-12 col-sm-12 col-md-3 col-lg-3" text-left">
									<label for="Status"><small>Estado</small></label>
									<select class = "form-control" name="status" 
										{$this->condition($this->user->isProvider(), 'disabled' ,'')}
										id="status">
									<option value="0">Todos</option>
BODY;
									foreach ($params->status as $curStatus){
										$body .=<<<BODY
										<option value="{$curStatus->getId()}">{$curStatus->getStatusName()}</option>
BODY;
									}	
									

									$body .=<<<BODY
										</select>
								</div>
								
							</div>
								
							<div class="row">
								<div class="form-group col-xs-12 col-sm-12 col-md-1 col-lg-1 pull-left">
									<a class="btn btn-success dropdown-toggle btn-block" 
										href="{$this->condition($this->user->isProvider(), "#" ,$urlCreateAuction)}"
										{$this->condition($this->user->isProvider(), "disabled='disabled'" ,'')}> <span class="glyphicon glyphicon-plus"> </span> Nuevo </a>
								</div>
								<div class="form-group col-xs-12 col-sm-12 col-md-1 col-lg-1 pull-left">
									<a class="btn btn-info dropdown-toggle btn-block" 
										href="{$this->condition($this->user->isProvider(), "#" ,$urlAnalyze)}"
										{$this->condition($this->user->isProvider(), "disabled='disabled'" ,'')}> <span class="glyphicon glyphicon-signal"> </span> Analizar </a>
								</div>
								<div class="form-group col-xs-12 col-sm-12 col-md-9 col-lg-9 text-center">
									<span class="glyphicon glyphicon-time"></span> <b>Próxima {$keyAuction} en :</b> <span id="countdown"></span>
								</div>
											
								<div class="form-group col-xs-12 col-sm-12 col-md-1 col-lg-1 pull-right">
									<button  class="btn btn-sm btn-primary btn-block"> <span class="glyphicon glyphicon-search" > </span> Buscar</button>
								</div>
							</div>
					
						</form>
					
					</div>
					<!-- panel body -->
					</div>
				</div>
			</div>
		</div>
		<div class="row" id="resultTable"></row>
				
BODY;
		return $body;
	} 
	}
?>
