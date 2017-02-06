<?php

namespace Models;

class Chat implements \JsonSerializable{
	
	const TABLE = "chat";
	const PER_PAGE = 4;
	const COLUMNS =	array(
			"idAuction"		=>	array("idAuction", "i"),
			"idUser"	=> 	array("idUser", "i"),
			"message"		=>	array("message", "s"),
			"time"		=>	array("time", "s")
	);
	
	private $idUser;
	private $idAuction;
	private $message;
	private $time;
	
	/**
	 * Guardar el chat actual
	 * @return Ambigous <multitype:, boolean, multitype:object >
	 */
	public function save(){
		
		$conection = new \App\Connection();
		$query = new \App\QueryBuilder();
		$table = self::TABLE;
		
		$query->append(" INSERT INTO $table (`idAuction`, `idUser`, `message`, `time`) VALUES (?, ?,?, ?)");
		$types = "iiss";
		
		$p = array($this->idAuction, $this->idUser, $this->message, $this->time);
		$parameters = \App\Utils::makeArrayValuesToReferenced($p);
		
		$response = $conection->prepareQuery($query->getQueryString(), $parameters, $types, \Models\Chat::class);
		
		return $response;
	}
	
	/**
	 * Busca todos los mensajes de chat de una subasta
	 * @param unknown $idAuction identificador de subasta
	 * @return Ambigous <multitype:, boolean, multitype:object >
	 */
	public static function findByAuction($idAuction){
		
		$conection = new \App\Connection();
		$query = new \App\QueryBuilder();
		$table = self::TABLE;
		
		$p = array($idAuction);
		$parameters = \App\Utils::makeArrayValuesToReferenced($p);
		$types = "i";
		
		$query->select()->from($table)->innerJoin('idUser', \Models\User::TABLE, \Models\User::PRIMARY_KEY)->where(" idAuction = ?", $parameters);
		
		$response = $conection->prepareQuery($query->getQueryString(), $parameters, $types, \Models\Chat::class);
		
		return $response;
	}
	
	public function getIdUser(){
		return $this->idUser;
	}
	
	public function setIdUser($idUser){
		$this->idUser = $idUser;
	}
	
	public function  getIdAuction(){
		return $this->idAuction;
	}
	
	public function setIdAuction($idAuction){
		$this->idAuction = $idAuction;
	}
	
	public function getMessage() {
		return $this->message;
	}
	
	public function setMessage($message){
		$this->message = $message;
	}
	
	public function getTime(){
		return $this->time;
	}
	
	public function setTime($time){
		$this->time = $time;
	}
	
	public function jsonSerialize() {
		
		return get_object_vars($this);
	} 
	
}
?>