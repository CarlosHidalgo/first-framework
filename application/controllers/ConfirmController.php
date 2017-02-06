<?php

namespace Controllers;

use Models\User;
class ConfirmController extends \Controllers\Controller{
	
	public function showView($auctionKey){
		return \App\Utils::makeView(new \Views\Auctions\ConfirmView(), array( "auctionKey" => $auctionKey));
	}
	public function cancelConfirmationProviderView($idUser,$idAuction){
		
		return  \App\Utils::makeView(new \Views\Auctions\CancelConfirmationProviderViews(), array("idUser" => $idUser, "idAuction" => $idAuction ));
	}
	public function confirmParticipationAuction($role){
		
		$isValid = true;
		$errors = 'Los siguientes campos presentan errores: <ul>';
		$token="";
		if (isset($_POST['auctionKey']) && strcmp("", trim($_POST['auctionKey'])) != 0 ){
			$keyAution = $_POST['auctionKey'];
		
		}else{
			$errors.= "<li> Clave de la subasta </li>";
			$isValid = false;
		}
		
		
		if (isset($_POST['email']) && strcmp("", trim($_POST['email'])) != 0 ){
			$email = $_POST['email'];
			
		
		}else{
			$errors.= "<li> Correo electr&oacute;nico </li>";
			$isValid = false;
		}
		if($role == FALSE){
			if (isset($_POST['token']) && strcmp("", trim($_POST['token'])) != 0 ){
				$token = $_POST['token'];
				
			
			}else{
				$errors.= "<li> Clave de confirmacion </li>";
				$isValid = false;
			}
		}
		
		
		if ($isValid){
			 
			$user = \Models\User::findOne(array( "email" => $email));
			
			if ($user == NULL){
				$msg = "Usuario incorrecto";
				return json_encode(array('msgDefault'=> $msg));
			}else {
				
				$auction = \Models\Auction::findOne(array("auctionKey" => $keyAution));
				
				if($auction == NULL){
					$msg = "No existe la subasta";
					return  json_encode(array('msgDefault'=> $msg));
				}else {
					
					$currentDate = date('Y-m-d H:i:s');
					$expirationDate = $auction->getConfirmExpirationDate();
					
					if($expirationDate > $currentDate || $role == TRUE){
						$participants = \Models\Participants::findOne(array("idAuction" => $auction->getId(), "idUser" => $user->getId()));
						$uriBack = \App\Route::getForNameRoute(\App\Route::GET, 'information-auction', array($keyAution));
						
						
							
							if($participants == NULL){
								$msg = "No esta disponible la subasta";
								return json_encode(array('msgDefault'=> $msg));
							
							}else {
								
								if($participants->getConfirm() == FALSE){
									
									if((strcmp($participants->getToken(), $token) == 0) || $role == TRUE){
									
										
										if(isset($_POST['upload']) && $_FILES['fichero_usuario']['size'] > 0) {
												
											$fileName = $_FILES['fichero_usuario']['name'];
											$fileSize = $_FILES['fichero_usuario']['size'];
											$fileType = $_FILES['fichero_usuario']['type'];
											$fp      = fopen($_FILES['fichero_usuario']['tmp_name'], 'r');
											$content = fread($fp, filesize(($_FILES['fichero_usuario']['tmp_name'])));
											fclose($fp);
												
											$this->fileController = new \Controllers\FileController();
											$addFile = $this->fileController->addFile($content, $fileName, $fileSize, $fileType);
												
											if($addFile) {
										
												$idDatasheet = $addFile->getId();
										
												//$product->setIdDatasheet($idDatasheet);
												$participants->setIdDatasheet($idDatasheet);
												$participants->setConfirm(TRUE);
												$response = $participants->saveOrUpdate();
										
												if ($response === true || $response == 1) {
															
														$msg = "Gracias por confirmar su participacion a la subasta!!   <a class='btn btn-danger' href='$uriBack' > Regresar</a> ";
														return  json_encode(array('msgSuccess'=> $msg));
												
												}else{
										
													return json_encode(array('msgError'=>$response));
												}
													
											}
												
										}
									}else{
										$msg = "La clave de confirmacion es incorrecta";
										return json_encode(array('msgDefault'=> $msg));
									}
										
								}else{
									$msg = "Usted ya ha confirmado su participacion a la subasta     <a class='btn btn-danger' href='$uriBack' > Regresar</a>";
									return json_encode(array('msgDefault'=> $msg));
								}
							
							}
							
						
					}else {
						$msg = "La fecha límite de confirmación ya ha expirado";
						return json_encode(array('msgDefault'=> $msg));
					}
				
					
				}
				
				
			}
			
		}else{
			$errors.= '</ul>';
			return json_encode(array('msgError'=>$errors));
		}
		
	}
	
	public function confirmParticipation(){
		
		$isValid = true;
		$errors = 'Los siguientes campos presentan errores: <ul>';
		
		if (isset($_POST['idUser']) && strcmp("", trim($_POST['idUser'])) != 0 ){
			$idUser = $_POST['idUser'];
		
		}else{
			$errors.= "<li> Usuario </li>";
			$isValid = false;
		}
		if (isset($_POST['idAuction']) && strcmp("", trim($_POST['idAuction'])) != 0 ){
			$idAuction = $_POST['idAuction'];
		
		}else{
			$errors.= "<li> Subasta </li>";
			$isValid = false;
		}
		if (isset($_POST['reasonCancel']) && strcmp("", trim($_POST['reasonCancel'])) != 0 ){
			$reasonCancel = $_POST['reasonCancel'];
		
		}else{
			$errors.= "<li> Motivo de la cancelacion </li>";
			$isValid = false;
		}
		
		if ($isValid){
			$confirm = \Models\Participants::findOne(array('idUser' => $idUser, 'idAuction' => $idAuction));
			$auction = \Models\Auction::findById($idAuction);
			$uriBack = \App\Route::getForNameRoute(\App\Route::GET, 'information-auction', array($auction->getAuctionKey()));
				
			if($confirm->getConfirm() == FALSE){
				
					
					$msg = "Ya se ha rechazado la confirmacion del proveedor   <a class='btn btn-danger' href='$uriBack' > Regresar</a>";
					return  json_encode(array('msgSuccess'=> $msg));
				
			}else{
				$confirm->setConfirm(FALSE);
				$response = $confirm->saveOrUpdate();
				$user = \Models\User::findById($idUser);
				$subject = "Notificacion de confirmacion";
				$uniqueid= uniqid('np');
				$message = "";
				
				$message .= "\r\n\r\n--" . $uniqueid. "\r\n";
				$message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
				$message .= "Estimado(a) ".$user->getEmail()."\v";
				$message .= "Su confirmación para la participación a la subasta ha sido rechazada debido por lo siguiente: \v";
				$message .= $reasonCancel;
				$message .= "\r\n\r\n--" . $uniqueid. "--";
				
				$to = $user->getEmail();
				$headers = "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: multipart/alternative;boundary=" . $uniqueid. "\r\n";
				$headers .= "From: subastas@proteinol.com\r\n" ;
				mail($to,$subject,$message,$headers);
				if ( $response === true || $response == 1){	
					
						$msg = "Ha rechazado la confirmacion del proveedor   <a class='btn btn-danger' href='$uriBack' > Regresar</a> ";
						return  json_encode(array('msgSuccess'=> $msg));
						
				}else{
						
					return  json_encode(array('msgError'=>$response));
				}
				
			}
			
		}else{
			$errors.= '</ul>';
			return  json_encode(array('msgError'=>$errors));
		}
		
		
	}
	
}
?>