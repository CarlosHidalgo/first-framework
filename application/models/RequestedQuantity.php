<?php

namespace Models;

/**
 * Cantidad solictada por entidad en cada subasta.
 * @author clopezh
 *
 */
class RequestedQuantity extends Model{
	
	const TABLE = "requestedQuantity";
	const COLUMNS =	array(
			self::PRIMARY_KEY => array(self::PRIMARY_KEY, "i" ),
			"idEntity"			=> 	array("idEntity", "i"), 
			"idAuction"			=>	array("idAuction", "i"),
			"quantity"			=> 	array("quantity", "d") 
			
	);
	
	private $id;
	private $idEntity;
	private $idAuction;
	private	$quantity;
	
	
	public function getId(){
		return $this->id;
	}
	
	public function setId($id){
		$this->id = $id;
	}
	
	public function getIdAuction(){
		return $this->idAuction;
	}
	
	public function setIdAuction($idAuction){
		$this->idAuction = $idAuction;
	}
	
	public function getIdEntity(){
		return $this->idEntity;
	}
	
	public function setIdEntity($idEntity){
		$this->idEntity = $idEntity;
	}
	
	public function getQuantity(){
		return $this->quantity;
	}
	
	public function setQuantity($quantity){
		$this->quantity = $quantity;
	}
	
	/**
	 * Retorna la cantidades requeridas por cada entidad.
	 * @param unknown $idAuction
	 * @return Ambigous <multitype:, boolean, multitype:object >
	 */	
	public function getRequestedQuantity($idAuction){
	
		//Columnas de la tabla requestedquantity
		$colsR = \Models\RequestedQuantity::COLUMNS;
	
		//Columna de la tabla entidad
		$colsE = \Models\Entity::COLUMNS;
	
		$query =  new \App\QueryBuilder();
		$query->select();
	
		$query->from(\Models\RequestedQuantity::TABLE)->innerJoin($colsR['idEntity'][0], \Models\Entity::TABLE, $colsE['idEntity'][0]);
		$query->where($colsR['idAuction'][0].' = ?', $idAuction);
	
		$con = new \App\Connection();
		$info = $query->getParameters();
		$request = \App\Utils::makeArrayValuesToReferenced($info);
		$types = $colsR['idAuction'][1];
	
		$response = $con->prepareQuery($query->getQueryString(), $request, $types, \Models\RequestedQuantity::Class);
		
		return $response;
	}
	
}
?>