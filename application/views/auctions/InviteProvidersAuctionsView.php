<?php

namespace Views\Auctions;

class InviteProvidersAuctionsView extends \App\View{

public function jScript($params = null){
		$js = <<<SCRIPT
				
		jQuery(function($){			
			$('body').tooltip({
    			selector: '.glyphicon'
			});
			
			
			$('#select_all').change(function() {
			  var checkboxes = $(this).closest('form').find(':checkbox');
			    if($(this).is(':checked')) {
			        checkboxes.prop('checked', true);
			    } else {
			        checkboxes.prop('checked', false);
			    }
			});
			
			
			$('.invitedUser').on('change', function() {
			    $('#select_all').not(this).prop('checked', false);  
			});
							
			$.setAjax({});	
		});
		
SCRIPT;
		return $js;
	}
	
	public function bodyContent($params = null) {
	
		$action = \App\Route::getForNameRoute(\App\Route::POST, 'auctions-invite-user', array(1));
		$uriCancel = \App\Route::getForNameRoute(\App\Route::GET, 'auctions', array(0));
		
		$body = <<<BODY
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="panel panel-default">
		        	<div class="panel-heading text-center">
		        		<a class="btn btn-danger btn-sm pull-left" type="button" href="{$uriCancel}"> <span class="glyphicon glyphicon-arrow-left"></span>&nbsp;Regresar</a>
						<div><strong>Invitar Proveedores para la subasta - {$params->auctionKey} </strong></div>
						<div class="clearfix"></div>
					</div>
					<div class="panel-body">
						<form method="POST" action = "{$action}"class="form" role="form" id="searchForm">
						<input class="form-control" id="id" name="id" type="hidden" value="{$params->auction}" required />
						<div class="row">
BODY;
							if(!empty($params->users)){
							
							$body .= <<<BODY
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							    <div class="input-group">
							      <span class="input-group-addon">
							        <input type="checkbox" id="select_all" value = "select_all">
							      </span>
							      <label class="form-control name = "selectLabel">Seleccionar Todos</label>
							    </div>
							</div>
BODY;
							foreach($params->users as $curUser){
							$body .= <<<BODY
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							    <div class="input-group">
							      <span class="input-group-addon">
							        <input class = "invitedUser" type="checkbox" value = "{$curUser->getId()}" name = "providers[]">
							      </span>
							      <label class="form-control name = "providers[]">{$curUser->getName()}</label>
							    </div>
							</div>
BODY;
								}
							}
							else{
							$body .= <<<BODY
							<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
								<p>No se encontraron proveedores disponibles para realizar invitaciones</p>
							</div>
						
BODY;
							}
							$body .= <<<BODY
							</div>
BODY;
							if(!empty($params->users)){
							$body .= <<<BODY
							<div></p></div>	
							<div class="row">
								<div class="form-group">
							    <div class="col-lg-offset-5 col-lg-10">
							      <button type="submit" class="btn btn-success">Guardar</button> <a href="{$uriCancel}" class="btn btn-primary">Cancelar</a>
							    </div>
							</div>
							</div>
BODY;
						}
						$body .= <<<BODY
						</form>
					<!-- panel body -->
				</div>
			</div>
		</div>
		
				
BODY;
		return $body;
	
	}}
?>