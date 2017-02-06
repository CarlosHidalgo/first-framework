<?php

namespace Models;

/**
 * Modelo de Datasheet
 * @author maque
 *
 */
class File  extends Model{
	const TABLE = "file";
	const COLUMNS =	array(
			self::PRIMARY_KEY => array(self::PRIMARY_KEY, "i" ),
			"archivo"		=>	array("archivo", "s"),
			"name"          => array("name","s"),
			"type"			=> array("type","s"),
			"size"			=> array("size","i")
			
	);

	private $id = null;
	private $archivo;
	private $name;
	private $type;
	private $size;
	
		

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getArchivo(){
		return $this->archivo;
	}

	public function setArchivo($archivo){
		$this->archivo = $archivo;
	}
	
	public function getName() {
		return $this->name;

	}
	
	public function setName($name){
		$this->name = $name;
	}  
	
	public function getType() {
		return $this->type;
	
	}
	
	public function setType($type){
		$this->type = $type;
	}
	
	public function getSize() {
		return $this->size;
	
	}
	
	public function setSize($size){
		$this->size = $size;
	}
}
?>