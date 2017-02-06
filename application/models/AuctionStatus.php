<?php

namespace Models;

/**
 * Estado de la subasta
 * @author clopezh
 *
 */
class AuctionStatus extends Model{
	
	const TABLE = "auctionstatus";
	const COLUMNS =	array(
			self::PRIMARY_KEY => array(self::PRIMARY_KEY, "i" ),
			"keyStatus"	=> 	array("keyStatus", "s"),
			"statusName"	=> 	array("statusName", "s")
	);
	
	const AVAILABLE =  'AVAILABLE';
	const NOT_AVAILABLE = 'NOT_AVAILABLE';
	const CANCELED = 'CANCELED';
	const FINALIZED = 'FINALIZED';
	
	private $id = null;
	private $statusName;
	private $keyStatus;
	
	public function getId(){
		return $this->id;
	}
	
	public function getStatusName(){
		return $this->statusName;
	}
	
	public function getKeyStatus(){
		return $this->keyStatus;
	}
	
}
?>