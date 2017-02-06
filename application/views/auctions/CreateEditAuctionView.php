<?php

namespace Views\Auctions;

class CreateEditAuctionView extends \App\View{
	public function jScript($params = null){
	
		$products = json_encode(isset($params->products) ? $params->products : array());
		
		$js = <<<SCRIPT
	
		jQuery(function($){
			$('body').tooltip({
    			selector: '.glyphicon'
			});
			$.setAjax({});
		
			var products = {$products};
			
			$("#select_product").autocomplete({
				source: products,
				select: function( event, ui ) {
					
					$("#product").val(ui.item.id);
				}
			
			});
									
		});
		
		
		$(document).ready(function(){
    	
		    var inputList = [];
		    var addedEntity = [];
		    var flagHeader = 1;
		    
		    if(flagHeader == 1){
		   	inputList.push('<tr class="info"><th><span class="glyphicon glyphicon-menu-hamburger"></span> Entidad</th><th><span class="glyphicon glyphicon-tint"></span> Cantidad </th><th><span class="glyphicon glyphicon-cog"></span> operaciones </th></tr>');
		    	flagHeader++;
		    }
		    
		   $("#addButton").click(function(){
		   	   var containedId = false;
			   var index;
			   
				for (index = addedEntity.length - 1; index >= 0; --index) {
				    if(addedEntity[index] == $("#entity").val() ){
				    	
				    	containedId = true;
				    	$("#error").html('<label class="label-red">La entidad ya ha sido agregada</label>').fadeIn("slow").fadeOut("slow").fadeIn("slow");
				    }
				}
				
			   if($("#quantity").val()=="" || $("#quantity").val() <= 0 ){
			  	 $("#error").html('<label class="label-red">Debe de introducir un valor mayor a cero</label>').fadeIn("slow").fadeOut("slow").fadeIn("slow");
			   }else{
				    if(!containedId){
				    	addedEntity.push($("#entity").val());
				       inputList.push('<tr><th hidden><input type="text" value = "'+$("#entity").val()+'" name = "entityQuantityId[]" /></th>'+
				       '<th><input type="text" value = "'+$("#entity option:selected").text()+'" name = "entytiesNames[]" hidden/>'+$("#entity option:selected").text()+'</th>'+
				       '<th><input type="text" value = "'+$("#quantity").val()+'" name = "entytiesQuantity[]" hidden/>' + $("#quantity").val() + '</th>'+
				       '<th><a href="#" type="button" class="remove_field"><span class="glyphicon btn-glyphicon glyphicon-remove-sign  text-danger"> </span></a></th></tr>');
				       $("#quantitiesEntity").html(inputList);
				       $("#error").html('<label hidden>La entidad ya ha sido agregada</label>');
			       }
		       }
		   }); 
		   
		   $("#quantitiesEntity").on("click", "a.remove_field", function(e){
		   
		   	var entityRemoval = $(this).closest('tr').find('input').val();
		  	var indexToRemove;
		  	
		  	for (index = addedEntity.length - 1; index >= 0; --index) {
				    if(addedEntity[index] == entityRemoval ){
				    	indexToRemove = index;
				    }
				}
				addedEntity.splice(indexToRemove,1);
		  	//alert(jQuery.inArray("PAYCH",inputList));
		    inputList.splice($(this).closest('tr').index(),1);
		    
		     $("#quantitiesEntity").html(inputList);
		   });
		 
		   
		});
		
	
SCRIPT;
		return $js;
	}
	
