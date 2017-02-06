<?php

namespace Models;

/**
 * Modelo de producto
 * @author clopezh
 *
 */
class Product  extends Model{
	const TABLE = "product"; 
	const PER_PAGE = 10;
	const COLUMNS =	array(
			self::PRIMARY_KEY => array(self::PRIMARY_KEY, "i" ),
			"productName"		=>	array("productName", "s"),
			"keyProduct"	=> 	array("keyProduct", "s"),
			"idDatasheet"	=>	array("idDatasheet","i")
	);
	
	private $id = null;
	private $keyProduct;
	private $productName;
	private $idDatasheet;
	
	public function getId(){
		return $this->id;
	}
	
	public function setId($id){
		$this->id = $id;
	}
	
	public function getName(){
		return $this->productName;
	}
	
	public function setName($name){
		$this->productName = $name;
	}
	
	public function getKeyProduct(){
		return $this->keyProduct;
	}
	
	public function setKeyProduct($keyProduct){
		$this->keyProduct = $keyProduct;
	}
	
	public function getIdDatasheet(){
		return $this->idDatasheet;
	}
	
	public function setIdDatasheet($idDatasheet){
		$this->idDatasheet = $idDatasheet;
	}	
	
}
?>
