<?php

namespace Models;

class AuctionType extends Model{
	
	const TABLE = "auctionType";
	const COLUMNS =	array(
			self::PRIMARY_KEY => array(self::PRIMARY_KEY, "i" ),
			"type"	=> 	array("type", "s"),
			"key"	=> 	array("key", "s")
	);
	
	private $id = null;
	private $type;
	private $key;
	
	public function getId(){
		return $this->id;
	}
	
	public function getKey(){
		return $this->key;
	}
	
	public function getType(){
		return $this->type;
	}
	
}
?>