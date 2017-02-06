<?php

namespace Controllers;

/**
 * Controlador de la relacion de Productos con Proveedores
 * @author maque
 *
 */
class ProductUserController extends \Controllers\Controller{

	/**
	 * Busca la relacion de un Producto-Usuario
	 * @param array (idUser,IdProduct)
	 * @return object \Models\ProducUser
	 */
	public function getProductUser($params){

		$productU = \Models\ProductUser::findOne($params);
		
		return $productU;
	}
	
	public function getProviderProductByProduct ($parameters,$isRecordCount, $size, $offset){
		$pu = new \Models\ProductUser();
		$productsUsers= $pu->getProviderProductByProduct($parameters,$isRecordCount, $size, $offset);
		return $productsUsers;
	}
}


?>