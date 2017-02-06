<?php
namespace Controllers;

/**
 * Controlador de tipos de subasta (Licitacion, Servicio)
 * @author maque
 *
 */
class AuctionTypeController extends \Controllers\Controller{

	/**
	 * Busqueda de estados de la subasta
	 */
	public function getAllActionTypes(){

		$allActionTypes = \Models\AuctionType::all();

		return $allActionTypes;

	}
	
	/**
	 * @param $params IdAuctionType
	 * @return object(Models\AuctionType)
	 */
	
	public function  getAuctionTypeById($params){
		
		$auctionType = \Models\AuctionType::findById($params);
		
		return $auctionType;
	}
	
}


?>