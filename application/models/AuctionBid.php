<?php

namespace Models;

/**
 * Modelo para ofertas por subasta
 * @author clopezh
 *
 */
class AuctionBid implements \JsonSerializable{
	
	const TABLE = "auctionBid";
	
	const COLUMNS =	array(
			"idAuction"		=>	array("idAuction", "i"),
			"idUser"	=> 	array("idUser", "i"),
			"bid"		=>	array("bid", "d"),
			"bidDate"		=>	array("bidDate", "s"),
			"idEntity"		=>	array("idEntity", "i"),
			"best"		=>	array("best", "i")
	);
	
	private $idUser;
	private $idAuction;
	private $bid;
	private $bidDate;
	private $idEntity;
	private $best;
	
	public function getIdUser(){
		return $this->idUser;
	}
	
	public function setIdUser($idUser){
		$this->idUser = $idUser;
	}
	
	public function getIdAuction(){
		return $this->idAuction;
	}
	
	public function setIdAuction($idAuction){
		$this->idAuction = $idAuction;
	}
	
	public function getBid(){
		return $this->bid;
	}
	
	public function setBid($bid){
		$this->bid = floatval($bid);
	}
	
	public function getIdEntity(){
		return $this->idEntity;
	}
	
	public function setIdEntity($idEntity){
		$this->idEntity = $idEntity;
	}
	
	public function isBest(){
		return $this->best;
	}
	
	public function setBest($best){
		$this->best = $best;
	}
	
	public function getBidDate(){
		return $this->bidDate;
	}
	
	public function setBidDate($bidDate){
		$this->bidDate = $bidDate;
	}
	
	/**
	 * Guardar la oferta actual
	 * @return Ambigous <multitype:, boolean, multitype:object >
	 */
	public function save(){
		
		$conection = new \App\Connection();
		$query = new \App\QueryBuilder();
		$table = self::TABLE;
		
		$query->append(" INSERT INTO $table (idAuction, idUser, bid, bidDate, idEntity, best) VALUES (?,?,?,?,?,?)");
		$types = "iidsii";
		
		$p = array($this->idAuction, $this->idUser, $this->bid, $this->bidDate,$this->idEntity, $this->best);
		$parameters = \App\Utils::makeArrayValuesToReferenced($p);
		
		$response = $conection->prepareQuery($query->getQueryString(), $parameters, $types, \Models\AuctionBid::class);
		
		return $response;
	}
	
	/**
	 * Acutualiza la oferta actual dado su campo de fecha
	 * @return Ambigous <multitype:, boolean, multitype:object >
	 */
	public function update(){
		$conection = new \App\Connection();
		$query = new \App\QueryBuilder();
		$table = self::TABLE;
		
		$query->append(" UPDATE $table SET  idAuction = ? , idUser = ? , bid = ? , bidDate = ? , idEntity = ? , best = ? WHERE bidDate  = ? and idAuction = ? and idUser = ? and idEntity = ? ");
		$types = "iidsiisiii";
		
		$p = array($this->idAuction, $this->idUser, $this->bid, $this->bidDate, $this->idEntity, $this->best, $this->bidDate, $this->idAuction, $this->idUser, $this->idEntity);
		$parameters = \App\Utils::makeArrayValuesToReferenced($p);
		
		$response = $conection->prepareQuery($query->getQueryString(), $parameters, $types, \Models\AuctionBid::class);
		
		return $response;
	}
	
