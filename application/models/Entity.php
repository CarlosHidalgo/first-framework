<?php

namespace Models;

class Entity extends Model implements \JsonSerializable{
	
	const TABLE = "entity";
	const PRIMARY_KEY = 'idEntity'; 
	const COLUMNS =	array(
			self::PRIMARY_KEY => array(self::PRIMARY_KEY, "i" ),
			"name"			=> 	array("nameEntity", "s"), 
			"key"			=>	array("keyEntity", "s")
			
	);
	
	private $idEntity = null;
	private $nameEntity;
	private $keyEntity;
	
	public function getId(){
		return $this->idEntity;
	}
	
	public function setId($id){
		$this->idEntity = $id;
	}
	
	public function getName(){
		return $this->nameEntity;
	}
	
	public function setName($name){
		$this->nameEntity = $name;
	}
	
	public function getKey(){
		return $this->keyEntity;
	}
	
	public function setKey($key){
		$this->keyEntity = $key;
	}
	
	public function jsonSerialize() {
	
		return get_object_vars($this);
	}
	
	/**
	 * Obtiene las entidades del sistema dado los id´s proporcioandos
	 * @param unknown $ids arreglo de id's de entidades
	 */
	public static function getByIds(array $ids){
		
		$conection = new \App\Connection();
		$query = new \App\QueryBuilder();
		$table = self::TABLE;
	
		$integer = 'i';
		$types = "";
		$in = '';
		
		foreach ($ids as $index => $id){
			$in.= "?";
			$types.= $integer;
			
			if ($index < count($ids) -1 ){
				$in.= ',';
			}
			
		}
		
		$query->select()->from($table)->where(" idEntity IN ($in) ", $ids);
		
		$p = $query->getParameters();
		$parameters = \App\Utils::makeArrayValuesToReferenced($p);
		
		$response = $conection->prepareQuery($query->getQueryString(), $parameters, $types, \Models\Entity::class);
		
		return $response;
	}
	
}
?>