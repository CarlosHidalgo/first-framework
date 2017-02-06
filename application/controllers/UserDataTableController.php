<?php

namespace Controllers;
/**
 * Tabla de búsqueda de usuarios
 * @author clopezh
 *
 */
class UserDataTableController extends DataTableController{
	
	public function getRecordCount(){
		$total = 0;
		$response = $this->search(0,$this->parameters, true);
		
		if (isset($response[0])){
			$total = $response[0]->total;
		}
		return $total;
	}
	
	public function search($page, array $parameters, $isRecordCount = false){
		
		$model = $this->classModel;
		$size = $model::PER_PAGE;
		$this->setParameters($parameters);
		if (!$isRecordCount){
			$this->setPage($page);
			$offset = ($page -1) * $size;
		}else {
			$offset = null;
		}
		
		$users = \Models\User::find($parameters, $isRecordCount, $size, $offset);
		
		return $users;
	}
	
}