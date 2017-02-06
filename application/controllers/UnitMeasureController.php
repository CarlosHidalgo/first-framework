<?php

namespace Controllers;

/**
 * Controlador de unidades de medida
 * @author maque
 *
 */
class UnitMeasureController extends \Controllers\Controller{

	/**
	 * Busqueda de unidades de medidas disponibles
	 */
	
	public function getAllUnitMeasure(){
	
		$allUOM = \Models\UnitMeasure::all();
	
		return $allUOM;
	}
	
	/**
	 * @param $params IdUnitMeasure  de la subasta
	 * @return object(Models\UnitMeasure)
	 */
	
	public function getUnitMeasureByAuction($params){

		$unitMeasure = \Models\UnitMeasure::findById($params);

		return $unitMeasure;
	}
	
	
	
}


?>