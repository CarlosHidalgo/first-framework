<?php

/**
 * Controlador para las subastas del sistema
 * @author clopezh
 *
 */
namespace Controllers;

use App;
class AuctionController extends \Controllers\Controller{
	
	/**
	 * Despliega la vista de búsqueda de subastas
	 */
	public function showView(){
		/*busqueda de los posibles status que puede tener una subasta*/
		$aStatus= new \Controllers\AuctionStatusController();
		$allStatus= $aStatus->getAllStatus();		
		
		$status = array();
		
		foreach($allStatus as $curStatus){
			$status[$curStatus->getId()] = $curStatus;
		}
		
		$user = \Security\Auth::getCurrentUser();
		
		if ($user){
			$this->user = $user;
		}else {
			$this->user = new \Models\User();
		}
		
		if ( $user->isProvider() ){
			
			$idUser = $user->getId();
			
		}else{
			
			$idUser = null;
		}
		
		$currentAuction = $this->getCurrentAuction($idUser);
		$nextAuction = null;
		
		if (!isset($currentAuction)){
			$nextAuction = $this->getNextAuction($idUser);
		}
	
		$providersByRol= new \Controllers\UserController();
		$allProviders = $providersByRol->getProvidersByRol(\Models\Role::SUPPLIER);		
		$providers = array(); //autocomplete view
		
		foreach ($allProviders as $p){
			
			$providers[] = array('label' => $p->getName(), 'id' => $p->getId());
		
		}
		
		$searchRoute = \App\Route::getForNameRoute('POST', 'auctions-search', array(1));
		$urlSystemDate = \App\Route::getForNameRoute('GET', 'system-date');
		
		return \App\Utils::makeView(new \Views\Auctions\SearchAuctionsView(),array('status'=> $status, 'currentAuction' =>$currentAuction, 'nextAuction' => $nextAuction, 'searchRoute' => $searchRoute,'providers' => $providers ,'urlSystemDate' => $urlSystemDate));
		
	}
	
	/**
	 * Obtiene los chats de la subasta pasando el $id 
	 * si se indica una clave de ROL los chats serán wrapeados para no mostrar toda la información
	 * Método:GET
	 * @param string $idAuction 
	 * @param string $keyRole
	 * @return Ambigous <boolean, \Models\Ambigous>
	 */
	public function getChats($idAuction = null, $keyRole = null){
		
		$response = null; 
		
		if (isset($idAuction)){
			
			$response = \Models\Chat::findByAuction($idAuction);
		
		}
		
		if ( isset($response) && isset($keyRole)){
			
			if ( strcmp(\Models\Role::ALL_ROLES, $keyRole) != 0 && strcmp(\Models\Role::SUPPLIER, $keyRole) == 0 && strcmp(\Models\Role::AUDITOR, $keyRole) == 0
					&& strcmp(\Models\Role::MANAGER, $keyRole) == 0){
					
				throw new \InvalidArgumentException('clave de Rol invalida '.$keyRole);
			}
			
			foreach ($response as $chat){	
				
				$chat = $this->wrapHiddenUserInformation($chat);
				
				if (strcmp(\Models\Role::SUPPLIER, $keyRole) == 0 ){
					$chat->name = 'Proveedor';
				}			
			}
		}
		
		return $response;
	}
	
	/**
	 * Eliminar información de usuario en la busqueda de chats | bids
	 * @param unknown $obj
	 */
	public function wrapHiddenUserInformation($obj){
		
		unset($obj->password);
		unset($obj->keyUser);
		unset($obj->idRole);
		unset($obj->role);
		unset($obj->email);
		
		return $obj;
	}
	
	/**
	 * Obtiene los chats de la subasta pasando el $idAuction 
	 * si se indica una clave de ROL ($keyRol )los chats serán wrapeados para no mostrar toda la información
	 * Metodo: POST
	 */
	public function getChatsPost(){
		
		$idAuction = null;
		$keyRole = null;
		if (isset($_POST['idAuction']) && strcmp("", trim($_POST['idAuction'])) != 0){
				
			$idAuction =  $_POST['idAuction'];
				
		}
		
		if (isset($_POST['keyRole']) && strcmp("", trim($_POST['keyRole'])) != 0){
		
			$keyRole =  $_POST['keyRole'];
		
		}
		
		return $this->getChats($idAuction, $keyRole);
	}
	
	
	/**
	 * Hacer búsqueda de subastas por parámetros
	 * @param unknown $page
	 * @return boolean
	 */
	public function search($page){
	
		$parameters = array();
	
		$uri =  'auctions-search';
	
		if (isset( $_POST['auctionKey'] )){
			$parameters['auctionKey'] = $_POST['auctionKey'];
		}
		
		if (isset( $_POST['productKey'] )){
			$parameters['productKey'] = $_POST['productKey'];
		}
		
		if (isset($_POST['status'] )){
			$parameters['status'] = $_POST['status'];
		}
		
		if (isset($_POST['idProvider'] )){
			$parameters['idProvider'] = $_POST['idProvider'];
		}
		
		$dtc = new \Controllers\AuctionDataTableController($uri, \Models\Auction::class);
		$auctions = $dtc->search($page, $parameters);
		
		$paginator = $dtc->getPaginator();
	
		$total = $dtc->getTotal();
	
		$table = new \Views\Auctions\AuctionTableView();
	
		return \App\Utils::makeView($table, array ('auctions' => $auctions , 'total' => $total, 'paginator' => $paginator ));
	
	}
	
