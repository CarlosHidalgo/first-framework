<?php

namespace Views\Products;

class ProductTableView extends \Views\Core\TableView {
	
	public function bodyContent($params = null){
				
		$rows = '';
		if (isset($params)){
			
			$rows =<<<ROWS
		
							<tr>
								<th><i class="icon_calendar"></i> Clave</th>
                                 <th><i class="icon_profile"></i> Nombre </th>
         						<th><i class="icon_mail_alt"></i> Ficha Tecnica</th>
					 			<th><i class="icon_cogs"></i> Operaciones</th>
                            </tr>
ROWS;
			
			
			foreach ($params->products as $product){
					
					$uriEditProduct = \App\Route::getForNameRoute(\App\Route::GET, 'products-create-edit', array($product->getKeyProduct()));
					$urlProductUsers = 	  \App\Route::getForNameRoute(\App\Route::GET, 'products-add-provider', array($product->getKeyProduct()));
					$uriAddDataSheet = \App\Route::getForNameRoute(\App\Route::GET, 'products-add-dataSheet', array($product->getKeyProduct()));
					
					$rows .=<<<ROWS
							<tr>
								 <td>{$product->getKeyProduct()}</td>
                                 <td>{$product->getName()}</td>
                                 <td>{$product->getIdDatasheet()}</td>

                                 <td>
	                                  <div class="btn-group">
	
		                                <a class="btn btn-primary glyphicon glyphicon-pencil" title="Editar" 
		                                	href="{$uriEditProduct}"></a>	                                			
		                                <a class="btn btn-warning glyphicon glyphicon-user" title="Asignar Proveedores" 
		                                	href="{$urlProductUsers}"></a>
										<a class="btn btn-info glyphicon glyphicon-file" title="Asignar ficha tecnica" 
		                                	href="{$uriAddDataSheet}"></a>			
	  
	                                  </div>
                                  </td>
                             </tr>
ROWS;
			}
		}
		
		return $rows;
	}
	
}


?>