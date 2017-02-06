<?php

namespace Controllers;

class InformationAuctionController extends \Controllers\Controller{
	
	public function showView($auctionKey){
				
		$auctionController = new \Controllers\AuctionController();
		$productController = new \Controllers\ProductController();
		$auctionTypeController = new \Controllers\AuctionTypeController();
		
		$auction = $auctionController->getAuction(array("auctionKey" => $auctionKey));
		$requestQuantitiesControllers = new \Controllers\RequestedQuantityController();
		$product = $productController->getProductById($auction->getIdProduct());		
		$auctionType = $auctionTypeController->getAuctionTypeById($auction->getIdAuctionType());			
		$requestedquantity = $requestQuantitiesControllers->getRequestedQuantityByAuction($auction->getId());
		$unitMeasureController = new \Controllers\UnitMeasureController();
		$unitMeasure = $unitMeasureController->getUnitMeasureByAuction($auction->getIdUnitMeasure());
		
		return \App\Utils::makeView(new \Views\Auctions\InformationAuctionView(), array('auction' => $auction, 'product' => $product, 'auctionType' => $auctionType, 'quantity' => $requestedquantity, 'unitMeasure' => $unitMeasure));
	}
	
	public function searchProvidersUsers($page) {
		
		$parameters = array('idAuction' => trim($_POST['idAuction']));
		$uri = 'search-providers-users';
		$dtc = new \Controllers\ProviderUserDataTableController($uri, \Models\Auction::class);
		$providers = $dtc->search($page, $parameters);
		$paginator = $dtc->getPaginator();
		$total = $dtc->getTotal();
		$table = new \Views\Auctions\ProviderUserTableView();
			
		return \App\Utils::makeView($table, array ('providers' => $providers , 'total' => $total, 'paginator' => $paginator ));
	
	}
	
	
}

?>