<?php

namespace Models;

/**
 * Modelo de Subasta
 * @author clopezh
 *
 */
class Auction extends Model implements \JsonSerializable {
	
	const PRIMARY_KEY = 'idAuction';
	const TABLE = "auction";
	const PER_PAGE = 4;
	const COLUMNS =	array(
			self::PRIMARY_KEY => array(self::PRIMARY_KEY, "i" ),
			"auctionKey"				=> 	array("auctionKey", "s"), 
			"idStatus"					=>	array("idStatus", "i"),
			"startDate"					=>	array("startDate", "s"), 
			"rememberStart"				=>	array("rememberStart", "i"),
			"endDate"					=>	array("endDate", "s"),
			"auctionName"				=>	array("auctionName","s"),
			"idProduct"					=>	array("idProduct", "i"),
			"idCurrency"				=>	array("idCurrency", "i"),
			"totalQuantity"				=>	array("totalQuantity", "d"),
			"idUnitMeasure"				=>	array("idUnitMeasure", "i"),
			"idAuctionType" 			=>	array("idAuctionType", "i"),
			"confirmExpirationDate" 	=>	array("confirmExpirationDate", "s"),
			"productStartDeliveryDate"	=>	array("productStartDeliveryDate", "s"),
			"productEndDeliveryDate"	=>	array("productEndDeliveryDate", "s"),
			"openPrice"					=>	array("openPrice", "d"),
			"idFile"					=>  array("idFile", "i")
	);
	
	private $idAuction = null;
	private $auctionKey;
	private $idStatus;
	private $startDate;
	private $rememberStart;
	private $endDate;
	private $auctionName;
	private $idProduct;
	private $idCurrency;
	private $totalQuantity;
	private $idUnitMeasure;
	private $idAuctionType;
	private $confirmExpirationDate;
	private $productStartDeliveryDate;
	private $productEndDeliveryDate;
	private $openPrice;
	private $idFile;
	
	// No persiste
	private $product;
	private $currency;
	private $unitMeasure;
	private $requestQuantities;
	private $bids;
	private $bestBids;
	
	/**
	 * Modifica la información de los bids para la subasta
	 * @param array $bids
	 */
	public function setBids(array $bids){
		
		$this->bids = $bids;
	}
	
	/**
	 * Retorna la información de las ofertas de la subasta
	 */
	public function getBids(){
		
		return $this->bids;
	}
	
	/**
	 * Modifica la información de las mejores ofertas para la subasta
	 * @param array $bestBids
	 */
	public function setBestBids(array $bestBids){
		$this->bestBids = $bestBids;
	}
	
	/**
	 * Obtiene las mejores ofertas para la subasta actual
	 */
	public function getBestBids(){
		
		return $this->bestBids;
	}
	
	
	/**
	 * Modifica el producto de la subasta
	 * @param \Models\Product $product
	 */
	public function setProduct(\Models\Product $product){
		$this->idProduct = $product->getId();
		$this->product = $product;
	}
	
	/**
	 * Obtiene la información del producto actual de la subasta con el $idProduct propio
	 */
	public function getProduct(){
		return $this->product;
	}
	
	/**
	 * Modifica la unidad de medida de la subasta
	 * @param \Models\UnitMeasure $unitMeasure
	 */
	public function setUnitMeasure(\Models\UnitMeasure $unitMeasure){
		$this->idUnitMeasure = $unitMeasure->getId();
		$this->unitMeasure = $unitMeasure;
	}
	
	/**
	 * Obtiene la información de la unidad de medida de la subasta actual con su $idUnitMeasure propio
	 */
	public function getUnitMeasure(){
		
		return $this->unitMeasure;
	}
	
	/**
	 * Modifica la información de la moneda en la subasta
	 * @param \Models\Currency $currency
	 */
	public function setCurrency(\Models\Currency $currency){
		
		$this->idCurrency = $currency->getId();
		$this->currency = $currency;
	}
	
	/**
	 * Obtiene la información de la moneda actual de la subasta con el $idCurrency propio
	 */
	public function getCurrency(){
	
		return $this->currency;
	}
	
	/**
	 * 
	 * @param array $requestQuantities
	 */
	public function setRequestQuantities(array $requestQuantities){
		$this->requestQuantities = $requestQuantities;
	}
	
	/**
	 * Retorna las cantidades solicitadas para la subasta por entidad.
	 */
	public function getRequestQuantities(){
		
		return $this->requestQuantities;
	}
	
