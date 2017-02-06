<?php

namespace Controllers;

/**
 * Controlador para las gráficas de las subastas
 * @author clopezh
 *
 */
class AnalyzeAuctionController extends Controller {
	
	
	/**
	 * Despliega la vista de analisis de subasta
	 */
	public function showAnalyzeAuctionView(){
		
		$pc = new \Controllers\ProductController();
		
		$allProducts = $pc->getAllProducts();
		$products = array();
		
		foreach($allProducts as $curProduct){
			$products[] = array('id' => $curProduct->getId(), 'label' => $curProduct->getName());
		}
		
		return \App\Utils::makeView(new \Views\Auctions\AnalyzeAuctionsView(), array('products' => $products));
	}
	
	/**
	 * Genéra una gráfica para el análisis de subastas por mejores ofertas y proveedores
	 * @return string
	 */
	public function analyzeBestBids(){
	
		$a = new \Controllers\AuctionController();
		
		$idProduct = null;
		$startDateFrom = null;
		$startDateTo = null;
		
		if (isset($_POST['product']) && strcmp("", trim($_POST['product'])) != 0 ){
			$idProduct =  trim($_POST['product']);
		}
		
		if (isset($_POST['startDateFrom']) && strcmp("", trim($_POST['startDateFrom'])) != 0 ){
			$startDateFrom =  trim($_POST['startDateFrom']);
		}
		
		if (isset($_POST['startDateTo']) && strcmp("", trim($_POST['startDateTo'])) != 0 ){
			$startDateTo =  trim($_POST['startDateTo']);
		}
		
		$bids = $a->getByDateRangeWithBestBids($idProduct, $startDateFrom, $startDateTo);
		
		$graph = $this->generateDataGoogleChart($bids);

		return ($graph);
	
	}
	
	/**
	 * Genera un string compatible con la gráfica "Combo Chart" de google
	 * https://developers.google.com/chart/interactive/docs/gallery/combochart
	 * https://developers.google.com/chart/interactive/docs/php_example
	 * @param unknown $results subastas con bids  ( moneda, entidad, usuario, producto, unidad de medida)
	 * @return string
	 */
	private function generateDataGoogleChart(array $results){
		$analyze = array();
		$bids = array();
		$productName = ''; 		// options
		$currencyName = '';		// options
		$series = array();		// options
		$entityColor = array();	// options
		
		if (isset($results)  && count($results) > 0) {
			// Create array auction + entity + weight
			foreach ($results as $result){
				$key = $result->getAuctionKey()." ".$result->keyEntity." - ".$result->quantity.' '.$result->keyUnitMeasure;
				$keyAnnotation = $result->getId()."-annotation-".$result->keyEntity;
				$bids[$key] = 0;
				$bids[$keyAnnotation] = '';
				
				if (!isset($entityColor[$result->getId()])){
					$entityColor[$result->getId()] = sprintf("#%06x",rand(0,16777215));
				}
				
				$series[$key] = $entityColor[$result->getId()];
				$currencyName = $result->currencyName;
				$productName = $result->productName;
			}
			
			// FILL array bids
			foreach ($results as $result){
					
				$key = $result->getAuctionKey()." ".$result->keyEntity." - ".$result->quantity.' '.$result->keyUnitMeasure;
				$keyAnnotation = $result->getId()."-annotation-".$result->keyEntity;
					
				if (!isset($analyze[$result->keyUser])){
			
					$analyze[$result->keyUser] = $bids;
			
				}
					
				$analyze[$result->keyUser][$key] = $result->bid;
				$analyze[$result->keyUser][$keyAnnotation] = sprintf("$%01.2f", $result->bid) ." ".$result->keyEntity;
			
			}
			
			// ----------------------------------------------
			// [[ convert to string combo chart google JSON]]
			// ----------------------------------------------
			
			$rows = array();
			$row = array();
			$types = array();
			$lastBid = array();
			$options = array( 'title' => 'Licitaciones de '.$productName , 'vAxis' => ' Precio ('.$currencyName.')' , 
					'hAxis' => ' Proveedores ', 'series' => count($series), 'colors' => $series);
			
			$cols = array(  array('label' => 'provider', 'type' => 'string'  )  );
			$annotation = array( 'role' => 'annotation', 'type' => 'string',  'p' => array('role' => 'annotation' )  );
				
			// Create Data ROWS
			foreach ($analyze as $key => $bid){
					
				$row[] = array( 'v' => $key);
			
				foreach ($bid as $data){
						
					if (is_numeric($data)){
						$types[] = 'number';
					}else{
						$types[] = 'string';
					}
					
					$row[] = array( 'v' => $data);
				}
			
				$lastBid = $bid;
				$rows[] = array('c' => $row);
				$row = array();
			
			}
				
			// Create headers 
			$i = 0;
			foreach ($lastBid as $index => $data){
			
				$val = array( 'label' => $index , 'type' => $types[$i] );
			
				if (strpos($index, 'annotation') !=  false){
					$val = $annotation;
				}
			
				$i++;
				$cols[] = $val;
				
			}
				
			$graph = array( 'options' => $options , 'data' => array('cols' => $cols , 'rows' => $rows));
		}else{
			$graph = array('error' => 'No existen subastas en el rango indicado');
		}
		
		return json_encode($graph);
	}
}
?>