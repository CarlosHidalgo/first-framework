<?php
	namespace Views\Auctions;
	
	class HistoryBidView extends \App\View{
		
		public function bodyContent($params = null){
		$auctionbid = $params->auctionbid;
		$auction = $params->auction;
		
		$downloadHistory = \App\Route::getForNameRoute(\App\Route::GET, 'download-history-bid', array($auction->getId()));
		$urlCancel = \App\Route::getForNameRoute(\App\Route::GET, 'auctions');
		
		$body = <<<BODY
			<div class="panel panel-default">
				<div class="panel-heading text-center"><strong>Histórico de ofertas de la subasta - {$auction->getAuctionKey()} - {$auction->getAuctionName()}</strong>
			</div>
					<div class="panel-body tab-pane active" >
					<ul class="list-group">
BODY;
						foreach($auctionbid as $curBid){
							if($curBid->isBest()){
							$body .= <<<BODY
							<li class="list-group-item list-group-item-success">
BODY;
						}else{
							$body.= <<<BODY
							<li class="list-group-item">
BODY;
						}
						$body.= <<<BODY
						
							 <span class="badge"><small>{$curBid->getBidDate()}</small></span>
							<small> El usuario <b>{$curBid->name}</b> ofertó <b>$ {$curBid->getBid()} {$auction->getCurrency()}</b>para la entidad  <b>{$curBid->keyEntity}</b></small>
						</li>
BODY;
						}
					$body .= <<<BODY
						
					</ul>
					
					</div>
			</div>
BODY;
		$body .= <<<BODY
		<div class = "row">
			<form method="GET" action = "{$urlCancel}" class="form" role="form" id="searchForm">
				<input class="form-control" id="id" name="id" type="hidden" value="{$auction->getId()}" required />
						<div class="row">
							<div class="col-md-3 col-md-offset-5 pull-left">
								<a href={$downloadHistory} type="button" class="btn btn-info pull-left" ><span class="glyphicon glyphicon-download-alt" > Descargar</span></a>
								<button type="submit" class="btn btn-danger pull-right"><span class = "glyphicon glyphicon-remove"> Cancelar</button></span>
								
							</div>
						</div>
			</form>
		</div>	
			
		
BODY;
return $body;	
		}
		
	}


?>