	/**
	 * Retorna la subasta que se esta ejecutando en este momento
	 * @param $idUser int identificador de usuario
	 * @return Ambigous <multitype:, boolean, multitype:object >
	 */
	public static function getCurrentAuction($idUser = null){
		
		$conection = new \App\Connection();
		$query = new \App\QueryBuilder();
		$table = self::TABLE;
		
		
		$now = date("Y-m-d H:i:s");
		$query->select()->from($table);
		
		if (isset($idUser)){
			$query->innerJoin('idAuction', \Models\Participants::TABLE , 'idAuction');
		}
		
		$query->where(" ? >= startDate  ", $now)->where(" ? < endDate ", $now);
		$types = "ss";
		
		if (isset($idUser)){
			$query->where(" idUser = ? " , $idUser);
			$types.= 'i';
		}
		
		
		
		$p = $query->getParameters();
		$parameters = \App\Utils::makeArrayValuesToReferenced($p);
		
		$response = $conection->prepareQuery($query->getQueryString(), $parameters, $types, \Models\Auction::class);
		
		if ($response && isset($response[0])){
			$response = $response[0];
		}else {
			$response = null;
		}
		
		return $response;
		
	}
	
	public function getId(){
		return $this->idAuction;
	}
	
	public function setId($idAuction){
		$this->idAuction = $idAuction;
	}
	
	public function getAuctionKey(){
		return $this->auctionKey;
	}
	
	public function setAuctionKey($auctionKey){
		$this->auctionKey = $auctionKey;
	}
	
	public function getIdStatus(){
		return $this->idStatus;
	}
	
	public function setIdStatus($idStatus){
		$this->idStatus = $idStatus;
	}
	
	public function getStartDate(){
		return $this->startDate;
	}
		
	public function setStartDate($startDate){
		$this->startDate = $startDate;
	}
		
	public function getEndDate(){
		return $this->endDate;
	}
	
	public function setEndDate($endDate){
		$this->endDate = $endDate;
	}
	
	public function getAuctionName(){
		return $this->auctionName;
	}
	
	public function setAuctionName($auctionName){
		$this->auctionName = $auctionName;
	}
	
	public function getIdProduct(){
		return $this->idProduct;
	}
	
	public function setIdProduct($idProduct){
		$this->idProduct = $idProduct;
	}
	
	public function getIdCurrency(){
		return $this->idCurrency;
	}
	
	public function setIdCurrency($idCurrency){
		$this->idCurrency = $idCurrency;
	}
	
	public function getQuantity(){
		return $this->totalQuantity;
	}
	
	public function setQuantity($quantity){
		$this->totalQuantity = $quantity;
	}
	
	public function getIdAuctionType(){
		return $this->idAuctionType;
	}
	
	public function setIdAuctionType($idAuctionType){
		$this->idAuctionType = $idAuctionType;
	}
	
	public function getConfirmExpirationDate(){
		return $this->confirmExpirationDate;
	}
	
	public function setConfirmExpirationDate($confirmExpirationDate){
		$this->confirmExpirationDate = $confirmExpirationDate;
	}
	
	public function getProductStartDeliveryDate(){
		return $this->productStartDeliveryDate;
	} 
	
	public function setProductStartDeliveryDate($productStartDeliveryDate){
		$this->productStartDeliveryDate = $productStartDeliveryDate;
	}
	
	public function getProductEndDeliveryDate(){
		return $this->productEndDeliveryDate;
	} 
	
	public function setProductEndDeliveryDate($productEndDeliveryDate){
		$this->productEndDeliveryDate = $productEndDeliveryDate;
	}
	
	public function getOpenPrice(){
		return $this->openPrice;
	}
	
	public function setOpenPrice($openPrice){
		$this->openPrice = $openPrice;
	}
	
	public function getIdFile(){
		return $this->idFile;
	}
	
	public function  setIdFile($idFile){
		$this->idFile = $idFile;
	}
	
	public function getIdUnitMeasure(){
		return $this->idUnitMeasure;
	}
	
	public function setIdUnitMeasure($idUnitMeasure){
		$this->idUnitMeasure = $idUnitMeasure;
	}
	
	
	public function isRememberStart(){
		return $this->rememberStart;
	}
	
	public function setRememberStart($rememberStart){
		$this->rememberStart = $rememberStart;
	}
	
