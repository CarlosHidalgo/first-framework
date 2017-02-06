<?php

namespace Controllers;

/**
 * Controlador de estados de la subasta
 * @author maque
 *
 */
class AuctionStatusController extends \Controllers\Controller{	
	
	/**
	 * Busqueda de estados de la subasta
	 */
	public function getAllStatus(){
	
		$allStatus = \Models\AuctionStatus::all();
	
		return $allStatus;
	
	}
	
	/**
	 * Devuelve object(Models\AuctionStatus)
	 * @param $params clave del estado keyStatus ::CANCELED
	 */	
	public function getStatus($params){
	
		$status = \Models\AuctionStatus::findOne($params);
		
		return $status;	
		
	}	
	
}


?>