<?php 

/**
 * Vista para una busbasta finalizada
 * @author clopezh
 *
 */

namespace Views\Auctions;

class AuctionFinishedView extends \App\View{
	
	public function bodyContent($params = null){
		
		$auction = isset($params->auction) ? $params->auction : new \Models\Auction();
		$keyAuction = $auction->getAuctionKey();
		$UrlTerminos = \App\Route::getForNameRoute(\App\Route::GET, 'terminos');
		
		$bestBids = '';
		$isWinner = false;
		
		$typeCurrency = $auction->getCurrency()->getType();
		
		// BEST BIDS
		foreach ($auction->getBestBids() as $bb){
			
			$u = "<span class='badge'><small>".$bb->getBidDate()."</small></span>";
			
			$u .= '$'.$bb->getBid(). ' '.$typeCurrency.' para planta '.$bb->nameEntity;
			
			if ($this->user->isManager() || $this->user->isAuditor()){
				$u .= " por el usuario <b>".$bb->name.'</b>';	
			}
			
			$bestBids.= "<li class='list-group-item list-group-item-success'> $u  </li>";
			
			if ( $bb->getIdUser() == $this->user->getId()){
				$isWinner = true;
			}
		}
		
		// BIDS
		if ($this->user->isProvider() ){
			
			$bids = '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<p><i>Las ofertas realizadas por usted se describen a continuación:</i></p></div>
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><ul class="list-group">';
			
			foreach ($auction->getBids() as $bb){
				$u = "<span class='badge'><small>".$bb->getBidDate()."</small></span>";
				
				$u .= '$'.$bb->getBid(). ' '.$typeCurrency.' para planta '.$bb->nameEntity;
				
				if ($bb->isBest()){
					
					$bids.= "<li class='list-group-item list-group-item-success' > $u  </li>";
					
				}else{
	
					$bids.= "<li class='list-group-item '> $u  </li>";
				}
			}
			$bids.= '</ul></div>';
		}
				
		// USER
		$nameUser = $this->user->getName();
		
		if ($isWinner){
			$congratulations = "<h4><b>¡FELICIDADES! '$nameUser' su oferta resulto ser la mejor para la subasta $keyAuction.</b></h4>";
		}else{
			$congratulations = "<h4><b>Gracias '$nameUser' por participar en la subasta $keyAuction.</b></h4>";
		}
		
		$body = <<<BODY
<div class="container" >
	 
      <div class="row">
      	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      		{$congratulations}
      	</div>
      </div>
	  <br/>
	  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	  	<p><i>Las mejores ofertas de la subasta fueron las siguientes:</i></p>
	  </div>
      
	  <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	  	<ul class="list-group">
		{$bestBids}
		</ul>
	  </div>
	  
      
      <a href="{$UrlTerminos}"> Terminos y condiciones </a></input>

				
</div> <!-- /container -->	
BODY;
		
		return $body;
	}
}

?>