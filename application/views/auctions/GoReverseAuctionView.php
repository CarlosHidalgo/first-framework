<?php

namespace Views\Auctions;

class GoReverseAuctionView extends \App\View{
	
	public function ccsFiles(){
	
		$scripts = array('auctionClient.css', 'jquery.countdown.css');
		$css = $this->generateUriFileCSS($scripts);
	
		return $css;
	}
	
	public function jScriptFiles(){
		$scripts = array('AuctionClient.js', 'jquery.countdown.js');
		$jss = $this->generateUriFileJS($scripts);
		
		return $jss;
	}
	
	public function jScript($params = null){
		$script = '';
		$user = $this->user;
		$myKeyRole = $this->user->getRole()->getKeyRole();
		
		if (isset($params)){
			
			$auction = isset($params->auction) ? $params->auction : new \Models\Auction();
			$myIdUser =  !$user->isManager() ? $user->getId(): 'null';  
			
			$myIdAuction = $auction->getId();
			$endDate = new \DateTime($auction->getEndDate());
			$millis = $endDate->getTimestamp() * 1000;
			$uriChats = \App\Route::getForNameRoute('POST', 'auction-chat');
			$uriBids = \App\Route::getForNameRoute('POST', 'auction-bids');
			$uriFinishedAuction = \App\Route::getForNameRoute('GET', 'auction-finished', array($auction->getAuctionKey()));
			$uriWebSocket = \App\Router::getDomain().":".\App\Configuration::getGeneralConfigs()['webSocketPort'];
			
			$urlSystemDate = isset($params->urlSystemDate) ? $params->urlSystemDate :'';
			$script = "\n\t\t\t var myKeyRole = '$myKeyRole'; \n\t\t\t var myIdAuction = $myIdAuction;\n\t\t\t var uriChats = '$uriChats'; \n\t\t\t var endDateAuction = $millis;";
			$script.= "\n\t\t\t var uriFinishedAuction = '$uriFinishedAuction';\n\t\t\t var uriBids = '$uriBids';\n\t\t\t var myIdUser = $myIdUser;";
			$script.= "\n\t\t\t var uriWebSocket = '$uriWebSocket'; var urlSystemDate = '$urlSystemDate'";
			

		}
		
		return $script;
	}
	
