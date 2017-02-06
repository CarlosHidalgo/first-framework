<?php

namespace Models;

class Currency extends Model{
	
	const TABLE = "currency";
	const COLUMNS =	array(
			self::PRIMARY_KEY => array(self::PRIMARY_KEY, "i" ),
			"type"	=> 	array("type", "s"),
			"currencyName"	=> 	array("currencyName", "s")
	);
	
	private $id = null;
	private $type;
	private $currencyName;
	
	public function getId(){
		return $this->id;
	}
	
	public function getName(){
		return $this->currencyName;
	}
	
	public function getType(){
		return $this->type;
	}
	
}
?>