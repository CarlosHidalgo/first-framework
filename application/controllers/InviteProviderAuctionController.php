<?php

namespace Controllers;


class InviteProviderAuctionController extends \Controllers\Controller{
	
	public function showView($params){
		
		$auction = \Models\Auction::findById((int)$params);

		$idAuction =	$auction->getId();
		
		$auctionKey = $auction->getAuctionKey();
						
		$parametersProductUser = array();
		
		$parametersProductUser['idProduct'] = array($auction->getIdProduct(),'=');
		
		$prodUser = \Models\ProductUser::find($parametersProductUser);
		
		$parametersConfirmation = array();
		
		$parametersConfirmation['idAuction'] = array($auction->getId(),'=');
		
		$invitedUsers = \Models\participants::find($parametersConfirmation); 
					
		$userParams = array();
		
		$userParams['active'] = array(1,'=');
		$userParams['idRole'] = array(3,'=');
		
		$users = \Models\user::find($userParams); 
		$selectedUsers = array();
		
		$usersInProduct = array();
		 
		 foreach($users as $curUser){
		 	
		 	foreach ($prodUser as $curProdUser){
		 			if($curProdUser->getIdUser() == $curUser->getId()){
		 				
		 				if(sizeof($invitedUsers)== 0){
		 					$selectedUsers[$curUser->getId()] = $curUser;
		 				}
		 				else{
		 					$invitedFlag = 0;
			 				foreach($invitedUsers as $curInvited){
			 					if($curInvited ->getIdUser() == $curProdUser->getIdUser()){
			 						$invitedFlag = 1;
			 						
			 					}
			 				}
			 				if($invitedFlag == 0){
			 					$selectedUsers[$curUser->getId()] = $curUser;
			 				}
			 			}
		 			}
		 	}
		 }
		 
		return \App\Utils::makeView(new \Views\Auctions\InviteProvidersAuctionsView(),array('auction'=>$idAuction, 'users'=>$selectedUsers, 'auctionKey' => $auctionKey));
		
	}
	
	public function invite(){
		$isValid = true;
		$participants = null;
		$auction = \Models\Auction::findById($_POST['id']);
		$errors = 'Debe de seleccionar almenos un invitado ';		
		$utils = new \App\Utils();
		//busqueda de archivos (basisfile, datasheet)
		$files = array();
		$idBasisFile = ($auction->getIdFile());	
		$idProduct = ($auction->getIdProduct());
		$product =  \Models\Product::findById($idProduct);
		$idDatasheet = ($product->getIdDatasheet());
		$datasheet = \Models\File::findById($idDatasheet);		
		$basisFile = \Models\File::findById($idBasisFile);	
		
		$files = (array($basisFile,$datasheet ));
				
		$ac = new \Models\Auction();
		
		$auctionFind = \Models\Auction::findById($auction->getId());		
		$invite = array();
		$i=0;
		if(isset($_POST['providers'])){
				
			foreach($_POST['providers'] as $invitedUsers){
				$password = $utils::randomPassword();
				$encrypt = $utils::encryptPassword($password);
				
				$participants = new \Models\Participants();
				$participants->setIdAuction($_POST['id']);
				$participants->setIdUser($invitedUsers);
				$participants->setConfirm(0);
				$participants->setToken($encrypt);
				$urlConfirm = \App\Route::getForNameRoute(\App\Route::GET, 'confirm', array($auction->getAuctionKey()));
				$urlQuestion = \App\Route::getForNameRoute(\App\Route::GET, 'questions-auction', array($auction->getAuctionKey()));
				
				
				$response = $participants->saveOrUpdate();
				$users = \Models\User::findById($invitedUsers);
				$invite[] = $users;
				$i ++;					
				if ( $response === true || $response == 1){
					foreach ($invite as $acUser){
						
						$from_mail="";
						$from_name = "Proteinas y Oleicos";
						$subject = "Invitacion de la subasta ".$auction->getAuctionKey();
						$from_mail = "subastas@proteinol.com";						
						$mailto = $acUser->getEmail();					
						$message = "";
						$message .= "Estimado(a) Usuario: ".$acUser->getName().".\n";
						$message .= "Por medio del presente le notificamos que ha sido invitado a participar en una subasta de Proteinas y Oleicos \n\n";
						$message .= "Fecha y hora de inicio de la subasta: ".$auction->getStartDate()."\n\n";
						$message .= "Se notifica la ficha tecnica y  las bases de la subasta : ".$auction->getAuctionKey().".\n";
						$message .= "Para confirmar su asistencia ingrese en la siguiente ruta e ingrese su correo y la clave de confirmacion: \n";
						$message .= $urlConfirm."\n\n";
						$message .=	"Clave para confirmar su asistencia: ".$participants->getToken()."\n\n";						
						$message .= "Link del portal para realizar las preguntas sobre la subasta: ".$urlQuestion;
						
						
						$mail_header = "From: ".$from_name." <".$from_mail.">\r\n";
					} 
						// boundary
						$semi_rand = md5(time());
						$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
						
						// Common header:
						$mail_header .= "MIME-Version: 1.0\r\n";
						$mail_header .= "Content-Type: multipart/mixed; boundary=\"".$mime_boundary."\"\r\n\r\n";						
							
						// multipart boundary 
						$message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
						$message .= "--{$mime_boundary}\n";
							
						  for ($x=0;$x<count($files);$x++){
								
							$filename= $files[$x]->getName();
														
							$file_size = $files[$x]->getSize();
							$content = $files[$x]->getArchivo();
							
							$content = chunk_split(base64_encode($content));
							
							$message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$filename\"\n" .
							"Content-Disposition: attachment;\n" . " filename=\"$filename\"\n" .
							"Content-Transfer-Encoding: base64\n\n" . $content . "\n\n";
														
								if ($x == count($files)-1 ){
									$message .= "--{$mime_boundary}--";
								} else {
									$message .= "--{$mime_boundary}\n";
								}
							} 							
													
					 mail($mailto, $subject,$message, $mail_header);
				}	
			}
				
						if ( $response === true || $response == 1){							
						
							$uriCancel = \App\Route::getForNameRoute(\App\Route::GET, 'auctions');
						
							$msg = "¡Proveedores invitados a la subasta! <br><br/>
							<a class='btn btn-danger' href='$uriCancel' >Regresar a búsqueda</a> ";
							return json_encode(array('msgDefault'=> $msg));
						}else{
						
						return json_encode(array('msgError'=>$response));
						}
			
			}	else{
					return json_encode(array('msgError'=>$errors));
					}

	}		
	

}
?>
