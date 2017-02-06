<?php
namespace Models;

/**
 * Modelo de producto
 * @author maque
 *
 */
class ProductUser extends Model{
	
	const TABLE = "productUser";
	const PER_PAGE = 5;
	const COLUMNS =	array(
			self::PRIMARY_KEY => array(self::PRIMARY_KEY, "i" ),
			"idProduct"	=>	array("idProduct","i"),
			"idUser"	=>	array("idUser","i")
			
	);
		
	private $idProduct;
	private $idUser;
	private $id;
	
	public function getId(){
		return $this->id;	
	}
		
	public function setId($id){
		$this->id = $id;
	}
	
	public function getIdProduct(){
		return $this->idProduct;
	}
	
	public function setIdProduct($idProduct){
		$this->idProduct = $idProduct;
	}
	
	public function getIdUser(){
		return $this->idUser;
	}
			
	public function setIdUser($idUser){
		$this->idUser = $idUser;
	}
	
	/**
	 * Busqueda de usuarios de tipo proveedor dado un producto
	 * @param $parameters = idProduct
	 */
	public function getProviderProductByProduct($parameters, $isRecordCount, $size = null, $offset = null) {
	
		//Columna tabla user
		$colsU = \Models\User::COLUMNS;
	
		//Columna tabla ProductUser
		$colsP = \Models\ProductUser::COLUMNS;
	
		$query =  new \App\QueryBuilder();
		if ($isRecordCount) {
			$query->select ( ' count(*) as total ' );
		} else {
			$query->select ();
		}
		/*$query->select();*/
	
		$query->from(\Models\User::TABLE)->innerJoin($colsU['idUser'][0], \Models\ProductUser::TABLE, $colsP['idUser'][0]);
		$query->where($colsP['idProduct'][0].' = ? ', $parameters);
		$con = new \App\Connection();
		if (!$isRecordCount){
			$query->setResultMaxSize($size);
	
			$query->setResultOffset($offset);
		}
	
		$info = $query->getParameters();
		$response = \App\Utils::makeArrayValuesToReferenced($info);
		$types = $colsP['idProduct'][1];
			
		$response = $con->prepareQuery($query->getQueryString(), $response, $types, \Models\ProductUser::Class);
	
		return $response;
	}
	
}

?>