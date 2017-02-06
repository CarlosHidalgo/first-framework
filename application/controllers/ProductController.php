<?php

/**
 * Controlador para los productos del sistema
 * @author clopezh
 * @author wcampos
 * @author maque
 */
namespace Controllers;

class ProductController extends \Controllers\Controller{
	
	/**
	 * Despliega la vista de búsqueda de productos
	 */
	public function showView(){
		return \App\Utils::makeView(new \Views\Products\SearchProductView());
	}
	
	public function search($page){
		
		$parameters = array();
		
		if (isset($_POST['name']) && strcmp("", trim($_POST['name'])) != 0 ){
			$parameters['name'] =  array("%".$_POST['name']."%", 'LIKE');
		}
		
		if (isset($_POST['key']) && strcmp("", trim($_POST['key'])) != 0){
			$parameters['keyProduct'] =  array("%".$_POST['key']."%", 'LIKE');
		}
		
		$uri =  'products-search';
		
		$dtc = new \Controllers\ProductDataTableController($uri, \Models\Product::class);	
		$products = $dtc->search($page, $parameters);
		
		$paginator = $dtc->getPaginator();
		
		$total = $dtc->getTotal();
		
		$table = new \Views\Products\ProductTableView();
		
		return \App\Utils::makeView($table, array ('products' => $products , 'total' => $total, 'paginator' => $paginator ));
		
	
	}
	
	/**
	 * Vista crear editar producto
	 * @param unknown $keyProduct
	 */
	public function  createEditProductView($keyProduct){
		
		$product = \Models\Product::findOne(array('keyProduct' => $keyProduct));
		
		return \App\Utils::makeView(new \Views\Products\CreateEditProductView(), array(  'product' => $product));
		
	}
	
	/**
	 * Crea o guarda un producto
	 * @return string
	 */
	public function  createEditProduct(){
		
		$isValid = true;
		$errors = 'Los siguientes campos presentan errores: <ul>';
		$product = new \Models\Product();
		
		if (isset($_POST['id']) && strcmp("", trim($_POST['id'])) != 0 ){
			$product = \Models\Product::findById($_POST['id']);
		}		
		
		if (!isset($product)){
			$product = new \Models\Product();
		}
		
		if (isset($_POST['name']) && strcmp("", trim($_POST['name'])) != 0){
			$product->setName($_POST['name']);
			
		}else{
			$errors.= "<li> nombre producto </li>";
			$isValid = false;
		}
		
		if (isset($_POST['keyProduct']) && strcmp("", trim($_POST['keyProduct'])) != 0){
			$product->setKeyProduct($_POST['keyProduct']);
			
		}else{
			$errors.= "<li> clave producto </li>";
			$isValid = false;
		}
		
		if ($isValid){
				
			$response = $product->saveOrUpdate();
				
			if ( $response === true || $response == 1){
				$keyU = $product->getKeyProduct();
				$uriCancel = \App\Route::getForNameRoute(\App\Route::GET, 'products');
		
				$msg = "¡Producto $keyU creado/actualizado correctamente! <br><br/>
				<a class='btn btn-danger' href='$uriCancel' >Regresar a búsqueda</a> ";
				return json_encode(array('msgDefault'=> $msg));
			}else{
		
				return json_encode(array('msgError'=>$response));
			}
		}else{
			$errors.= '</ul>';
			return json_encode(array('msgError'=>$errors));
		}
	}
	
	/**
	 * Vista para agregar proveedores al producto indicado
	 * @param unknown $keyProduct identificador del producto
	 */
	public function addProviderProductView($keyProduct){

		$product = \Models\Product::findOne(array('keyProduct' => $keyProduct));
		
		$providersByRol= new \Controllers\UserController();
		$allProviders = $providersByRol->getProvidersByRol(\Models\Role::SUPPLIER);
		$providers = array(); //autocomplete view
		
		foreach ($allProviders as $p){
			
			$providers[] = array('label' => $p->getName(), 'id' => $p->getId());
			
		}
		
		$searchRoute = \App\Route::getForNameRoute(\App\Route::POST, 'search-provider-products', array(1));
		
		return \App\Utils::makeView(new \Views\Products\AddProviderProductView(), array('providers' => $providers, 'product' => $product, 'searchRoute' => $searchRoute));
	}
	
	
	/**
	 * Agrega proveedores al producto seleccionado.
	 * Método POST {idProvider}
	 * @return string
	 */
	public function saveProviderProduct() {
		
		$isValid = true;
		$productUser = new \Models\ProductUser();
		$errors = 'Los siguientes campos presentan errores: <ul>';
		
		if ( isset($_POST['idProvider']) &&strcmp("", $_POST['idProvider']) != 0 ){
			
			$productUser->setIdUser(trim($_POST['idProvider']));
		
		}else{
			
			$errors.= "<li> identificador de proveedor no puede ser nulo </li>   ";
			$isValid = false;
		}
						
		if (isset($_POST['idProduct']) && strcmp("", $_POST['idProduct']) != 0){
			
			$idProduct = trim($_POST['idProduct']);
			$productUser->setIdProduct($idProduct);
			
		}else{
			
			$errors.= "<li> identificador de producto no puede ser nulo </li>";
			$isValid = false;
		}
						
		if ($isValid){		
			
			$productUserController = new \Controllers\ProductUserController();
			$productU = $productUserController->getProductUser(array('idUser' => $productUser->getIdUser(), 'idProduct' => $productUser->getIdProduct()  ));			
			
			if ($productU){
				return json_encode(array('msgError'=> ' Proveedor ya existe para este producto' ));
			}
			
			$response = $productUser->saveOrUpdate();
			
			if ( $response === true || $response == 1){
				
				$this->searchProviderProduct(1);	
			
			} else{
				 
				return json_encode(array('msgError'=>$response));
			}
								
		}else{
			$errors.= '</ul>';
				
			return json_encode(array('msgError'=>$errors));
	
		}
				
	}
	
