<?php

namespace  Controllers;


class AuctionDataTableController extends DataTableController {
	
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
		$ac = new \Models\Auction();
		
		$auctions = $ac-> getByParameters($parameters,$isRecordCount, $size, $offset);
		
		return $auctions;
	}
	
}

?>
