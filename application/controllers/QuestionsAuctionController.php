<?php

namespace Controllers;

class QuestionsAuctionController extends \Controllers\Controller{
	public function showView($auctionKey){
		
		\App\Utils::makeView(new \Views\Auctions\QuestionsAuctionView(), array('auction' => $auctionKey));
		return true;
	}
	
	public function writeQuestions(){
		
		$isValid = true;
		$errors = 'Los siguientes campos presentan errores: <ul>';
		
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
		if (isset($_POST['message']) && strcmp("", trim($_POST['message'])) != 0 ){
			$message = $_POST['message'];
			
		}else{
			$errors.= "<li> Pregunta </li>";
			$isValid = false;
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
					return json_encode(array('msgDefault'=> $msg));
				}else {
						
						$participants = \Models\Participants::findOne(array("idAuction" => $auction->getId(), "idUser" => $user->getId()));

						if ($participants == NULL){
							
							$msg = "La subasta no esta disponible";	
							return json_encode(array('msgDefault'=> $msg));

						}else {
								
							if($participants->getConfirm() == TRUE){
								$currentDate = date('Y-m-d H:i:s');
								if($currentDate < $auction->getStartDate()){
									$questions = new \Models\Questions();
									$questions->setIdAuction($auction->getId());
									$questions->setQuestions($message);
									$questions->setSent(0);
									$response = $questions->saveOrUpdate();
										
									if ( $response === true || $response == 1){
											
										$msg = "La pregunta formulada ha sido enviada correctamente";
										return json_encode(array('msgDefault'=> $msg));
											
									}else{
											
										return json_encode(array('msgError'=>$response));
									}
								}else{
									$msg = "La fecha límite para la captura de preguntas ya ha expirado";
									return json_encode(array('msgDefault'=> $msg));
								}
							}else{
								$msg = "Debe haber confirmado para poder realizar sus preguntas";
								return json_encode(array('msgDefault'=> $msg));
							} 	
								
								
						}
		
				}
			}
				
		}else{
			$errors.= '</ul>';
			return json_encode(array('msgError'=>$errors));
		}
	}
	
	/**
	 * Devuelve object (Models\Questions)
	 * @param $params idQuestion
	 */
	public function getQuestionsByAuction($params){
				
		$questions = \Models\Questions::findById($params);
		
		return $questions;
	}
	
	/**
	 * Devuelve las preguntas que no se hayan enviado de una subasta (Sent = 0 & IdAuction = $_POST)
	 * @param $params idAuction & Sent = 0
	 */
	
	public function getQuestionsByAuctionToSend($params){
	
		$questionToSend = \Models\Questions::find($params);
	
		return $questionToSend;
	}
}

?>