	/**
	 * Realiza una búsqueda de proveedores por producto. 
	 * @param unknown $page
	 * @return html tabla de proveedores
	 */
	public function searchProviderProduct($page) {
		
		$idProduct = isset($_POST['idProduct']) ? trim($_POST['idProduct']) : 0 ;
		$parameters = array('idProduct' => $idProduct);
		$uri = 'search-provider-products';
		
		$product = \Models\Product::findById($idProduct);
		$dtc = new \Controllers\AddProviderProductDataTableController($uri, \Models\ProductUser::class);
		
		$productsUsers = $dtc->search($page, $parameters);
		$paginator = $dtc->getPaginator();
		
		$total = $dtc->getTotal();
		$table = new \Views\Products\AddProviderProductTableView();
					
		return \App\Utils::makeView($table, array ('product' => $product, 'productsUsers' => $productsUsers , 'total' => $total, 'paginator' => $paginator ));
	} 
	
	/**
	 * Elimina el proveedor asignado de la clave de producto indicada
	 * @param unknown $idProviderUser identificador de provider user
	 */
	public function deleteProviderProduct($keyProduct, $idProviderUser) {
			
		$deleteProvidersProducts = \Models\ProductUser::findById($idProviderUser);
		
		$response = $deleteProvidersProducts->delete();
		
		$uri = \App\Route::getForNameRoute('GET', 'products-add-provider', array($keyProduct));
		
		if ($response){
			
			return \App\Utils::redirect($uri);
			
		}else{
			
			return \App\Utils::redirect($uri, array("msg_error" => "No se pudo eliminar proveedor"));
		}
		
	}
	
	/**
	 * Vista para agregar una ficha técnica a un producto
	 * @param unknown $keyProduct
	 */
	public function addDatasheetProductView($keyProduct){ 
					 
		$product = \Models\Product::findOne(array('keyProduct' => $keyProduct));
		
		return \App\Utils::makeView(new \Views\Products\AddDatasheetProductView(), array(  'product' => $product));	
		
	}
	
	/**
	 * Agrega una ficha técnica al producto indicado.
	 * @return string
	 */
	public  function  addDatasheetProduct(){
		
		$idProduct = isset($_POST['idProduct']) ? trim($_POST['idProduct']) : 0;
		$product = \Models\Product::findone(array('id' => $idProduct));
							
		if(isset($_POST['upload']) && $_FILES['fichero_usuario']['size'] > 0) {
				
			$fileName = $_FILES['fichero_usuario']['name'];
			$fileSize = $_FILES['fichero_usuario']['size'];
			$fileType = $_FILES['fichero_usuario']['type'];
			$fp      = fopen($_FILES['fichero_usuario']['tmp_name'], 'r');
			$content = fread($fp, filesize(($_FILES['fichero_usuario']['tmp_name'])));
			fclose($fp);
			
			$fileController = new \Controllers\FileController();
			$addFile = $fileController->addFile($content, $fileName, $fileSize, $fileType);
			
			if($addFile) {
			
				$idDatasheet = $addFile->getId();						
					
				$product->setIdDatasheet($idDatasheet);
									
				$response = $product->saveOrUpdate();
					
				if ($response === true || $response == 1) {
					 	
					$keyU = $product->getKeyProduct();
					$uriCancel = \App\Route::getForNameRoute(\App\Route::GET, 'products');
					 	
					$msg = "¡Producto $keyU creado/actualizado correctamente! <br><br/>
					 	<a class='btn btn-danger' href='$uriCancel' >Regresar a búsqueda</a> ";
					 	
					return json_encode(array('msgDefault'=> $msg));
					 	
				}
				
			 }else {
				$keyU = $product->getKeyProduct();
				$uriCancel = \App\Route::getForNameRoute(\App\Route::GET, 'products');
				$msg = "¡Producto $keyU No se guardo la ficha tecnica! <br><br/>
						<a class='btn btn-danger' href='$uriCancel' >Regresar a búsqueda</a> ";
					
				return json_encode(array('msgDefault'=> $msg));
			} 
				
		} 
	}					
	
	/**
	 * Permite descargar la ficha técnica del producto.
	 * @param unknown $nameFile
	 * @return string
	 */
	public function downloadDatasheet($idFile){
			
		$fileController = new \Controllers\FileController();
		$file = $fileController->getFile($idFile);		
		
		if ($file != NULL) {
			header ("Content-Disposition: attachment; filename=".$file->getName());
			header("Content-type: application/octet-stream");
	 		//header ("Content-Type: ".$datasheet->getType());
	 		print $file->getArchivo();
	 		
	 		exit;
		}else {
			
			//return \App\Utils::redirect($uri);
		}	
			
	}
	
	public function getAllProducts(){
	
		$allProducts = \Models\Product::all();
	
		return $allProducts;
	
	}
	
	/**
	 * @param $params Idproduct
	 * @return object(Models\Product)
	 */
	
	public function getProductById($params){
	
		$curProduct = \Models\Product::findById($params);
	
		return $curProduct;
	
	}
	
}	
?>
			
			
		
	
				
	
	
	
	
