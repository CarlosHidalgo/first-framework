<?php
namespace Views\Auctions;

class QuestionsAuctionView extends \App\View{
	
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
		$action = \App\Route::getForNameRoute(\App\Route::POST, 'questions', array(0));
		$body =<<<BODY
		
			<div class="container">
				<div class="row">
					<div class="col-xs-12">
						<center><h2>Preguntas </h2></center>
					</div>
					<div class="form-group"  ALIGN=center>
		    			<label for="auctionKey" >
		    				<h4 >Clave de la subasta {$params->auction}</h4>
		    			</label>				
		  			</div>
				</div>
				<br> <br />
				<div class="row">
					<div class="col-md-6 col-md-offset-3"> 
				    	<form method="POST" action = "{$action}" id="searchForm">
		    				<div class="form-group"  ALIGN=center>
		    					<label for="auctionKey" >
		    						<input type="hidden" class="form-control" value = "{$params->auction}" name="auctionKey" id="auctionKey">
		    					</label>				
		  					</div>	
				            <div class="form-group">
		    					<label for="email">Correo electr&oacute;nico:</label>
		    						<input type="email" class="form-control" name="email" id="email">
		  					</div>
		    				<div class="form-group">
		    					<label for="email">Pregunta:</label>
		    						<textarea class="form-control" rows="4" maxlength="300" id="message"  name="message"></textarea>
		  					</div>				
  							<button type="submit" class="btn btn-default">Enviar</button>
				        </form>
				    </div>
				</div>
			</div>
		    				
BODY;
		
		return $body;
	}
	
}

?>