	public function bodyContent($params = null){

		$auction = isset($params->auction) ? $params->auction: new \Models\Auction();
		$entities = isset($params->entities) ? $params->entities: array();
		$usersBids = isset($params->usersBids) ? $params->usersBids: array();
		$userJSON = json_encode($this->user);
		
		$selectEntities = "";
	
		// ------------------------------------------------------------------------------
		// Construct SELECT´s
		// ------------------------------------------------------------------------------
		foreach ($entities as $entity){
			$value = json_encode($entity);
			$name = $entity->getName();
			$selectEntities.= "<option value='$value'> $name </option>";
		}
		
		$requestQuantityEntities = '';
		$bidsEntities = '';
		// ------------------------------------------------------------------------------
		// Construct quantities
		// ------------------------------------------------------------------------------
		foreach($auction->getRequestQuantities() as $rq){
			
			$entityName = $entities[$rq->getIdEntity()]->getName();
			$idEntity = $rq->getIdEntity();
			$quantity = $rq->getQuantity();
			$unitM = $auction->getUnitMeasure()->getKeyUnitMeasure();
			
			$requestQuantityEntities.= "<div class='row'><div class='col-xs-12 col-sm-3 col-md-3 col-xl-3'><span class=\"glyphicon glyphicon-tint\"></span> <b>Cantidad planta $entityName:</b> $quantity  $unitM </div>";
			$exist = false;
			
			foreach ($auction->getBestBids() as $bid){
				
				if ($bid->getIdEntity() == $rq->getIdEntity() ){
					
					$qbid = $bid->getBid();
					
					$buttonDelete = '';
					
					if ($this->user->isManager()){
						$jsonBid = json_encode($bid);
						$buttonDelete.= "<button id='entityDeleteBid-{$idEntity}' class='deleteBid' value='$jsonBid'><span class='glyphicon glyphicon-remove'></span></button>";
					}
					
					if ($this->user->isManager() || $this->user->isAuditor()){
						$userName = $bid->name;
					}else{
						$userName = '';
					}
					
					$requestQuantityEntities .= 
						"<div class='col-xs-12 col-sm-2 col-md-3 col-xl-2'><i><b>Mejor oferta para planta $entityName:</b></i></div> 
						 
						 <div class='col-xs-12 col-sm-2 col-md-3 col-xl-2'>
							<span class='glyphicon glyphicon-usd' ></span><input id='entityBid-{$idEntity}' disabled value='{$qbid}' /> {$buttonDelete}
						 </div>
						 <div class='col-xs-12 col-sm-5 col-md-3 col-xl-8'>
						 	<span id='nameUserBestBid-$idEntity' class='label label-success'>$userName</span>
						 </div>";
					$exist = true;
					
					break;
				}
				
			}
			
			if (!$exist){
				
				$buttonDelete = '';
					
				if ($this->user->isManager()){
					
					$buttonDelete.= "<button id='entityDeleteBid-{$idEntity}' class='deleteBid' ><span class='glyphicon glyphicon-remove'></span></button>";
				}
				
				$requestQuantityEntities .= 
				"<div class='col-xs-12 col-sm-2 col-md-3 col-xl-2'><i><b>Mejor oferta para planta $entityName:</b></i></div> 
						 
				 <div class='col-xs-12 col-sm-2 col-md-3 col-xl-2'>
					<span class='glyphicon glyphicon-usd' ></span><input id='entityBid-{$idEntity}' value='No existe oferta' disabled /> {$buttonDelete}
				 </div>";
			}
			
			$requestQuantityEntities.='</div>';
			
		}

		$body =<<<BODY
		<div class="row">
			
			<div class="panel panel-info">
				
				<div class="panel-heading clearfix">
				
					<div class="col-xs-12 col-sm-6 col-md-3 col-xl-3">
						<h5><span class="glyphicon glyphicon-lock"></span> Subasta [{$auction->getProduct()->getKeyProduct()}] - <b>{$auction->getProduct()->getName()} </b></h5>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-3 col-xl-3">
						<h5><span class="glyphicon glyphicon-tint"></span> <b>Cantidad:</b> {$auction->getQuantity()} {$auction->getUnitMeasure()->getNameUnitMeasure() }(s)</h5>
					</div>
					
					<div class="col-xs-12 col-sm-6 col-md-3 col-xl-3" >
						<h5><b>Precio de Apertura:</b> <span class="glyphicon glyphicon-usd"></span> {$auction->getOpenPrice()} {$auction->getCurrency()->getType()}</h5>
					</div>
					
					<div class="col-xs-12 col-sm-6 col-md-3 col-xl-3">	
						<h5><span class="glyphicon glyphicon-time"></span><b>Tiempo:</b> <span id="countdown"></span></h5>
					</div>
					
					<span ></span>
				</div>
				<div class="panel-body">
					
					{$requestQuantityEntities}
					
		  		</div>
				
			</div>
			
		</div>
		
		<!-- ------------ -->
		<!--     BID      -->
		<!-- ------------ -->
		<div class="row" >
			<div class="col-xs-12 col-sm-6 col-md-6 col-xl-6 chat-window">
				<div class="panel panel-default" >
	                <div class="panel-bid-heading panel-heading top-bar">
	                    <div class="col-md-8 col-xs-8">
	                        <h3 class="panel-title"><span class="glyphicon glyphicon-usd"></span> <b><i> Ofertar </i></b></h3>
	                    </div>
	                    <div class="col-md-4 col-xs-4" style="text-align: right;">
	                        <a href="#"><span id="minim_bid_window" class="glyphicon glyphicon-minus icon_minim"></span></a>
	                    </div>
	                </div>
	                <div id="boxBid" class="panel-body msg_container_base">
	                    <!-- MESSAGES -->
	              
		    			
						
	                </div>
	                <div class="panel-bid-footer panel-footer" {$this->condition($this->user->isManager(), 'hidden', '')}>
	                    <form method="POST" role="form" id="send-btn-bid">
		                    <div class="input-group">
		                    
		                    	<input type="hidden" name="user" id="user"   value='{$userJSON}' />
		                    	<input type="hidden" name="idAuction" id="idAuction" placeholder="Identificador de subasta" value="{$auction->getId()}" />
		                        
								<div class="col-xs-12 col-sm-12 col-md-3 col-xl-3">
								  
								  <select class="form-control" id="entity" name="entity">
								    {$selectEntities}
								  </select>
								</div>
								
								<div class="col-xs-12 col-sm-12 col-md-3 col-xl-3">
									
		                        	<input id="bid" name="bid" type="number" step="0.10" min="0.1" class="form-control bid_input" placeholder="Oferta" required/>
		                        </div>
		                        
		                        <div class="col-xs-12 col-sm-12 col-md-3 col-xl-3">
									
		                        	<input id="currency" name="currency" type="text" class="form-control" required value="{$auction->getCurrency()->getType()}" readonly/>
		                        </div>
		                        
		                        <div class="col-xs-12 col-sm-12 col-md-3 col-xl-3">
			                        <span class="input-group-btn btn-block">
			                        	<button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-gavel"></i></button>
			                        </span>
		                        </div>
		                    </div>
	                    </form>
					</div>
				</div>
			</div>
				
	    </div>
		
	    <!-- -------- -->
	    <!-- CHAT     -->
	    <!-- -------- -->
	    <div class="row">
			<div class="col-xs-12 col-sm-6 col-md-6 col-xl-6 chat-window right" >
				<div class="panel-chat-heading panel panel-default" >
					<form method="POST" role="form" id="send-btn-chat">
		                <div class="panel-heading top-bar">
		                    <div class="col-md-8 col-xs-8">
		                        <h3 class="panel-title"><span class="glyphicon glyphicon-comment"></span> Chat <b><i>{$this->user->getName()}</i></b></h3>
		                    </div>
		                    <div class="col-md-4 col-xs-4" style="text-align: right;">
		                        <a href="#"><span id="minim_chat_window" class="glyphicon glyphicon-minus icon_minim"></span></a>
		                    </div>
		                </div>
		                <div id="boxContainerMsg" class="panel-body msg_container_base">
		                    <!-- MESSAGES -->
						
		                </div>
		                <div class="panel-chat-footer panel-footer" >
		                    <div class="input-group">
		                    	
		                    	<input type="hidden" name="idAuction" id="idAuction" placeholder="Identificador de subasta" value="{$auction->getId()}" />
								<input type="hidden" name="user" id="user"   value='{$userJSON}' />
								
		                        <input id="message"  type="text" class="form-control input-sm chat_input" placeholder="Escriba su mensaje..." required/>
		                        <span class="input-group-btn">
		                        	<button type="submit" class="btn btn-primary btn-sm" ><i class="fa fa-envelope-o"></i></button>
		                        </span>
		                    </div>
						</div>
					</form>
				</div>
			</div>
				
	    </div>

BODY;
		
	return $body;
		
	}
}

?>