	public function bodyContent($params = null){
		$action = \App\Route::getForNameRoute(\App\Route::POST, 'auction-create-edit');
		
		$urlCancel = \App\Route::getForNameRoute(\App\Route::GET, 'auctions');
		
		$auction = new \Models\Auction();
		
		
		if (isset($params)){
				
			if (isset($params->auction)){
				$auction = $params->auction;
			}
		}
		
		
		$body = <<<BODY
		
		
		<div class="panel panel-default">
  			<div class="panel-body">
				<div class="container">
					<div class="col-lg-12">
						<fieldset>
			    		<legend>Agregar Subasta</legend>
							<form class="form-horizontal" role="form" action='{$action}' method="post" id="searchForm" enctype="multipart/form-data">
							 <div class="form-group">
							    <label for="title" class="col-lg-3 control-label">Título*</label>
							    <div class="col-lg-6">							  	  
                                  <input class="form-control" id="title" name="title" type="text" value="{$auction->getAuctionName()}" required />
							    </div>
							  </div>	
							 
							 
							 
							 
							 						  
							 <div class="form-group">
							    <label for="auctionType" class="col-lg-3 control-label">Tipo*</label>
							    <div class="col-lg-6">
							      <select class = "form-control" name="auctionType" id="auctionType">
BODY;
									foreach($params->auctionType as $curAuctionType){
										$body .= <<<BODY
										<option value="{$curAuctionType->getId()}">{$curAuctionType->getType()}</option>
BODY;
									}
									$body .= <<<BODY
									</select>
							    </div>
							  </div>
							  
							   <div class="form-group">
							    <label for="select_product" class="col-lg-3 control-label">Producto*</label>
							    <div class="col-lg-6">
							      <input type="text" class="form-control" id="select_product" name="select_product" placeholder="Introduzca el nombre para buscar">
							    </div>
							
							   <div class="col-lg-6">
							    <input type="hidden" class="form-control" id="product" name="product" placeholder="Clave del producto" >
							    </div>
							  </div>
							  
							  
							   <div class="form-group">
							    <label for="currency" class="col-lg-3 control-label">UDM*</label>
							    <div class="col-lg-6">
							      <select class = "form-control" name="uom" id="uom">
BODY;
									foreach($params->uom as $curUOM){
										$body .= <<<BODY
										<option value="{$curUOM->getId()}">{$curUOM->getKeyUnitMeasure()} - {$curUOM->getNameUnitMeasure()}</option>
BODY;
									}
									$body .= <<<BODY
									</select>
							    </div>
							  </div>
							  
							  
							  <div class="form-group block-inline">
							    <label for="auctionType" class="col-lg-3 control-label">Entidad*</label>
							    <div class="col-lg-2 ">
							      <select class = "form-control" name="entity" id="entity">
BODY;
									foreach($params->entities as $curEntity){
										$body .= <<<BODY
										<option value="{$curEntity->getId()}">{$curEntity->getKey()}</option>
BODY;
									}
									$body .= <<<BODY
									</select><div></div>
							    </div>
							    
							    <div class="form-group pull-left">
								    <label for="quantity" class="col-lg-4 control-label">Cantidad*</label>
								    <div class="col-lg-8">
								      <input class="form-control col-lg-2 col-sm-2" id="quantity" name="quantity" type="number" value="{$auction->getQuantity()}" required/>
								    </div>
							  	</div>
							    <div class="pull-left">
							    <button  class ="btn icon-btn btn-success " type="button" id="addButton" ><span class="glyphicon btn-glyphicon glyphicon-plus img-circle text-info"> </span> Añadir</button>
							    </div>
							  </div>

							  
							 <div class="form-group">
							 
							    <div class="col-lg-offset-3 col-lg-6 col-sm-offset-12 col-sm-12">
							      <table class="table table-striped" id="quantitiesEntity" name ="quantitiesEntity">
							      	
							      </table>
							  	</div>	
							</div>
							
							 <div class="form-group">
							    	<div class="col-lg-offset-3 col-lg-6"  id="error">
									
								 </div>
							  </div>	
							
							  <div class="form-group">
							    <label for="openPrice" class="col-lg-3 control-label">Precio apertura</label>
							    <div class="col-lg-6">
							      <input class="form-control" id="openPrice" name="openPrice" type="number" value="{$auction->getId()}" />
							    </div>
							  </div>
							  
							  <div class="form-group">
							    <label for="currency" class="col-lg-3 control-label">Moneda*</label>
							    <div class="col-lg-6">
							      <select class = "form-control" name="currency" id="entity">
BODY;
									foreach($params->currency as $curCurrency){
										$body .= <<<BODY
										<option value="{$curCurrency->getId()}">{$curCurrency->getType()}</option>
BODY;
									}
									$body .= <<<BODY
									</select>
							    </div>
							  </div>
							  <div></div>
							 
							  
							  <div class="form-group">
							    <label for="startDate" class="col-lg-3 control-label">Fecha de inicio*</label>
							    	<div class="col-lg-6">
									<input class="form-control" type="datetime-local" name="startDate" required>
								 </div>
							  </div>
							  
							  <div class="form-group">
							    <label for="endDate" class="col-lg-3 control-label">Fecha de finalización*</label>
							    	<div class="col-lg-6">
									<input class="form-control" type="datetime-local" name="endDate" required>
								 </div>
							  </div>				  
							  
							  <div class="form-group">
							    <label for="confirmExpirationDate" class="col-lg-3 control-label">Fecha Máxima de confirmación*</label>
							    	<div class="col-lg-6">
									<input  class="form-control" type="datetime-local" name="confirmExpirationDate" required>
								 </div>
							  </div>
							  <div class="form-group">
							    <label for="productStartDeliveryDate" class="col-lg-3 control-label">Fecha inicial del plazo de entrega*</label>
							    <div class="col-lg-6">
							      <input class="form-control" id="productStartDeliveryDate" name="productStartDeliveryDate" type="datetime-local"  required />
							    </div>
							  </div>
							  
							   <div class="form-group">
							    <label for="productEndDeliveryDate" class="col-lg-3 control-label">Fecha  final del plazo de entrega*</label>
							    <div class="col-lg-6">
							      <input class="form-control" id="productEndDeliveryDate" name="productEndDeliveryDate" type="datetime-local"  required />
							    </div>
							  </div>
							  
							  <div class="form-group">
							    <label for="Bassis" class="col-lg-3 control-label">Base*</label>
							    <div class="col-lg-6"  class="form-control">
							      <input type="hidden" name="MAX_FILE_SIZE" value="2000000">
							      <input type="file" name="basis" id="basis"  required>
							    </div>
							  </div>
							  
							  <div class="form-group">
							    <div class="col-lg-offset-3 col-lg-10">
							      <button type="submit" name="upload" class="btn btn-success">Guardar</button> <a href="{$urlCancel}" class="btn btn-primary">Cancelar</a>
							    </div>
							  </div>
							  
							  
							</form>
						</fieldset>
			        </div>
				</div>
			</div>
		</div>
		
BODY;
		return $body;	
	}
} 

?>