	/**
	 * Controla la edición o creación de subastas
	 * @param unknown $id
	 */
	public function  createEditAuctionView($id){
		$auction = \Models\Auction::findById($id);
		
		$currencyController= new \Controllers\CurrencyController();
		$allCurrency= $currencyController->getAllCurrency();		
		$currency = array();
	
		$auctionTypeController= new \Controllers\AuctionTypeController();
		$allActionTypes= $auctionTypeController->getAllActionTypes();	
		$auctionType = array();
	
		$productController= new \Controllers\ProductController();
		$allProducts= $productController->getAllProducts();
		$products = array();
	
		$entityController= new \Controllers\EntityController();
		$allEntities= $entityController->getAllEntitys();
		$entities = array();
	
		foreach($allProducts as $curProduct){
			$products[] = array('id' => $curProduct->getId(), 'label' => $curProduct->getName());
		}
	
		foreach($allCurrency as $curCurrency){
			$currency[$curCurrency->getId()] = $curCurrency;
		}
	
		foreach($allActionTypes as $curAuctionType){
			$auctionType[$curAuctionType->getId()] = $curAuctionType;
		}
	
		foreach($allEntities as $curEntity){
			$entities[$curEntity->getId()] = $curEntity;
		}
		
		$unitMeasureController = new \Controllers\UnitMeasureController();
		$allUOM = $unitMeasureController->getAllUnitMeasure();		
		$uom= array();
		foreach ($allUOM as $curUOM){
			$uom[$curUOM-> getId()] = $curUOM;
		}
			
		return \App\Utils::makeView(new \Views\Auctions\CreateEditAuctionView(), array( 'auction' => $auction, 'currency' => $currency,'auctionType' =>$auctionType,'products'=>$products, 'entities'=>$entities, 'uom'=>$uom  ));
	
	}
	
	
	/**
	 * Crear o editar Subasta
	 */
	public function createEditAuction(){
		
		$auction = null;
		$isValid = true;
		$errors = 'Los siguientes campos presentan errores: <ul>';
		$totalQuantity = 0;
		
		if(isset($_POST['entityQuantityId']) &&  isset($_POST['entytiesQuantity'])){	
						
			$arrayEntities = $_POST['entityQuantityId'];
			$arrayQuantities = $_POST['entytiesQuantity'];
			
			forEach ($arrayQuantities as $curQuantity){
				
				$totalQuantity += $curQuantity;
			}
	
		}else{
			$errors.= "<li> Debe de agregar almenos una entidad agregada </li>";
			$isValid = false;
		}
		
		if ((sizeof($arrayEntities)>0) && (sizeof($arrayEntities) == sizeof($arrayQuantities))){
			$countAuctionEntities = sizeof($arrayEntities)-1;
		}else{
			$errors.= "<li> Debe de agregar almenos una entidad agregada </li>";
			$isValid = false;
		}
		
		if (isset($_POST['id']) && strcmp("", $_POST['id']) != 0 ){
			$auction = \Models\Auction::findById($_POST['id']);
		}
	
		if (!isset($auction)){
			$auction = new \Models\Auction();
		}
		
		$auction->setRememberStart(false);
		$auction->setOpenPrice(trim($_POST['openPrice']));
	
		if (isset($_POST['title']) && strcmp("", $_POST['title']) != 0){
			$auction->setAuctionName(trim($_POST['title']));
		}else{
			$errors.= "<li> Debe de establecer un título a la subasta</li>";
			$isValid = false;
		}
	
		if (isset($_POST['product']) && strcmp("", $_POST['product']) != 0){
			$auction->setIdProduct(trim($_POST['product']));
		}else{
			$errors.= "<li> debe de seleccionar un producto</li>";
			$isValid = false;
		}
	
		if (isset($_POST['auctionType']) && strcmp("", $_POST['auctionType']) != 0){
			$auction->setIdAuctionType(trim($_POST['auctionType']));
		}else{
			$errors.= "<li> title </li>";
			$isValid = false;
		}
	
		if (isset($_POST['currency']) && strcmp("", $_POST['currency']) != 0 ){
			$auction->setIdCurrency(trim($_POST['currency']));
		}else{
			$errors.= "<li> nombre no debe ser nulo </li>";
			$isValid = false;
		}
		
		if (isset($_POST['uom']) && strcmp("", $_POST['uom']) != 0 ){
			$auction->setIdUnitMeasure(trim($_POST['uom']));
		}else{
			$errors.= "<li> Debe seleccionar una unidad de medida </li>";
			$isValid = false;
		}
		
		if (isset($_POST['startDate']) && strcmp("", $_POST['startDate']) != 0){
			$auction->setStartDate(trim($_POST['startDate']));
		}else{
			$errors.= "<li> Debe de seleccionar una fecha de inicio</li>";
			$isValid = false;
		}
	
		if (isset($_POST['endDate']) && strcmp("", $_POST['endDate']) != 0){
			$auction->setEndDate(trim($_POST['endDate']));
		}else{
			$errors.= "<li>Debe de seleccionar una fecha de finalización</li>";
			$isValid = false;
		}
	
		if (isset($_POST['confirmExpirationDate']) && strcmp("", $_POST['confirmExpirationDate']) != 0){
			$auction->setConfirmExpirationDate(trim($_POST['confirmExpirationDate']));
		}else{
			$errors.= "<li>	Debe de seleccionar una fecha de expiración para la confirmacion</li>";
			$isValid = false;
		}
			
		if (isset($_POST['productStartDeliveryDate']) && strcmp("", $_POST['productStartDeliveryDate']) != 0){
			$auction->setProductStartDeliveryDate(trim($_POST['productStartDeliveryDate']));
		}else{
			$errors.= "<li>	Debe de seleccionar una fecha de inicio de entrega</li>";
			$isValid = false;
		}
		
		if (isset($_POST['productEndDeliveryDate']) && strcmp("", $_POST['productEndDeliveryDate']) != 0){
			$auction->setProductEndDeliveryDate(trim($_POST['productEndDeliveryDate']));
		}else{
			$isValid = false;
		}
		
		//se agrega el documento de bases
		if(isset($_POST['upload']) && $_FILES['basis']['size'] > 0) {
			$fileName = $_FILES['basis']['name'];
			$fileSize = $_FILES['basis']['size'];
			$fileType = $_FILES['basis']['type'];
			$fp      = fopen($_FILES['basis']['tmp_name'], 'r');
			$content = fread($fp, filesize(($_FILES['basis']['tmp_name'])));
			fclose($fp);
			$fileController = new \Controllers\FileController();
			$addFile = $fileController->addFile($content, $fileName, $fileSize, $fileType);
			
			if($addFile) {
			
				$idBasisFile = $addFile->getId();
							
				$auction->setIdFile($idBasisFile);
				
			}
				
		}else{
			$errors.= "<li>	Debe de seleccionar un documento de bases</li>";
			$isValid = false;
		}
			
		//inicializa las subastas a estado no disponible
		$auction->setIdStatus(2);
	
		$curDate = date('Y-m-d H:i:s');
		if (date('Y-m-d H:i:s',strtotime($auction->getStartDate())) <= date('Y-m-d H:i:s',strtotime($curDate))){
			$errors.= "<li>	La fecha de inicio debe de ser Mayor a la fecha actual</li>";
			$isValid = false;
		}
	
		if(strtotime($auction->getEndDate()) <= strtotime($auction->getStartdate())){
			$errors .= "<li> La fecha de fin de subasta no puede ser menor que la fecha de inicio de subasta</li>";
			$isValid = false;
		}
		 
		if(strtotime($auction->getEndDate()) >= strtotime($auction->getProductStartDeliveryDate())){
			$errors .= "<li> El plazo de entrega no puede ser menor a la fecha de finalización de la subasta</li>";
			$isValid = false;
		}
		
		if(strtotime($auction->getProductStartDeliveryDate()) >= strtotime($auction->getProductEndDeliveryDate())){
			$errors .= "<li> El plazo de fin de entrega no puede ser menor a la fecha de inicio de entrega del producto</li>";
			
			$isValid = false;
		}
		
		$parameters = array();
		
		$sd = $auction->getStartDate();
		$ed = $auction->getEndDate();
		
		$parameters['idStatus'] = array(2,'<=');
		
		$auc = \Models\Auction::find($parameters);		
		
		foreach ($auc as $curAuc){
			$curAucStDate = $curAuc->getStartDate();
			$curAucEndDate = $curAuc->getEndDate();
			if ( (strtotime($sd) >= strtotime($curAucStDate) && strtotime($sd) <= strtotime($curAucEndDate)) || (strtotime($curAucStDate) >= strtotime($sd) && strtotime($curAucStDate) <= strtotime($ed)) ){
			
				$isValid = false;
				$errors .= "<li> Existe una subasta en estado disponible o no disponible en el rango de fechas dado</li>";
				break;
			}
			
		}
		
		$productController= new \Controllers\ProductController();
		$curProduct= $productController->getProductById($_POST['product']);
		
		//$curProduct = \Models\Product::findById($_POST['product']);
		$acKey = "LIC";
		$acKey .= date('YmdHis',strtotime($auction->getStartDate())); 
		$acKey .= $curProduct->getKeyProduct();
		
		$auction->setAuctionKey($acKey);
		$auction->setQuantity($totalQuantity);
		
		if ($isValid){
			
			$response = $auction->saveOrUpdate();
			
				for ($i=0; $i<=$countAuctionEntities ; $i++){
				
					$requestedQuantityController = null;					
					$requestedQuantityController= new \Controllers\RequestedQuantityController();
					$requestedQuantity= $requestedQuantityController->addRequestedQuantity($arrayEntities[$i],$auction->getId(),$arrayQuantities[$i]);
								
				} 
			if ( $response === true || $response == 1){
				
				$keyU = $auction->getAuctionKey();
				$uriCancel = \App\Route::getForNameRoute(\App\Route::GET, 'auctions');
	
				$msg = "¡Subasta $keyU creada correctamente! <br><br/>
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
	 * Vista para cancelar subastas
	 * @param unknown $auctionKey
	 */
	public function cancelAuctionView($auctionKey){
	
		$auction = \Models\Auction::findOne(array("auctionKey" => $auctionKey));
	
		\App\Utils::makeView(new \Views\Auctions\CancelAuctionView(), array(  'auction' => $auction));
	
	}
	
	/**
	 * Cancela una subasta
	 */
	public function  cancelAuction(){
		
		$isValid = true;
		$errors = 'Los siguientes campos presentan errores: <ul>';
		
		if (isset($_POST['idAuction']) && strcmp("", trim($_POST['idAuction'])) != 0 ){
			$idAuction =  trim($_POST['idAuction']);
		}else{
			$errors.= "<li> Identificador de subasta </li>";
			$isValid = false;
		}
		
		if (isset($_POST['reasonCancel']) && strcmp("", trim($_POST['reasonCancel'])) != 0 ){
			$reason = trim($_POST['reasonCancel']);
		}else{
			$errors.= "<li> Motivo de cancelación </li>";
			$isValid = false;
		}
		
		if ($isValid){
			
			$errors = 'No se pudo cancelar la subasta';
			$auctionStatusController= new \Controllers\AuctionStatusController();
			$status = $auctionStatusController->getStatus(array('keyStatus' => \Models\AuctionStatus::CANCELED));			
			$auction = \Models\Auction::findById($idAuction);
		
		
			$auction->setIdStatus($status->getId());
			
			$ac = new \Models\Auction();
			$auctionsProvidersUser= $ac->getProviderAuctionByAuction($idAuction);
			
			if ($auctionsProvidersUser && count($auctionsProvidersUser) > 0 ){
				
				$genConfigs = \App\Configuration::getGeneralConfigs(); 
				$enotification = $genConfigs['notificationEmail'];
				
				$to = $genConfigs['systemEmail'];
				$subject = "Cancelacion de la subasta ".$auction->getAuctionKey();
				$txt = "Se cancela la subasta por los siguientes motivos: \n\n\t".$reason;
				$headers = "From: $enotification" . "\r\n" ;
				$bcc = "Bcc: ";
				
				foreach ($auctionsProvidersUser as $acUser){
					
					$bcc .= $acUser->email.",";
				
				}
				
				$headers .= rtrim($bcc,',');
					
				$result = mail($to,$subject,$txt,$headers);
				
				if ($result){
					\App\Log::info('Se notifico la cancelación de la subasta: '.$auction->getAuctionKey()." notificando a $bcc", get_class($this));
				}
			}
				
			$response = $auction->saveOrUpdate();
			
			if ( $response === true || $response == 1){
				
				$uriCancel = \App\Route::getForNameRoute(\App\Route::GET, 'auctions');
					
				$msg = "¡Se ha cancelado la subasta ! <br><br/>
						<a class='btn btn-danger' href='$uriCancel' >Regresar a búsqueda</a> ";
			
				return json_encode(array('msgDefault'=> $msg));
					
			}else{
			
				$errors = $response;
				$isValid = false;
			}
			
		}
				
		if (!$isValid){
			
			return json_encode(array('msgError'=>$errors));
			
		}	
		
	
	}
	
	/**
	* Redirección a la vista de preguntas de la subasta
	*/
	public function showAnswerQuestionsView($auctionKey){

		$auction = \Models\Auction::findOne(array("auctionKey" => $auctionKey));
		\App\Utils::makeView(new \Views\Auctions\AnswerQuestionsView(), array('auction' => $auction));
	}
	
	/**
	* Redirección a la vista de ofertas de la subasta
	*/
	public function showHistoryBidView($auctionKey){
		
		$auction = \Models\Auction::findOne(array("auctionKey" => $auctionKey));
		
		$auctionBidController = new \Controllers\AuctionBidController();
		
		$auctionbid = $auctionBidController->searchByAuctionAndUser($auction-> getId(), null);		
		
		return \App\Utils::makeView(new \Views\Auctions\historyBidView(), array('auctionbid' => $auctionbid, 'auction' => $auction));
	}
	
	
	/**
	 * Comenzar una subasta 
	 * @param unknown $keyAuction clave de subasta.
	 */
	public function goAuction($keyAuction){
		
		$auction = \Models\Auction::findOne(array('auctionKey' => $keyAuction ));
		if (isset($auction)){
			
			$startDate = new \DateTime($auction->getStartDate());
			$endDate = new \DateTime($auction->getEndDate());
			$now = new \DateTime();
			 
			if ( $now < $startDate ){
				
				return json_encode(array('msgError'=> 'La subasta no puede comenzar: '.$auction->getStartDate()));
				
			}else if( $now > $endDate){
				
				return json_encode(array('msgError' => 'La fecha de la subasta ha finalizado: '.$auction->getEndDate()));
			}
			
			// [[ DATA AUCTION ]]
			$this->loadProduct($auction);
			$this->loadCurrency($auction);
			$this->loadUnitMeasure($auction);
			$this->loadRequestQuantities($auction);
			
			$bc = new \Controllers\AuctionBidController();
			$bestBidWhithUser = $bc->searchByAuctionAndUser($auction->getId(), null, true);
			
			$auction->setBestBids($bestBidWhithUser);
			
			$ids = array();
			foreach ($auction->getRequestQuantities() as $rq){
				$ids[] = $rq->getIdEntity();
			}
			
			$entityController = new \Controllers\EntityController();
			$allEntities  =  $entityController->getEntitysByAuction($ids);			
			
			$entities = array();
			
			$user = unserialize(\Security\Session::get(\Models\User::TABLE));
			$uc = new \Controllers\UserController();
			
			if (!$user){
				$user = new \Models\User();
			}
			
			foreach ($allEntities as $entity){
				
				$entities[$entity->getId()] = $entity;
			}
			
			$urlSystemDate = \App\Route::getForNameRoute('GET', 'system-date');
			
			return \App\Utils::makeView(new \Views\Auctions\GoReverseAuctionView(), array('auction' => $auction, 'entities' => $entities, 'urlSystemDate' => $urlSystemDate ));
		}else{
			return json_encode(array('msgError' => 'La clave de subasta incorrecta : '.$keyAuction ));
		}
	}
	

	
	
	/**
	 * Muestra la vista del hisotorial del chat
	 * @param unknown $idAuction
	 */
	public function  historyChat($idAuction){
		
		$chat = $this->getChats($idAuction);
		$auction = \Models\Auction::findById($idAuction);
		
		return \App\Utils::makeView(new \Views\Auctions\HistoryChatView(),array('chat' => $chat,"auction" => $auction));
	} 
	
	/**
	 * Realiza la descarga del historial del chat indicado
	 * @param unknown $idAuction
	 */
	public function downloadHistoryChat($idAuction){
		$chats = $this->getChats($idAuction);
		$auction = \Models\Auction::findById($idAuction);
		if ($auction != NULL || $chats != NULL){
			$content = $auction->getAuctionKey()."\n\r";
				
			foreach ($chats as $chat){
			
				$content .= $chat->name." -	".$chat->getTime()."\n".$chat->getMessage()."\n";
			
			}
			header ("Content-Disposition: attachment; filename=".$auction->getAuctionKey().".txt");
			header("Content-Type: application/octet-stream; ");
			header ("Content-Length: ".$auction->getAuctionKey());
			header ("Content-Transfer-Encoding: binary");
			print $content;
			
		}else{
			echo json_encode(array('msgError'=>"Descarga no disponible"));
		}
	
	}
	/**
	* función para la descarga del historial del chat
	* @param idAuction
	*/
	public function downloadHistoryBid($idAuction){
		$auction = \Models\Auction::findById($idAuction);
		$auctionbid = \Models\AuctionBid::findByAuctionWihtUser($idAuction);
		$content = "\n";
		foreach($auctionbid as $curBid){
			$content .= "El usuario ".$curBid->name." ofertó ".$curBid->getBid()." para la entidad ".$curBid->keyEntity;
			if ($curBid->isBest()){
			 $content .= "(Mejor oferta) ";
			}
			$content .=$curBid->getBidDate();
			$content .= "\n";
			
		}
		
		$filename = 'Ofertas de la subasta '.$auction->getAuctionKey().'.doc';
	
		!$handle = fopen($filename, 'w');
		fwrite($handle, $content);
		fclose($handle);
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Length: ". filesize("$filename").";");
		header("Content-Disposition: attachment; filename=$filename");
		header("Content-Type: application/octet-stream; ");
		header("Content-Transfer-Encoding: binary");
		readfile($filename);
		
	}
	
	

	/**
	* función para responder y notificar por correo las preguntas de los proveedores desde la vista AnswerQuestionsView
	*/
	public function saveAnswers(){	
	 $index=0;
	 $uriCancel = \App\Route::getForNameRoute(\App\Route::GET, 'auctions');
	
		foreach ($_POST['id'] as $id){
			
			$questionController = new \Controllers\QuestionsAuctionController();
			$questions = $questionController->getQuestionsByAuction($id);								
			$questions->setAnswer($_POST['answer'][$index]);
			
			$response = $questions->saveOrUpdate();
			$index = $index +1;
		} 
			if ( $response === true || $response == 1){			
				
				$parameters = array();
				$parameters['idAuction'] = $_POST['idAuction'];
					
				$parametersQuestions['sent'] = 0;
				$parametersQuestions['idAuction'] = $_POST['idAuction'];
				$auction =  \Models\Auction::findById($parameters['idAuction']);
				
				$userController = new \Controllers\UserController();
				$providers = $userController->getConfirmedProvidersByAuction($parameters);			
				$question	= $questionController->getQuestionsByAuctionToSend($parametersQuestions);								
				$subject = "Envio de Preguntas/respuestas de la subasta ".$auction->getAuctionKey();
					
				if (!empty($question)){
						foreach ($providers as $p){
							$message = "";
							$message .= "Estimado(a) ".$p->getEmail().".\n";
							$headers = "";
							$to = $p->getEmail();							
							$headers = "From: subastas@proteinol.com\r\n" ;							
							$headers = "MIME-Version: 1.0\r\n";
							$headers .= "Content-type: text/html;charset=utf-8\r\n\r\n";
							$message .= 'Estas son las preguntas que se realizaron de la subasta:</p>';
								
							foreach ($question as $q){						
								$q->setSent(1);
								$response = $q->saveOrUpdate();
								$questions = $q->getQuestions();
								$answer =	$q->getAnswer();
								$message.= '<h3><label>'.$questions.'</label></p></h3>';
								$message.= '<label >'.$answer.'</label></p>';
								
							} 
								
							mail($to,$subject,$message,$headers);
						
						
						}							
							$msg = "¡Se han respondido y notificado por correo las preguntas correctamente! <br><br/>
									<a class='btn btn-danger' href='$uriCancel' >Regresar a búsqueda</a> ";
						
							return json_encode(array('msgDefault'=> $msg));	
			}					
					$msg = "¡Todas las preguntas ya se han respondido! <br><br/>
					<a class='btn btn-danger' href='$uriCancel' >Regresar a búsqueda</a> ";
					
					return json_encode(array('msgDefault'=> $msg));
			}
 	}	
	/**
	 * Retorna la subasta que se esta ejecutando en este momento
	 * @param $idUser int identificador de usuario
	 * @return \Models\Ambigous
	 */
	public function getCurrentAuction($idUser = null){
		
		return \Models\Auction::getCurrentAuction($idUser);

	}
	
	/**
	 * Realizar una oferta sobre la subasta
	 * @param \Models\Auction $auction subasta sobre la que se realizará la oferta
	 * @param \Models\AuctionBid $auctionBid oferta
	 */
	public function makeBid(\Models\Auction $auction ,\Models\AuctionBid $auctionBid ){
		
		if ($auction->getId() != $auctionBid->getIdAuction() || $auctionBid->getBid() == null || $auctionBid->getBid() <= 0){
			return false;
		}
		
		$this->loadBids($auction);
		
		$updateBids = array(); //ofertas para actualizar
		$exist = false;
		$best = false;
		$auctionBid->setBest($best);
		
		foreach ($auction->getBids() as $bid){
			
			if ($bid->getIdEntity() == $auctionBid->getIdEntity() && $bid->isBest() ){
				
				$exist = true;
				
				if ($auctionBid->getBid() < $bid->getBid()){
					
					$best = true;
					$auctionBid->setBest($best);
					
					$bid->setBest(false);
					$updateBids[] = $bid;
						
				}else{
					$best = false;
					$auctionBid->setBest($best);
					
				}
				
				break;
			}// fin exist
			
		}
		
		if (!$exist){
			$auctionBid->setBest(true);
		}
		
		$response = $auctionBid->save();
		
		//Actualizar antiguas ofertas
		foreach($updateBids as $ub){
			
			$response = $ub->update();

		}
		
		if ($response){
			$response = $auctionBid;
		}
		
		return $response;
	}
	
	/**
	 * Actualiza las ofertas de la subasta indicada, búsca el mejor y los que no lo son.
	 * @param \Models\Auction $auction subasta sobre la que se realizará la oferta
	 * @param int $idEntity identificador de la entidad de las ofertas
	 */
	public function updateBids(\Models\Auction $auction, $idEntity = null ){
	
		$this->loadBids($auction, $idEntity);
		$response = null;
	
		$updateBids = array(); 	//ofertas para actualizar
		$bestBids = array();	//mejores ofertas
	
		if ( $auction->getBids() != null && count($auction->getBids()) > 0  ){
			
			foreach ($auction->getBids() as $bid){
				
				$entity = $bid->getIdEntity();
				if ( !isset( $bestBids[$entity] ) ){
					
					$bid->setBest(true);
					$bestBids[$entity] = $bid;
					
				}else{

					$best = $bestBids[$entity];
					
					if ( $bid->getBid() < $best->getBid()  ){

						$bid->setBest(true);
						$bestBids[$entity] = $bid;
						
						$best->setBest(false);
						$updateBids[] = $best;
						
					}else if( $bid->isBest() ){
						
						$bid->setBest(false);
						$updateBids[] = $bid;
					}
				}
			
			}

			foreach ($bestBids as $bid){
				$bid->update();
			}
			
			foreach ($bestBids as $bid){
				$bid->update();
			}
			
			if (isset($idEntity)){
				$response = $bestBids[$idEntity];
			}else{
				$response = $bestBids;
			}
			
		}
	
		return $response;		
	}
	
	/**
	 * Finaliza la sesión del usuario actual para la subasta indicada.
	 * @param unknown $keyAuction
	 */
	public function finishedUserAuction($keyAuction){
		
		$user = unserialize(\Security\Session::get(\Models\User::TABLE));
		
		$uc = new \Controllers\UserController();
		$bc = new \Controllers\AuctionBidController();
		
		$uc->loadRole($user);
		
		$auction = \Models\Auction::findOne(array('auctionKey' => $keyAuction));
		
		$bestBids = $bc->searchByAuctionAndUser($auction->getId(), null, true);
		
		$auction->setBestBids($bestBids);
		
		$this->loadCurrency($auction);
		
		$userBids = $bc->searchByAuctionAndUser($auction->getId(), $user->getId());

		$auction->setBids($userBids);
		
		if ($user->isProvider()){

			$user->setPassword(\App\Utils::randomPassword());
		
			$user->saveOrUpdate();
			
			$keyUser = $user->getKeyUser();
		
			\Security\Auth::logout();
		}
		
		
		return \App\Utils::makeView(new \Views\Auctions\AuctionFinishedView(), array('auction' => $auction));
	}
	
	/**
	 * Carga la información de las ofertas hechas  a la subasta
	 * @param int $idEntity identificador de entidad para la oferta
	 * @param \Models\Auction $auction
	 */
	public function loadBids(\Models\Auction $auction, $idEntity = null){				
		
		$auctionBidController = new \Controllers\AuctionBidController();
		$bids = $auctionBidController->getBidByAuction($auction->getId(), $idEntity);
		$auction->setBids($bids);
	}
	
	/**
	 * Carga información del producto de la subasta
	 * @param \Models\Auction $auction
	 */
	public function loadProduct(\Models\Auction $auction){
		
		$productController = new \Controllers\ProductController();
		$product = $productController->getProductById($auction->getIdProduct());
		$auction->setProduct($product);
		
	}
	
	/**
	 * Carga la información de las mejores ofertas de la subasta
	 * @param \Models\Auction $auction
	 */
	public function loadBestBids(\Models\Auction $auction){
		
		$auctionBidController = new \Controllers\AuctionBidController();
		$bestBids = $auctionBidController->getBestBidByAuction($auction->getId());		
		$auction->setBestBids($bestBids);
	}
	
	/**
	 * Carga la información de moneda de la subasta.
	 * @param \Models\Auction $auction
	 */
	public function loadCurrency(\Models\Auction $auction){
		
		$currencyController = new \Controllers\CurrencyController();
		$currency = $currencyController->getCurrencyByAuction($auction->getIdCurrency());		
		$auction->setCurrency($currency);
	}
	
	/**
	 * Carga información de las cantidades solicitadas de producto para la subasta
	 * @param \Models\Auction $auction
	 */
	public function loadRequestQuantities(\Models\Auction $auction){
				
		$parameters = array('idAuction' => $auction->getId() );
		$requestQuantitiesControllers = new \Controllers\RequestedQuantityController();
		$requestQuantities = $requestQuantitiesControllers->getRequestedQuantityByAuction($parameters);		
		$auction->setRequestQuantities($requestQuantities);
		
	}
	
	/**
	 * Carga información de la unidad de medida en la subasta
	 * @param \Models\Auction $auction
	 */
	public function loadUnitMeasure(\Models\Auction $auction){
		
		$unitMeasureController = new \Controllers\UnitMeasureController();
		$unitMeasure = $unitMeasureController->getUnitMeasureByAuction($auction->getIdUnitMeasure());		
		$auction->setUnitMeasure($unitMeasure);		
	}
	
	/**
	 * Retorna las subastas que deben notificarse antes del comienzo de la misma 15min antes
	 * @return Ambigous <multitype:, boolean, multitype:object >
	 */
	public function getWithoutStartNotification(){
		return \Models\Auction::getWithoutStartNotification();
	}
	
	/**
	 * Envia un correo para notificar que la subasta va a comenzar.
	 * @param \Models\Auction $auction
	 */
	public function sendStartNotification(\Models\Auction $auction){
		// procesar el envio de inicio de subasta
		// devolver true;
		
		$subject = "Recordatorio";
		
		$uniqueid= uniqid('np');
		
		$utils = new App\Utils(); 
		$auc = new \Models\Auction();
		$providers = $auc->getProviderConfirm($auction->getId());
		$urlLogin = 'http://subastapyo.ddns.net/login';
	
		
		foreach ($providers as $provider){
			$password = $utils::randomPassword();
			$encrypt = $utils::encryptPassword($password);
			$userController = new \Controllers\UserController();
			$user = $userController->findByidUser($provider->idUser);			
			$user->setPassword($encrypt);
			$response = $user->saveOrUpdate();
			if ($response){
				$message = "";
				
				$message .= "\r\n\r\n--" . $uniqueid. "\r\n";
				$message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
				$message .= "Estimado(a) ".$provider->email.".\n";
				$message .= "Se le recuerda que en unos breves minutos dar&aacute; inicio la subasta de Prote&iacute;nas y Oleicos.\n";
				
				$message .= "<p>Usuario: ".$provider->email."<p>";
				$message .= "<p>Contrase&ntilde;a: ".$password."<p>";				
				$message .= "Para iniciar sesion ingresa a: ".$urlLogin;
				$message .= "\r\n\r\n--" . $uniqueid. "--";
				
				$to = $provider->email;
				$headers = "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: multipart/alternative;boundary=" . $uniqueid. "\r\n";
				$headers .= "From: subastas@proteinol.com\r\n" ;
				
				
				if (mail($to,$subject,$message,$headers)){
					$response = true;
					
				}
			}
		} 
		
		return $response;
	}
	
	/**
	 * Retorna la siguiente subasta a realizarse
	 */
	public function getNextAuction($idUser = null){
	
		return \Models\Auction::getNextAuction($idUser);
	
	}
	
	/**
	 * @param $params auctionKey
	 * @return object(Models\Auction)
	 */
	public function getAuction($params){
		
		$auction = \Models\Auction::findOne($params);
		return $auction;
	}
	
	/**
	 * Busca las subastas del producto indicado con las mejores ofertas
	 * @param unknown $idProduct
	 * @param unknown $startDateFrom
	 * @param unknown $startDateTo
	 * @return Ambigous <multitype:, boolean, multitype:object >
	 */
	public function getByDateRangeWithBestBids($idProduct, $startDateFrom, $startDateTo){
		return \Models\Auction::getByDateRangeWithBestBids($idProduct, $startDateFrom, $startDateTo);
	}

}
?>
