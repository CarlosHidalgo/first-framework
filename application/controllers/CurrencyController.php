<?php

namespace Controllers;

/**
 * Controlador de moneda (DL, MX)
 * @author maque
 *
 */
class CurrencyController extends \Controllers\Controller{

	/**
	 * Busqueda de Monedas disponibles
	 */
	public function getAllCurrency(){

		$allCurrency = \Models\Currency::all();

		return $allCurrency;
	}
	
	/**
	 * @param $params IdCurrency de la subasta
	 * @return object(Models\Currency)
	 */
	public function getCurrencyByAuction($params){
	
		$currency = \Models\Currency::findById($params);	
	
		return $currency;
	}
	
	
}


?>