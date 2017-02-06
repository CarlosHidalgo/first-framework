<?php

namespace Models;

class Permission extends Model{
	
	const TABLE = "permission";
	
	const COLUMNS =	array(
			self::PRIMARY_KEY => array(self::PRIMARY_KEY, "i" ),
			"keyPermission"	=> 	array("keyPermission", "s"),
			"name"		=>	array("name", "s"),
			"description"	=>	array("description", "s"),
			"idPermission"		=>	array("idPermission", "i")
	);
	
	
	// [[ SUBASTA ]]
	const AUC = 'AUC';
	const AUCSEARCH = 'AUCSEARCH';
	const AUCADD = 'AUCADD';
	const AUCEDIT = 'AUCEDIT';
	const AUCDEACTIVATE = 'AUCDEACTIVATE';
	const AUCDELET = 'AUCDELET';
	
	
	// [[ Usuario ]]
	const US = 'US';
	const USSEARCH = 'USSEARCH';
	const USADD = 'USADD';
	const USEDIT = 'USEDIT';
	const USDELETE = 'USDELETE';
	const USDEACTIVE = 'USDEACTIVE';
	
	// [[ Producto ]]
	const PRO = 'PRO';
	
	
	private $id;
	private $keyPermission;
	private $name;
	private $description;
	private $idPermision;
	
	public function getId(){
		return $this->id;
	}
	
	public function getKeyPermission(){
		return $this->keyPermission;
	}	
	
}
?>