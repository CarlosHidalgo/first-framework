<?php

namespace Models;

class Participants extends Model{
	const TABLE = "confirmation";
	const PER_PAGE = 4;
	
	const COLUMNS =	array(
			self::PRIMARY_KEY => array(self::PRIMARY_KEY, "i"),
			"idAuction"					=> 	array("idAuction", "i"),
			"idUser"	 			=>	array("idUser", "i"),
			"confirm" 			=>	array("confirm", "i"),
			"idDatasheet"		=> array("idDatasheet","i"),
			"token"		=> array("token","s")
	);
	
	private $id = null;
	private $idAuction;
	private $idUser;
	private $confirm;
	private $idDatasheet;
	private $token;
	
	public function getId(){
		return $this->id;
	}
	
	public function setId($id){
		$this->id = $id;
	}
	
	public function getIdAuction(){
		return $this->idAuction;
	}
	
	public function setIdAuction($idAuction){
		$this->idAuction = $idAuction;
	}
	
	public function getIdUser(){
		return $this->idUser;
	}
	
	public function setIdUser($idUser){
		$this->idUser = $idUser;
	}
	
	public function getConfirm(){
		return $this->confirm;
	}
	
	public function setConfirm($confirm){
		$this->confirm = $confirm;
	}
	public function getIdDataSheet(){
		return $this->idDatasheet;
	}
	
	public function setIdDataSheet($idDatasheet){
		$this->idDatasheet = $idDatasheet;
	}
	
	public function getToken(){
		return $this->token;
	}
	
	public function setToken($token){
		$this->token = $token;
	}
}

?>