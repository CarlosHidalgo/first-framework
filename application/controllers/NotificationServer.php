<?php

namespace Controllers;

/**
 * Procesa las notificaciones del sistema. 
 * @author clopezh
 *
 */
class NotificationServer{
	
	private $auctionController;
	
	public function __construct(){
		
		$this->auctionController = new \Controllers\AuctionController();
		
		$info = "\n PHP Notification Server running....";
		$info .= "\n Server Started : ".date('Y-m-d H:i:s')."\n";
		
		$this->logInfo($info);
	}
	
	public function proccess(){
		
		sleep(5);
		
		$auctions = $this->auctionController->getWithoutStartNotification();
		
		foreach ($auctions as $auction){
			
			$resp = $this->auctionController->sendStartNotification($auction);
				
			if ($resp){
			
			$auction->setRememberStart(true);
			$auction->saveOrUpdate();
			}
			$userKey = '';
			$this->logInfo("\n - Subasta Notificada: ".$auction->getAuctionKey()." provedor: ".$userKey);
			
		}
		
		
	}
	
	/**
	 * Inicia el servidor de notificaciones
	 */
	public function start(){
		while (true){
			$this->proccess();
		}
	}
	
	/**
	 * Realizá un echo
	 * @param unknown $info
	 */
	protected function logInfo($info){
		echo $info;
	}
}
?>
