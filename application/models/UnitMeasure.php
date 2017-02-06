<?php

namespace Models;

/**
 * Modelo para unidades de medida
 * @author clopezh
 *
 */
class UnitMeasure extends Model{
	
	const TABLE = "unitMeasure";
	const PRIMARY_KEY = 'idUnitMeasure';
	const COLUMNS =	array(
			self::PRIMARY_KEY => array(self::PRIMARY_KEY, "i" ),
			"keyUnitMeasure"	=> 	array("keyUnitMeasure", "s"),
			"nameUnitMeasure"	=> 	array("nameUnitMeasure", "s"),
	);
	
	private $idUnitMeasure = null;
	private $keyUnitMeasure;
	private $nameUnitMeasure;
	
	public function getId(){
		return $this->idUnitMeasure;
	}
	
	public function setIdUnitMeasure($idUnitMeasure){
		$this->idUnitMeasure = $idUnitMeasure;
	}
	
	public function getKeyUnitMeasure(){
		return $this->keyUnitMeasure;
	}
	
	public function setKeyUnitMeasure($keyUnitMeasure){
		$this->keyUnitMeasure = $keyUnitMeasure;
	}
	
	public function getNameUnitMeasure(){
		return $this->nameUnitMeasure;
	}
	
	public function setNameUnitMeasure($nameUnitMeasure){
		$this->nameUnitMeasure = $nameUnitMeasure;
	}
}

?>