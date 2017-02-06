<?php

namespace Controllers;

/**
 * Controlador de ofertas de usuario
 * @author clopezh
 *
 */
class AuctionBidController extends \Controllers\Controller{
	
	/**
	 * Busca todas las ofertas realizadas por un usuario en una subasta
	 * ordenado por usuario
	 * @param unknown $idAuction identificador de subasta
	 * @param unknown $idUser identificador de usuario
	 * @return objetos bidAuction
	 */
	public function findByAuctionAndUser(){
		$idAuction  = null; 
		$idUser = null;
		
		if (isset($_POST['idAuction']) && strcmp("", trim($_POST['idAuction'])) != 0 ){
			$idAuction = $_POST['idAuction'];
		}
		
		if (isset($_POST['idUser']) && strcmp("", trim($_POST['idUser'])) != 0 ){
			$idUser = $_POST['idUser'];
		}
		
		return $usersBids = $this->searchByAuctionAndUser($idAuction, $idUser);
	}
	
	
	/**
	 * Busca todas las ofertas realizadas por un usuario en una subasta
	 * ordenado por usuario
	 * @param unknown $idAuction identificador de subasta
	 * @param unknown $idUser identificador de usuario
	 * @param unknown $best indica si se búscara la mejor oferta (true), todas (null), las ofertas normales (false)
	 * @return \Models\objetos
	 */
	public function searchByAuctionAndUser($idAuction, $idUser, $best = null){
	
		return $usersBids = \Models\AuctionBid::findByAuctionWihtUser($idAuction , $idUser, $best);
	}

	/**
	 * Busca todos las ofertas  de una subasta
	 * @param unknown $idAuction identificador de subasta
	 * @param int $idEntity identificador de entidad para la oferta
	 * @return Ambigous <multitype:, boolean, multitype:object >
	 */
	
	public function getBidByAuction($idAuction, $idEntity = null){
	
		$bids = \Models\AuctionBid::findByAuction($idAuction, $idEntity);
		return $bids;
	}
	
	public function getBestBidByAuction($params){
	
		$bestBids = \Models\AuctionBid::findBestByAuction($params);
		
		return $bids;
	}
	
	
}

?>