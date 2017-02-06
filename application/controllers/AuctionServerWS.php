<?php

namespace Controllers;

use Socket\WebSocketServer;
use Security\Auth;
use App\Configuration;

/**
 * Server para el control de subastas
 * @author clopezh
 *
 */
class AuctionServerWS extends WebSocketServer {
	
	const SYSTEM_TYPE = 'system';
	const USER_TYPE = 'user';
	const AUTH_TYPE = 'auth';
	const CHAT_KIND = 'chat';
	const DISCONNECTION_KIND = 'disconnection';
	const BID_KIND = 'bid';
	const HIDDEN_NAME = 'Proveedor';
	const ADMIN_NAME = 'Administrador';
	const ACTION_ADD = 'add';
	const ACTION_DELETE = 'delete';
	
	private $chatController;
	private $auctionController;
	private $jsonMapper;
	private $auction;
	
	private $clientMap = array(); //User and socket
	
	/**
	 * (non-PHPdoc)
	 * @see \Socket\WebSocketServer::proccess()
	 */
	public function proccess(){
	
		sleep(1);
		$currentAuction = $this->getAuctionController()->getCurrentAuction();
		if ( isset($currentAuction) ){
				
			$this->auction = $currentAuction;
				
			$status = \Models\AuctionStatus::findOne(array('keyStatus' => \Models\AuctionStatus::AVAILABLE));
			
			$this->auction->setIdStatus($status->getId());
			$response = $this->auction->saveOrUpdate();

			if ($response === true || $response === 1){
				
				$this->logInfo("\n\n- Procesando SUBASTA :  ".$currentAuction->getAuctionKey());
					
				while(true){
		
					$sockets = $this->clients;
						
					//returns the socket resources in $sockets array
					if (socket_select($sockets, $this->null, $this->null, 0, 10) ){
		
						//check for new socket
						if (in_array($this->socket, $sockets)) {
								
							$newSocket = socket_accept($this->socket);
		
							$recevedHeader = socket_read($newSocket, 2048);
								
							$headers = $this->loadHeadersToArray($recevedHeader);
								
							$this->incomingConnection($newSocket, $headers);
		
							$id = intval($this->socket);
								
							unset($sockets[$id]);
								
						}// new Socket
		
						// ----------------------
						//  MESSAGES OF CLIENTS
						// ----------------------
						foreach ($sockets as $socketClient) {
		
							while(@socket_recv($socketClient, $buf, 2048, 0) >= 1){
		
								$receivenMsg = $this->unmask($buf);
		
								$this->incomingMessages($receivenMsg, $socketClient);
		
								break 2;
							}
								
							$isValid = @socket_connect($socketClient, $this->host, $this->port);
		
							if ($isValid){
		
								$buf = @socket_read($socketClient, 2048, PHP_NORMAL_READ);
		
							}
								
							// [[ Check disconected ]]
							if (!$isValid || $buf === false) {
		
								$this->disconnectedClient($socketClient);
							}
		
						}//Fin for-each
					}//FIN SELECT
		
					if ($this->auction->getEndDate() < date('Y-m-d H:i:s')){
							
						$this->finishAuction();
							
						break;
					}
		
				}
			}else{
				$this->logInfo("\n\n- No se puede procesar SUBASTA :  ".$currentAuction->getAuctionKey(). "  $response ");
			}
				
		}//Fin búsqueda de subasta actual
	}
	
	/**
	 * Retorna el controlador del chat - singleton
	 * @return \Controllers\ChatController
	 */
	private function getChatController(){
		
		$this->chatController = new  \Controllers\ChatController();
		
		return $this->chatController;
		
	}
	
	/**
	 * Retorna el objeto JsonMapper del server - singleton
	 * @return \JsonMapper\JsonMapper
	 */
	public function getJsonMapper(){
		if ($this->jsonMapper == null){
			$this->jsonMapper = new \JsonMapper\JsonMapper();
		}
		
		return $this->jsonMapper;
	}
	
	/**
	 * Retorna el controlador de subastas - singleton
	 * @return \Controllers\AuctionController
	 */
	private function getAuctionController(){
		
		$this->auctionController = new  \Controllers\AuctionController();
		
		return $this->auctionController;
		
	}
	
