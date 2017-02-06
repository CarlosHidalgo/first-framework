<?php

namespace Models;

class Role extends Model implements \JsonSerializable{
	
	const TABLE = "role";
	const MANAGER = 'MANAGER';
	const AUDITOR = 'AUDITOR';
	const SUPPLIER = 'SUPPLIER';
	const ALL_ROLES = '*'; 
	
	const COLUMNS =	array(
			self::PRIMARY_KEY => array(self::PRIMARY_KEY, "i" ),
			"keyRole"	=> 	array("keyRole", "s"), 
			"name"		=>	array("name", "s"), 
			"description"	=>	array("description", "s")
	);
	
	public function getId(){
		return $this->id;
	}
	
	private $id;
	private $keyRole;
	private $name;
	private $description;
	
	private $permissions;
	
	public function getPermissions(){
		if ($this->permissions == null){
			$this->permissions = $this->hasToMany(\Models\Permission::class, 'rolepermission', 'idRole', 'idPermission');
			
			$refs = array();
			foreach($this->permissions as &$perm){
				$refs[$perm->getKeyPermission()] = &$perm;
			}
			
			$this->permissions = $refs;
		}
		return $this->permissions;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function setName($name){
		$this->name = $name;
	}
	
	public function getKeyRole(){
		return $this->keyRole;
	}
	
	public function setKeyRole($keyRole){
		$this->keyRole = $keyRole;
	}
	
	public function getDescription(){
		return $this->description;
	}
	
	public function setDescription($description){
		$this->description = $description;
	}
	
	public function jsonSerialize() {
	
		return get_object_vars($this);
	}
}
?>