	/**
	 * Búsqueda de subasta por parametros
	 * @param unknown $parameters
	 * @param unknown $isRecordCount
	 * @param string $size
	 * @param string $offset
	 * @return Ambigous <multitype:, boolean, multitype:object >
	 */
	public function getByParameters($parameters, $isRecordCount, $size = null, $offset = null){
	
		$colsAuction = \Models\Auction::COLUMNS;
       	$colsProduct = \Models\Product::COLUMNS;
       	$colsConfirmation = \Models\Participants::COLUMNS;
       	$colsUser = \Models\User::COLUMNS;
       	$query = new \App\QueryBuilder ();
       
       	if ($isRecordCount){
       		$query->select ( ' count(*) as total ' );
		}else {
			$query->select ();
		}
		
       $query->from(\Models\Auction::TABLE)->innerJoin($colsAuction['idProduct'][0], \Models\Product::TABLE, $colsProduct['id'][0]);
     if (isset($parameters['idProvider']) && $parameters['idProvider'] != 0){
       $query->innerJoin($colsAuction['idAuction'][0], \Models\Participants::TABLE, $colsConfirmation['idAuction'][0]);
       }
       $types = "";
       
       if (isset($parameters['auctionKey']) && strcmp("", trim($parameters['auctionKey'])) != 0 ){$query->where($colsAuction['auctionKey'][0].'  like  ? ', "%".strtoupper($parameters['auctionKey'])."%");
       		$types .= $colsAuction['auctionKey'][1];
       }
       
       if (isset($parameters['productKey']) && strcmp("", trim($parameters['productKey'])) != 0){
       $query->where($colsProduct['keyProduct'][0].' like ? ', "%".strtoupper($parameters['productKey'])."%"); 
       $types .= $colsProduct['keyProduct'][1];
       }
       
       if (isset($parameters['status']) && $parameters['status'] != 0){
        $query->where($colsAuction['idStatus'][0].' = ? ', $parameters['status']);
       	$types .= $colsAuction['idStatus'][1];
       }
       
      if (isset($parameters['idProvider']) && $parameters['idProvider'] != 0){
        $query->where($colsConfirmation['idUser'][0].' = ? ', $parameters['idProvider']);
       	$types .= $colsConfirmation['idUser'][1];
       }
       
       $con = new \App\Connection();
       if (!$isRecordCount){
       $query->setResultMaxSize($size);
       
       $query->setResultOffset($offset);
       }
	   
       $p = $query->getParameters();
       $paramsReference = \App\Utils::makeArrayValuesToReferenced($p);
       
       $response = $con->prepareQuery($query->getQueryString(), $paramsReference, $types, get_class($this));
       return $response;
	
	}
	
	/**
	 * Obtiene los proveedores de la subasta indicada
	 * @param unknown $idAuction
	 * @param string $isRecordCount
	 * @param string $size
	 * @param string $offset
	 * @return Ambigous <multitype:, boolean, multitype:object >
	 */
	public function getProviderAuctionByAuction($idAuction, $isRecordCount = false, $size = null, $offset = null) {
		
		//Columna tabla user
		$colsU = \Models\User::COLUMNS;
		
		//Columna tabla confirmation
		$colsP = \Models\Participants::COLUMNS;
		
		$con = new \App\Connection();
		$query =  new \App\QueryBuilder();
		
		if ($isRecordCount) {
			$query->select ( ' count(*) as total ' );
		} else {
			$query->select ();
		}
		
		$query->from(\Models\User::TABLE)->innerJoin($colsU['idUser'][0], \Models\Participants::TABLE, $colsP['idUser'][0]);
		$query->where($colsP['idAuction'][0].' = ? ', $idAuction);
		
		if (!$isRecordCount && isset($size) && isset($offset)){
			
			$query->setResultMaxSize($size);
		
			$query->setResultOffset($offset);
		}
		
		$info = $query->getParameters();
		$response = \App\Utils::makeArrayValuesToReferenced($info);
		$types = $colsP['idAuction'][1];
			
		$response = $con->prepareQuery($query->getQueryString(), $response, $types);
		
		return $response;
	}	
	
	public function getProviderConfirm($idAuction, $isRecordCount = false, $size = null, $offset = null) {
	
		//Columna tabla user
		$colsU = \Models\User::COLUMNS;
	
		//Columna tabla confirmation
		$colsP = \Models\Participants::COLUMNS;
	
		$con = new \App\Connection();
		$query =  new \App\QueryBuilder();
	
		if ($isRecordCount) {
			$query->select ( ' count(*) as total ' );
		} else {
			$query->select ();
		}
	
		$query->from(\Models\User::TABLE)->innerJoin($colsU['idUser'][0], \Models\Participants::TABLE, $colsP['idUser'][0]);
		$query->where($colsP['idAuction'][0].' = ? ', $idAuction);
		$query->where($colsP['confirm'][0].' = ? ', TRUE);
	
		if (!$isRecordCount && isset($size) && isset($offset)){
				
			$query->setResultMaxSize($size);
	
			$query->setResultOffset($offset);
		}
	
		$info = $query->getParameters();
		$response = \App\Utils::makeArrayValuesToReferenced($info);
		$types = $colsP['idAuction'][1].$colsP['confirm'][1];
			
		$response = $con->prepareQuery($query->getQueryString(), $response, $types);
	
		return $response;
	}
	
