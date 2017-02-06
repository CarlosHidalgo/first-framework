<?php
namespace Controllers;

/**
 * Modelo para los sockets clientes del WSAuction
 * @author clopezh
 *
 */
class ClientWSAuction {

	private $idUser;
	private $keyUser;
	private $socket;
	
	public function getIdUser(){
		return $this->idUser;
	}
	
	public function setIdUser($idUser){
		$this->idUser = $idUser;
	}
	
	public function getKeyUser(){
		return $this->keyUser;
	}
	
	public function setKeyUser($keyUser){
		$this->keyUser = $keyUser;
	}
	
	public function getSocket(){
		return $this->socket;
	}
	
	public function setSocket($socket){
		$this->socket = $socket;
	}
}
?>