	/**
	 * Conexiones entrantes
	 * @param unknown $newSocket cliente que solicitó conexión con el websocket
	 * @param unknown $headers cabecerá enviada por el cliente
	 */
	public function incomingConnection($newSocket, $headers){
		// [[ Load cookies for TOKEN]]
		$token = '';
		if (isset($headers['Cookie'])){
				
			$genConfigs = \App\Configuration::getGeneralConfigs();
			$cookies = explode(';', $headers['Cookie']);
				
			foreach ($cookies as $index => $value){
				$cookie = explode("=", $value);
		
				if (strcmp(trim($cookie[0]), $genConfigs['cookieName'])  == 0){
					$token = trim($cookie[1]);
					break;
				}
			}
		}
		
		socket_getpeername($newSocket, $ip);
		$id = intval($newSocket);
		
		//[[ HANDSHAKE ]]
		if (Auth::check($token)){
				
			$this->performHandshaking($headers, $newSocket, $this->host, $this->port);
				
			$this->clients[$id] = $newSocket;	
		
			$this->logInfo("\n\n [+]Connection:  $newSocket  of $ip  id: $id");
		}else{
				
			$this->logInfo("\n- [-]No Connection:  $newSocket  of $ip  id: $id");
		}
	}
	
	/**
	 * Procesar mensajes de los clientes conectados
	 * @param unknown $receivenMsg json mensaje
	 * @param unknown $socketClient
	 */
	public function incomingMessages($receivenMsg, $socketClient){
		//Hasta aqui
		$msg = json_decode($receivenMsg);
		$id = intval($socketClient);
			
		if (is_object($msg)){
			
			try{
				
				$msg->data->time = date('Y-m-d H:i:s');
				
				switch ($msg->type){
				
					case self::USER_TYPE:
				
						if ( strcmp($msg->kind, self::CHAT_KIND) == 0 ){
							
							$user = $msg->data->user;
							$msg->data->idUser = $user->idUser;
							
							$response = 
								$this->getChatController()->saveChat($msg->data->idAuction, $user->idUser, $msg->data->message, $msg->data->time);
				
							if ($response){
									
								$this->sendMessageForRole($socketClient, $msg,self::CHAT_KIND, $this->clientMap);
							}
						}else if ( strcmp($msg->kind, self::BID_KIND) == 0 ){
							
							if ( strcmp($msg->action, self::ACTION_ADD) == 0){
								
								$user = $msg->data->user;
								$msg->data->idUser = $user->idUser;
								
								$auctionBid = new \Models\AuctionBid();
								$entity = $msg->data->entity;
									
								$auctionBid->setBest(false);
								$auctionBid->setIdAuction($msg->data->idAuction);
								$auctionBid->setIdUser($user->idUser);
								$auctionBid->setBid($msg->data->bid);
								$auctionBid->setBidDate($msg->data->time);
								$auctionBid->setIdEntity($entity->idEntity);
									
								$response =
								
									$this->getAuctionController()->makeBid($this->auction, $auctionBid);
									
								if ($response){
										
									$msg->data->best = $response->isBest();
									$msg->data->nameEntity = $entity->nameEntity;
									$msg->data->bidDate = $msg->data->time;
									$msg->data->idEntity = $entity->idEntity;
										
									$this->sendMessageForRole($socketClient, $msg, self::BID_KIND,$this->clientMap);
								}
							}else if (strcmp($msg->action, self::ACTION_DELETE) == 0){
								
								$auctionBid = $this->getJsonMapper()->map($msg->data->auctionBid, new \Models\AuctionBid());
								
								$response = $auctionBid->delete();
								
								if ($response){
									
									$afterBid = $this->getAuctionController()->updateBids($this->auction, $auctionBid->getIdEntity());
									
									if (!$afterBid){
										$afterBid = new \Models\AuctionBid();
										$user = json_decode(json_encode( new \Models\User() ));
									}else{
										$user = json_decode(json_encode( \Models\User::findById( $afterBid->getIdUser() )));	
									}
									
									$nameEntity = $msg->data->auctionBid->nameEntity;
									
									$idEntity = $msg->data->auctionBid->idEntity;
									
									$msg->data = json_decode(json_encode($afterBid));
									
									$msg->data->best = true; // $afterBid null
									
									$msg->data->idEntity = $idEntity;
									
									$msg->data->nameEntity = $nameEntity;
									
									$msg->data->user = $user;

									unset($msg->data->auctionBid);
									
									$socket = $socketClient;
									
									//socket client after bid
									foreach ($this->clientMap as $index => $user){//carlos
										
										if ($user->getId() == $msg->data->user->idUser){
											
											$socket = $this->clients[$index];
											
											break;
										}
									}
									
									$this->sendMessageForRole($socket, $msg, self::BID_KIND,$this->clientMap);//carlos
									
								}
							}
						}					
								
						break;
					case self::SYSTEM_TYPE:
						// SYSTEM MSG
						$response_text = $this->mask(json_encode($msg));
							
						$this->sendMessageToAll($response_text);
							
						break;
					case self::AUTH_TYPE:
						
						$userU = $this->getJsonMapper()->map($msg->data->user, new \Models\User());
						$keyU = $userU->getKeyUser();
						$roleU =  $userU->getRole()->getKeyRole();
						$this->clientMap[$id] = $userU;
						
						$this->logInfo("\n [+]User $keyU  $roleU - Socket $id \n");
						
						break;
				}
				
			}catch(\Exception $e){
				
				$this->logInfo('\n\n Error: '. $e->getMessage());
			}
		
		}
	}
	
