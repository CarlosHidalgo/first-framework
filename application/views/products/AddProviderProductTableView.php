<?php
namespace Views\Products;

class AddProviderProductTableView extends \Views\Core\TableView {

	public function bodyContent($params = null){
		
		$rows = '';
		if (isset($params)){ 

			$product = isset($params->product) ? $params->product : new \Models\Product();
			$rows =<<<ROWS
				<tr>
					<th><i class="icon_calendar"></i>Nombre del proveedor</th>
					<th><i class="icon_cogs"></i> Operaciones</th>
				</tr>
ROWS;
				
		foreach ($params->productsUsers as $productsProviders){				
			
			$uriDeleteProviderProduct = \App\Route::getForNameRoute(\App\Route::GET, 'delete-provider-product', array($product->getKeyProduct(),$productsProviders->getId()));
			$rows .=<<<ROWS

			<tr>
				<td>{$productsProviders->name}</td>
				<td>
					<div class="btn-group">
						
						<a class="btn btn-danger glyphicon  glyphicon-remove" title="Remover" href="{$uriDeleteProviderProduct}"></a>
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
