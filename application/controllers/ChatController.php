<?php
namespace Controllers;

/**
 * Controlador de Chat
 * @author clopezh
 *
 */
class ChatController extends Controller{
	
	/**
	 * Guarda el chat de una subasta
	 * @param unknown $idAuction identificador de subasta
	 * @param unknown $idUser identificador de usuaurio
	 * @param unknown $message mensaje
	 * @param unknown $time fecha y hora en que se efectuo el mensaje
	 * @return \Models\Ambigous
	 */
	public function saveChat($idAuction, $idUser, $message, $time){
		
		$c = new \Models\Chat();
			
		$c->setIdAuction($idAuction);
		$c->setIdUser($idUser);
		$c->setMessage($message);
		$c->setTime($time);
			
		$response = $c->save();
		
		return $response;
	}
	
}