	/**
	 * Retorna las subastas que deben notificarse 15min antes de su inicio
	 * @return Ambigous <multitype:, boolean, multitype:object >
	 */
	public static function getWithoutStartNotification(){
		
		//Columnas de la tabla Auction
		$colsA = self::COLUMNS;
		$hoy = date("Y-m-d H:i:s");
		$status = \Models\AuctionStatus::findOne(array('keyStatus' => \Models\AuctionStatus::NOT_AVAILABLE));
		
		$query =  new \App\QueryBuilder();
		$query->select();
		
		$query->from(self::TABLE);
		$query->where(" rememberStart  = ?  and TIMEDIFF(startDate , ?) <=  '00:15:00' and TIMEDIFF( startDate, ? ) >= '00:00:00' and idStatus = ? ", array(false, $hoy, $hoy,$status->getId() ));
		
		$con = new \App\Connection();
		$p = $query->getParameters();
		$request = \App\Utils::makeArrayValuesToReferenced($p);
		
		$types = $colsA['rememberStart'][1].$colsA['startDate'][1].$colsA['startDate'][1].$colsA['idStatus'][1];
		
		$response = $con->prepareQuery($query->getQueryString(), $request, $types, \Models\Auction::class);
		
		return $response; 
	}
	
	/**
	 * Retorna la siguiente subasta a realizarse
	 */
	public static function getNextAuction($idUser = null){
		
		//Columnas de la tabla Auction
		$colsA = self::COLUMNS;
		$hoy = date("Y-m-d H:i:s");
		$status = \Models\AuctionStatus::findOne(array('keyStatus' => \Models\AuctionStatus::NOT_AVAILABLE));
		
		$query =  new \App\QueryBuilder();
		$query->select();
		
		$query->from(self::TABLE);
		
		if (isset($idUser)){
			$query->innerJoin('idAuction', \Models\Participants::TABLE , 'idAuction');
		}
		
		$query->where(" idStatus = ?  and startDate > ? ", array($status->getId(), $hoy ) );
		$types = $colsA['idStatus'][1].$colsA['startDate'][1];
		
		if (isset($idUser)){
			$query->where(" idUser = ? " , $idUser);
			$types .= $colsA['idAuction'][1];
		}
		
		$query->orderBy('startDate');
		$query->setResultMaxSize(1);
		
		$con = new \App\Connection();
		$p = $query->getParameters();
		$request = \App\Utils::makeArrayValuesToReferenced($p);
		
		$response = $con->prepareQuery($query->getQueryString(), $request, $types, \Models\Auction::class);
		
		if ($response && isset($response[0])){
			$response = $response[0];
		}else{
			$response = null;
		}
		
		return $response;
		
	}
	
	/**
	 * Busca las subastas del producto indicado con las mejores ofertas
	 * @param unknown $idProduct
	 * @param unknown $startDateFrom
	 * @param unknown $startDateTo
	 * @return Ambigous <multitype:, boolean, multitype:object >
	 */
	public static function getByDateRangeWithBestBids($idProduct, $startDateFrom, $startDateTo){
		
		$status = \Models\AuctionStatus::findOne( array ("keyStatus" => \Models\AuctionStatus::FINALIZED) );
		
		$conection = new \App\Connection();
		$query = new \App\QueryBuilder();
		$table = self::TABLE;
		
		$query->select()->from($table);
		$query->innerJoin('idProduct', \Models\Product::TABLE, \Models\Product::PRIMARY_KEY);
		$query->innerJoin('idCurrency', \Models\Currency::TABLE, \Models\Currency::PRIMARY_KEY);
		$query->innerJoin('idUnitMeasure', \Models\UnitMeasure::TABLE, \Models\UnitMeasure::PRIMARY_KEY);
		$query->innerJoin('idAuction', \Models\AuctionBid::TABLE, 'idAuction');
		$query->innerJoin('idAuction', \Models\RequestedQuantity::TABLE, 'idAuction');
		$query->join(\Models\RequestedQuantity::TABLE, 'idEntity', \Models\Entity::TABLE, \Models\Entity::PRIMARY_KEY);
		$query->join(\Models\AuctionBid::TABLE, 'idUser', \Models\User::TABLE, \Models\User::PRIMARY_KEY);
		
		$query->where(" best = ? ", true);
		$query->where(" idStatus = ? ", $status->getId());
		$query->where(" idProduct = ? ", $idProduct);
		$query->where(" startDate >= ?  and startDate <= ? ", array($startDateFrom, $startDateTo));
		
		$query->append( \App\QueryBuilder::AND_OPERATOR." ".\Models\AuctionBid::TABLE.".idEntity = ".\Models\RequestedQuantity::TABLE.".idEntity ");
		$types = "iiiss";
		
		$p = $query->getParameters();
		$parameters = \App\Utils::makeArrayValuesToReferenced($p);
		
		$response = $conection->prepareQuery($query->getQueryString(), $parameters, $types, \Models\Auction::class);
		
		return $response;
		
	}
	
	public function jsonSerialize() {
	
		return get_object_vars($this);
	}
	
}
?>
