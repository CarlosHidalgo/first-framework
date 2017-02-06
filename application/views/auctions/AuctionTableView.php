<?php

namespace Views\Auctions;
	 
	class AuctionTableView extends \Views\Core\TableView {
	
	public function bodyContent($params = null){
		$rows = '';
		
			foreach ($params->auctions as $auction){
					
					$uriGoAuction = \App\Route::getForNameRoute(\App\Route::GET, 'go-reverseauction', array($auction->getAuctionKey()));
					$uriInviteProvider = \App\Route::getForNameRoute(\App\Route::GET, 'invite-providers-auction', array($auction->getId() ));
					$urlInfo = \App\Route::getForNameRoute(\App\Route::GET, 'information-auction', array($auction->getAuctionKey()));
					$urlCancel = \App\Route::getForNameRoute(\App\Route::GET, 'cancel-auction', array($auction->getAuctionKey()));
					$urlAnswerQuestion = \App\Route::getForNameRoute(\App\Route::GET, 'answer-question-auction', array($auction->getAuctionKey()));
					$urlHistoryChat = \App\Route::getForNameRoute(\App\Route::GET, 'history-chat', array($auction->getId()));
					$urlHistoryBid = \App\Route::getForNameRoute(\App\Route::GET, 'history-bid', array($auction->getAuctionKey()));
					$rows .=<<<ROWS
					<p>			
						<div class="well well-sm col-md-push-1 col-sm-5 col-md5"  style="margin:-2">
							<div class="col-sm-12 col-md-12">
								<strong class="lead text-center" style= "font-size : 16px;font-weight: bold;"><p>{$auction->getAuctionKey()} - {$auction->getAuctionName()}</p></strong>
							</div> 
							<div class="col-sm-4 col-md-4" >
								<div class="row-fluid inline-block text-center">
									<div class = "col-sm-1 col-md-2 text-center"><label><em><span class="glyphicon glyphicon-time"> {$auction->getStartDate()}</span></em></label></div>
								</div>
							</div>
								<p></p>
							<div class="col-md-4 col-sm-4 text-center">
								<div class = "btn-group-vertical" >
									<div class="btn-group" role="group">
									<button type="button" class="btn btn-warning disabled {$this->condition($auction->getIdStatus() != 2, 'hidden', '')}">No Disponible</button>
									</div><div class="btn-group" role="group">
									<button type="button" class="btn btn-success disabled {$this->condition($auction->getIdStatus() != 1, 'hidden', '')}">Disponible</button>
									</div><div class="btn-group" role="group">
									<button type="button" class="btn btn-danger disabled {$this->condition($auction->getIdStatus() != 3, 'hidden', '')}">Cancelada</button>
									</div><div class="btn-group" role="group">
									<button type="button" class="btn btn-info disabled {$this->condition($auction->getIdStatus() != 4, 'hidden', '')}">Finalizada</button>
									</div><div class="btn-group" role="group">
									<a type="button" role="button" href="{$urlInfo}" class="btn btn-primary"><span class="glyphicon  glyphicon-info-sign"></span> Info</a>
									</div>
								</div>
								
							</div>
							<p></p>	
							<div class="col-sm-4 col-md-4  text-center">
								<div class="btn-group-sm" role="group" >
										<a href="{$uriGoAuction}" type="button" class="btn btn-success {$this->condition($auction->getIdStatus() == '1', '', 'hidden')}"  data-toggle="tooltip" data-placement="top" title="Ver subasta"><span class="glyphicon glyphicon-eye-open"></span> </a>
								
										<a href="{$urlHistoryBid}" class="btn btn-info {$this->condition($auction->getIdStatus() == '4' && (!$this->user->isProvider()), '', 'hidden')}" data-toggle="tooltip" data-placement="top" title="Historial de subasta"><span class="glyphicon glyphicon-usd"></span> </a>
										
										<a href={$urlAnswerQuestion} type="button" class="btn btn-primary " data-toggle="tooltip" data-placement="top" title="Ver preguntas"><span class="glyphicon glyphicon-question-sign" ></span> </a>
									
										<a href="{$uriInviteProvider}" type="button" class="btn btn-info {$this->condition($auction->getIdStatus() == '2' && (!$this->user->isProvider()), '', 'hidden')}"  data-toggle="tooltip" data-placement="top" title="Invitar proveedores"><span class="glyphicon glyphicon-user" ></span> </a>
									
										<a href="{$urlCancel}" type="button"  class="btn btn-danger {$this->condition($auction->getIdStatus() == '2' && (!$this->user->isProvider()), '', 'hidden')}"  data-toggle="tooltip" data-placement="top" title="Cancelar Subasta"><span class="glyphicon glyphicon-remove-sign" ></span> </a>
									
										<a href ="{$urlHistoryChat}" type="button" class="btn btn-info {$this->condition($auction->getIdStatus() == '4' && (!$this->user->isProvider()), '', 'hidden')}"  data-toggle="tooltip" data-placement="top" title="Historial de chat"><span class="glyphicon glyphicon-comment" ></span> </a>
								</div>
							</div>
						</div>
</p>	
					
							
ROWS;
			}
			return $rows;
		}
	
	
}

?>