	/**
	 * Cliente del que no se puede obtener respuesta
	 * @param unknown $disconectedClient socket
	 */
	public function disconnectedClient($disconectedClient){
		
		socket_getpeername($disconectedClient, $ip);
		
		$id = intval($disconectedClient);
		
		$response = $this->mask(json_encode(array('type'=> self::SYSTEM_TYPE, 'data' => array('message'=>$ip.' desconectado'))));
		
		$this->sendMessageToAll($response);
			
		unset($this->clients[$id]);
		
		unset($this->clientMap[$id]);
		
		$this->logInfo("\n [-]Disconected: $disconectedClient of $ip id: $id ");

	}
	
	/**
	 * Finalizar una subasta
	 */
	private function finishAuction(){
		
		$status = \Models\AuctionStatus::findOne(array('keyStatus' => \Models\AuctionStatus::FINALIZED));
		$this->auction->setIdStatus($status->getId());
		
		$result = $this->auction->saveOrUpdate();
		
		if ($result){
				
			foreach ($this->clients as $socket){
				
				$msg = $this->mask(json_encode(array('type'=> self::SYSTEM_TYPE,  'kind' => self::DISCONNECTION_KIND , 'data' => null) ));//CARLOS
				
				$this->sendMessage($socket, $msg);
				
				if ($socket === $this->socket){
					continue;
				}
			
				socket_close($socket);
			}
				
			$this->clients = array(intval($this->socket) => $this->socket);
			
			$info = "\n\n- FINALIZO SUBASTA :  ".$this->auction->getAuctionKey();
			$info .= "\n-------------------------------------------------------------";
			$info .= "\n.... awaiting connections ...";
			
			$this->logInfo($info);
			
		}else{
			
			$this->logInfo("\n\n- ERROR AL FINALIZAR SUBASTA :  ".$this->auction->getAuctionKey()."\n\n");
			
		}
		
		$this->auction = null;
	}
	
	/**
	 * Enviar mensaje a todos los clientes dado su rol
	 * @param unknown $senderSocket socket que realiza el mensaje
	 * @param unknown $msg mensaje
	 * @param unknown $kind tipo
	 * @return boolean
	 */
	protected function sendMessageForRole($senderSocket, $msg, $kind, $users){
		
		$idSender = intval($senderSocket);
		$senderUser = $users[$idSender];
		
		//hidden general information
		$msg->data = $this->getAuctionController()->wrapHiddenUserInformation($msg->data);
		
		if (isset($msg->data->user)){
			$msg->data->user = $this->getAuctionController()->wrapHiddenUserInformation($msg->data->user);
		}
		
		$response = $this->mask(json_encode($msg));
		
		//hidden information for suppliers
		if($senderUser->isManager()){
			$msg->data->user->name = self::ADMIN_NAME;
		}else{
			$msg->data->user->name = self::HIDDEN_NAME;
		}
		
		if (strcmp($kind, self::BID_KIND) == 0){
			unset($msg->data->user->idUser);
			unset($msg->data->idUser);
		}
		
		$hiddenResponse = $this->mask(json_encode($msg));
		
		foreach($this->clients as $socketClient){
			
			if ($socketClient != $this->socket){// No master
				$idReceiver = intval($socketClient);
				$userReceiver = $users[$idReceiver]; 
				
				if (isset($userReceiver) && ($userReceiver->isManager() || $userReceiver->isAuditor() || $idReceiver == $idSender) ){
					
					@socket_write($socketClient,$response,strlen($response));
					
				}else{
					
					@socket_write($socketClient,$hiddenResponse,strlen($hiddenResponse));
				}
			}
		}
		return true;
	}
	
}


?>