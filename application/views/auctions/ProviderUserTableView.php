<?php

namespace Views\Auctions;

class ProviderUserTableView extends \Views\Core\TableView {

	public function bodyContent($params = null){
		
		$rows = '';
		$currentDate = date('Y-m-d H:i:s');
		if (isset($params)){

			$rows =<<<ROWS

							<tr class="info" >
								<th><span class="glyphicon glyphicon-menu-hamburger"></span> Clave</th>
                                 <th><span class="glyphicon glyphicon-user"></span> Nombre </th>
                                 <th><span class="glyphicon glyphicon-envelope"></span> Email</th>
         						 <th><span class="glyphicon glyphicon glyphicon-flag"></span> Confirmaci&oacute;n</th>	
                                 <th><span class="glyphicon glyphicon-cog"></span> Operaciones</th>
                            </tr>
ROWS;
				
			
			
			foreach ($params->providers as $provider){
					
				$action = \App\Route::getForNameRoute(\App\Route::GET, 'information-confirm', array($provider->idUser, $provider->idAuction));
				$auction = \Models\Auction::findById($provider->idAuction);	
				$urldownload = \App\Route::getForNameRoute(\App\Route::GET, 'download-datasheet', array($provider->idDatasheet));
				$auction = \Models\Auction::findById($provider->idAuction);
				$confir = \App\Route::getForNameRoute(\App\Route::GET, 'confirm', array($auction->getAuctionKey()));
				$cancel = \App\Route::getForNameRoute(\App\Route::GET, 'cancel-confirmation-provider', array($provider->idUser, $provider->idAuction));
				
				$rows .=<<<ROWS
							<tr>
								 <td>{$provider->keyUser}</td>
                                 <td>{$provider->name}</td>
                                 <td>{$provider->email}</td>
                                 <td><a {$this->condition($provider->confirm, '', 'hidden')}><span class="glyphicon glyphicon-ok" ></span></a><a {$this->condition($provider->confirm, 'hidden', '')}><span class="glyphicon glyphicon-remove" ></span></a> </td>
                                 <td>
                                  <div class="btn-group">


	                               
										
	                                <a class=" btn btn-success glyphicon glyphicon-thumbs-up {$this->condition($provider->confirm | $auction->getConfirmExpirationDate() > $currentDate, 'hidden', '')}"
	                                	href="{$confir}"
	                                	title="Confirmar" msgConfirmation="¿Desea confirmar asistencia del usuario {$provider->name}?"/>
									  
	                                <a class=" btn btn-danger glyphicon glyphicon-thumbs-down {$this->condition($provider->confirm, '', 'hidden')}" 
	                                	href="{$cancel}"
	                                	title="Rechazar" msgConfirmation="¿Desea rechazar la confirmacion del usuario {$provider->name}?"/>
	                             
	                                	
	                                <a class=" btn btn-info glyphicon glyphicon-file {$this->condition($provider->idDatasheet, '', 'hidden')} " title="Descargar"  
		                                	href="{$urldownload}"/>		
		                             			
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