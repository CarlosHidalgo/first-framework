<?php

namespace Models;

/**
 * Modelo de usuario
 * @author clopezh 
 *
 */
class User extends Model implements \JsonSerializable{
	
	const TABLE = "user";
	const PER_PAGE = 4;
	const PRIMARY_KEY = 'idUser'; // Autoincrement (i)
	const COLUMNS =	array(
			self::PRIMARY_KEY => array(self::PRIMARY_KEY, "i" ),
			"keyUser"	=> 	array("keyUser", "s"), 
			"email"		=>	array("email", "s"),
			"name"		=>	array("name", "s"), 
			"password"	=>	array("password", "s"), 
			"idRole"	=>	array("idRole","i"),
			"active"	=>	array("active","i")
	);
	
	private $idUser = null;
	private $keyUser;
	private $email;
	private $name;
	private $password;
	private $idRole;
	private $active;
	
	private $role;
	
	/**
	 * Edita la información de Role del usuario
	 * @param \Models\Role $role
	 */
	public function setRole(\Models\Role $role){
		$this->role = $role;
	}
	
	/**
	 * Retorna la información de Role del usuario
	 * @return \Models\Role
	 */
	public function getRole(){
		return $this->role;
	}
	
	/**
	 * Carga el ROL y los permisos correspondientes a dicho rol
	 */
	public function loadRoleWithPermissions(){
		$this->getRole()->getPermissions();
	}
	
	public function getId(){
		return $this->idUser;
	}
	
	public function setId($id){
		$this->idUser = $id;
	}
	
	public function setIdUser($idUser){
		$this->idUser = $idUser;
	}
	
	public function getKeyUser(){
		return $this->keyUser;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getIdRole(){
		return $this->idRole;
	}
	
	public function getPassword(){
		return $this->password;
	}
	
	public function setPassword($password){
		$this->password = $password;
	}
	
	public function setKeyUser($keyUser){
		$this->keyUser = $keyUser;
	}
	
	public function setName($name){
		$this->name = $name;
	}
	
	public function setIdRole($idRole){
		$this->idRole = $idRole;
	}
	
	public function getEmail(){
		return $this->email;
	}
	
	public function setEmail($email){
		$this->email = $email;
	}
	
	public function isActive(){
		return $this->active;
	}
	
	public function setActive($active){
		$this->active = $active;
	}
	
	public function getUserByProduct(){
	
	 $colsAuction = \Models\Auction::COLUMNS;
     $colsProduct = \Models\Product::COLUMNS;
       
       
     $query =  new \App\QueryBuilder();
     if($isRecordCount){
     	$query->select(' count(*) as total ');
     }
     else{
     	$query->select();
     }
     
     $query->from(\Models\Auction::TABLE)->innerJoin($colsAuction['idProduct'][0], \Models\Product::TABLE, $colsProduct['id'][0]);
       
     $types = "";
       
     if (isset($parameters['auctionKey']) && strcmp("", trim($parameters['auctionKey'])) != 0 ){
      	$query->where($colsAuction['auctionKey'][0].'  like  ? ', "%".strtoupper($parameters['auctionKey'])."%");
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
       
      $con = new \App\Connection();
      if (!$isRecordCount){
       $query->setResultMaxSize($size);
       
       $query->setResultOffset($offset);
      }

      $pppppp = $query->getParameters();

      $qq = \App\Utils::makeArrayValuesToReferenced($pppppp);
      
       
      $response = $con->prepareQuery($query->getQueryString(), $qq, $types);
      
      return $response;
	
	}
	/**
	* Obtiene todos los usuarios confirmados de una subasta dada
	**/
	public function getConfirmedProvidersByAuction($idAuction){
		$colsU = \Models\User::COLUMNS;
		$colsP = \Models\Participants::COLUMNS;
	
		$query =  new \App\QueryBuilder();
		$query->select();
		
		$query->from(\Models\User::TABLE)->innerJoin($colsU['idUser'][0], \Models\Participants::TABLE, $colsP['idUser'][0]);
		$query->where($colsP['idAuction'][0].' = ? ', $idAuction);
		$query->where($colsP['confirm'][0].' = ?', 1);
		$con = new \App\Connection();
		
		$info = $query->getParameters();
		$response = \App\Utils::makeArrayValuesToReferenced($info);
		$types = $colsP['idAuction'][1].$colsP['confirm'][1];
			
		$response = $con->prepareQuery($query->getQueryString(), $response, $types, get_class($this));
		return $response;
	}
	
	
	
	
	/**
	 * Determina si un usuario es manager
	 * usar \Controllers\UserController::loadRole()
	 */
	public function isManager(){
		if (isset($this->role)){
			return strcasecmp($this->role->getKeyRole(), \Models\Role::MANAGER) == 0;
		}
	}
	
	/**
	 * Determina si un usuario es proveedor
	 * usar \Controllers\UserController::loadRole()
	 */
	public function isProvider(){
		if (isset($this->role)){
			return strcasecmp($this->role->getKeyRole(), \Models\Role::SUPPLIER) == 0;
		}
	}
	
	/**
	 * Determina si un usario es auditor
	 * usar \Controllers\UserController::loadRole()
	 */
	public function isAuditor(){
		if (isset($this->role)){
			return strcasecmp($this->role->getKeyRole(), \Models\Role::AUDITOR) == 0;
		}
	}
	
	public function jsonSerialize() {
	
		return get_object_vars($this);
	}
	
}
?>