	/**
	 * Actualiza un conjunto de ofertas realizadas
	 * @param unknown $idAuction
	 * @param array $bids
	 * @param unknown $idEntity
	 * @param unknown $idUser
	 * @param string $best
	 * @return Ambigous <multitype:, boolean, multitype:object >
	 */
	public static function updateBest($idAuction, array $bids, $idEntity, $idUser, $best = true){
		
		$conection = new \App\Connection();
		$query = new \App\QueryBuilder();
		$table = self::TABLE;

		$query->append(" UPDATE $table SET best = ? WHERE idAuction = ? and idUser = ? and idEntity = ? ");
		$types = "iidsiisiii";
		
		$p = array($this->idAuction, $this->idUser, $this->bid, $this->bidDate, $this->idEntity, $this->best, $this->bidDate, $this->idAuction, $this->idUser, $this->idEntity);
		$parameters = \App\Utils::makeArrayValuesToReferenced($p);
		
		$response = $conection->prepareQuery($query->getQueryString(), $parameters, $types, \Models\AuctionBid::class);
		
		return $response;
		
	}
	
	
	/**
	 * Elimina la oferta actual
	 * por identificador de subasta, identificador de usuario e identificador de entidad como parámetros obligatorios.
	 * opcionalmente se puede indicar la fecha de oferta o si es la mejor oferta
	 * @return Ambigous <multitype:, boolean, multitype:object >
	 */
	public function delete(){
		
		$conection = new \App\Connection();
		$query = new \App\QueryBuilder();
		$table = self::TABLE;
		
		$query->append("DELETE FROM $table ");
		$query->where("  WHERE idAuction = ? AND idUser = ? AND idEntity = ? ", array( $this->idAuction, $this->idUser, $this->idEntity));
		$types = "iii";
		
		if (isset($this->best)){
			$query->where(' best = ? ', $this->best);
			$types.= "i";
		}
		
		if (isset($this->bidDate)){
			$query->where(' bidDate = ? ', $this->bidDate);
			$types.= "s";
		}
		
		$p = $query->getParameters();
		$parameters = \App\Utils::makeArrayValuesToReferenced($p);
		
		$response = $conection->prepareQuery($query->getQueryString(), $parameters, $types, \Models\AuctionBid::class);
		
		return $response;
	}
	
	/**
	 * Busca todos las ofertas de chat de una subasta
	 * @param unknown $idAuction identificador de subasta
	 * @param int $idEntity identificador de entidad para la oferta
	 * @return Ambigous <multitype:, boolean, multitype:object >
	 */
	public static function findByAuction($idAuction, $idEntity = null){
		
		$response = \Models\AuctionBid::findBestByAuction($idAuction, null, $idEntity);
		
		return $response;
	}
	
	/**
	 * Busca todos las mejores ofertas de chat de una subasta
	 * @param unknown $idAuction identificador de subasta
	 * @param boolean $best indica si se obtendran lás mejores ofertas (true -> mejores, false ->normales, null -> todas)
	 * @param int $idEntity identificador de la entidad para las ofertas
	 * @return Ambigous <multitype:, boolean, multitype:object >
	 */
	public static function findBestByAuction($idAuction, $best = true, $idEntity = null){
	
		$conection = new \App\Connection();
		$query = new \App\QueryBuilder();
		$table = self::TABLE;
	
		$types = "i";
		$query->select()->from($table)->innerJoin('idEntity', \Models\Entity::TABLE, \Models\Entity::PRIMARY_KEY)->where(" idAuction = ? ", $idAuction);
		
		if (isset($best)){
			$types.= 'i';
			
			$query->where(" best = ?  ", $best);
		}
		
		if (isset($idEntity)){
			$types.= 'i';
				
			$query->where(" $table.idEntity = ?  ", $idEntity);
		}
		
		$p = $query->getParameters();
		$parameters = \App\Utils::makeArrayValuesToReferenced($p);
		
		$response = $conection->prepareQuery($query->getQueryString(), $parameters, $types, \Models\AuctionBid::class);
		
		return $response;
	}
	
	/**
	 * Busca todas las ofertas realizadas por un usuario en una subasta
	 * ordenado por usuario
	 * @param unknown $idAuction identificador de subasta
	 * @param unknown $idUser identificador de usuario
	 * @param unknown $best indica si se búscara la mejor oferta (true), todas (null), las ofertas normales (false)
	 * @return objetos bidAuction
	 */
	public static function findByAuctionWihtUser($idAuction , $idUser = null, $best =  null){
		$conection = new \App\Connection();
		$query = new \App\QueryBuilder();
		$table = self::TABLE;
		
		$types = "i";
		$query->select()->from($table)->innerJoin('idEntity', \Models\Entity::TABLE, \Models\Entity::PRIMARY_KEY);	
		$query->innerJoin('idUser', \Models\User::TABLE, \Models\User::PRIMARY_KEY);
		$query->where(" idAuction = ? ", $idAuction);
		
		if (isset($idUser)){
			$types .= 'i';
			
			$query->where(" $table.idUser = ? ", $idUser);
		}
		
		if (isset($best)){
			$types .= 'i';
				
			$query->where(" $table.best = ? ", $best);
		}
		
		$query->orderBy(" $table.bidDate");
		
		$p = $query->getParameters();
		$parameters = \App\Utils::makeArrayValuesToReferenced($p);

		$response = $conection->prepareQuery($query->getQueryString(), $parameters, $types, \Models\AuctionBid::class);
	
		return $response;
	}
	
	public function jsonSerialize() {
		
		return get_object_vars($this);
	}
	
}
?>