<?php
	namespace Views\Auctions;
	class HistoryChatView extends \App\View{
		public function bodyContent($params = null){
			$chats = $params->chat;
			$auction = $params->auction;
			$urlDownload = \App\Route::getForNameRoute(\App\Route::GET, 'download-history-chat', array($auction->getId()));
			$uriCancel = \App\Route::getForNameRoute(\App\Route::GET, 'auctions');
				
			
			$body=<<<BODY
				<h3>Subasta: {$auction->getAuctionKey()} </h3>
				<ul class="list-group">
BODY;
			
			foreach ($chats as $chat){
				
				
			$body.=<<<BODY
				<li class="list-group-item">
				    <span class="badge"><small>{$chat->getTime()}</small></span>
					<b>{$chat->name}</b>
					<p><small>{$chat->getMessage()}</small></p>
					
				  </li>
			
BODY;
				
			}
			$body.=<<<BODY
				</ul>

			<a class="btn btn-danger" type="button" href="{$uriCancel}"> <span class="glyphicon glyphicon-arrow-left"></span>&nbsp;Regresar</a>
			<a href="{$urlDownload}" class="btn btn-primary" role="button">Descargar</a> 
BODY;
		
			return  $body;
		}
	}
?>