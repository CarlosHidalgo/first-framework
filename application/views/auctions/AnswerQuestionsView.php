<?php

namespace Views\Auctions;

class AnswerQuestionsView extends \App\View{
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
		
		$uriCancel = \App\Route::getForNameRoute(\App\Route::GET, 'auctions');
		
		$action = \App\Route::getForNameRoute(\App\Route::POST, 'save-answer');
		$auction = new \Models\Auction();
		$auction = $params->auction;	
		
		$parameters = array();
		$parameters['idAuction'] = $auction->getId();
		$questions = \Models\Questions::find($parameters);
		
		$body = <<<BODY
	<div class="panel panel-default">
			<div class="panel-heading text-center">
				<a class="btn btn-danger btn-sm pull-left" type="button" href="{$uriCancel}"> <span class="glyphicon glyphicon-arrow-left"></span>&nbsp;Regresar</a>
				<strong>Preguntas de la subasta Subasta - {$auction->getAuctionKey()} - {$auction->getAuctionName()}</strong>
				<div class="clearfix"></div>
			</div>			
		<div class="row">
				<div class="col-md-6 col-md-offset-3"> 		 			
					<form role="form" id="searchForm" action="{$action}" method="post">
				 	  <div class="form-group">
					
BODY;
							if(!empty($questions)){
						
						
								foreach($questions as $curQuestion){
							
								$body .= <<<BODY
								<label >{$curQuestion->getQuestions()}</label> <textarea class="form-control" rows="1"  id="answer"  name="answer[]" {$this->condition($this->user->isManager(), 'enabled', 'disabled')} required>{$curQuestion->getAnswer()}</textarea>
								<input class="form-control" id="id" name="id[]"  type="hidden" value="{$curQuestion->getId()}" required />
								<input class="form-control" id="idAuction" name="idAuction"  type="hidden" value="{$curQuestion->getIdAuction()}" required /></p>
BODY;
								}
							} else{
									$body .= <<<BODY
									<p>No se han realizado preguntas para la subasta</p>
BODY;
								}	
									$body .= <<<BODY
					  
						</div>		
					  <div class="form-group" {$this->condition($this->user->isManager(), 'enabled', 'hidden')}>
						 <div class="col-lg-offset-3 col-lg-10">						 	
						 	<button type="submit" class="btn btn-info"><span class="glyphicon glyphicon glyphicon-envelope"></span>&nbspGuardar</button>						
						    <a href="$uriCancel" class="btn btn-danger">Cancelar</a>
						    
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
