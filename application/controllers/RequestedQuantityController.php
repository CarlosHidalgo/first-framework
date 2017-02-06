<?php

/**
 * Controlador de las cantidades por entidad de las subastas
 * @author maque
 */
namespace Controllers;

class RequestedQuantityController extends \Controllers\Controller{
	

  public  function  addRequestedQuantity($arrayEntities , $auction, $arrayQuantities){
	
		$requestedQuantity= new \Models\RequestedQuantity();
		$requestedQuantity->setIdEntity($arrayEntities);
		$requestedQuantity->setIdAuction($auction);
		$requestedQuantity->setQuantity($arrayQuantities);
			
		$response = $requestedQuantity->saveOrUpdate();
				
		if ($response === true || $response == 1) {
			
		return $requestedQuantity;
	
		}	
		else {
			return $response;
			
			
		}
	}
	
	/**
	 * @param $params IdAuction
	 * @return array [object(Models\RequestedQuantity)]
	 */	
	public function getRequestedQuantityByAuction($params){
		
		$requestQ = new \Models\RequestedQuantity();
		$requestQuantities = $requestQ->getRequestedQuantity($params);
				
		return $requestQuantities;
	}
}
?>			
		