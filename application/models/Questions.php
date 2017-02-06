<?php

namespace Models;

class Questions extends Model{
	const TABLE = "questions";
	const PER_PAGE = 4;
	const COLUMNS =	array(
			self::PRIMARY_KEY => array(self::PRIMARY_KEY, "i"),
			"idAuction"					=> 	array("idAuction", "i"),
			"questions"	 			=>	array("questions","s"),
			"answer"				=> array("answer","s"),
			"sent"				=> array("sent","i")
	);
	
	private $id = null;
	private $idAuction;
	private $questions;
	private $answer;
	private $sent;
	
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
	
	public function getQuestions(){
		return $this->questions;
	}
	
	public function setQuestions($questions){
		$this->questions = $questions;
	}
	
	public function getAnswer(){
		return $this->answer;
	}
	
	public function setAnswer($answer){
		$this->answer = $answer;
	}
	
	public function getSent(){
		return $this->sent;
	}
	
	public function setSent($sent){
		$this->sent = $sent;
	}
	
}

?>