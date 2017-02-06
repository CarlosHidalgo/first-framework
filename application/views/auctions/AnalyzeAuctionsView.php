<?php

namespace Views\Auctions;

class AnalyzeAuctionsView extends \App\View{

	public function ccsFiles(){

		$scripts = array('jquery.countdown.css');
		$css = $this->generateUriFileCSS($scripts);

		return $css;
	}

	public function jScriptFiles(){
		
		$scripts = array('jquery.countdown.js');
		$jss = $this->generateUriFileJS($scripts);
		
		return $jss;
	}
	
	public function jScript($params = null){
		
		if (isset($params)){
			
			$products = json_encode(isset($params->products) ? $params->products : array());
		
		}else{
			
			$products = json_encode(array());
			
		}
		
		$js = <<<SCRIPT
	
		jQuery(function($){
			$.setAjax({});
			var products = {$products};
			
			$("#select_product").autocomplete({
				source: products,
				select: function( event, ui ) {
					
					$("#product").val(ui.item.id);
				}
			
			});
	      				
	      	/**--------------------------
			/ * 			GRAPH
			/ *--------------------------*/
	      	$(document).on('submit', '#analyze-graph', function (event) { 
				
				event.preventDefault();
	      		
	      		var route = $(this).attr('action');
	      		var type = "POST";
	      		var data =  new FormData($('#analyze-graph')[0]); 
				var contentType = false;
	      		var targetDiv = null;
	      		var nameForm = false;
				var loading = '#divLoading';

				$(loading).addClass('show');
	      		
				$.requestAjax(route, type, data, contentType , targetDiv,  nameForm, null, function(response, textStatus, jqXHR){
					
					var r = JSON.parse(response);
					
					if (r.error){
						$('#resultTable').html( "<div class='alert alert-danger' role='alert'><span class='glyphicon glyphicon-exclamation-sign'>"+
							"</span><span class='sr-only'>Error: </span>"+ r.error +"</div>");
					}else{
						
		      			var dataT = new google.visualization.DataTable(JSON.stringify(r.data));
					    var totalSeries = r.options.series;
						var colors = [];
						
						$.each(r.options.colors, function (key, data) {
							colors.push(data);
					    });
						
						;
					    var options = {
					      	title : r.options.title,
					    	vAxis: {title: r.options.vAxis },
					      	hAxis: {title: r.options.hAxis},
					      	seriesType: 'bars',
					      	series: { totalSeries : {type: 'line'}}, 
					        colors: colors,
			      			legend: { position: 'right', alignment: 'start' , textStyle : {fontSize: 10 } },
			      			chartArea: {width: '70%'},
		      			
					    };
						
					    var chart = new google.visualization.ComboChart(document.getElementById('resultTable'));
					    chart.draw(dataT, options);
					}
					
					$(loading).removeClass('show');
				});
	      				
			});
	      			
		});
SCRIPT;
		return $js;
	}

	public function bodyContent($params = null) {

	$action = \App\Route::getForNameRoute(\App\Route::POST, 'auctions-analyze-bestBids');
	$uriCancel = \App\Route::getForNameRoute(\App\Route::GET, 'auctions');

	$body = <<<BODY
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="accordion">
			<div class="panel panel-default">
				<div class="panel-heading text-center" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
					<div><Strong>  <label ">Analizar subastas </label></Strong> </div>
					<div class="clearfix"></div>
				</div>
		
				<div id="collapseOne" class="panel-collapse collapse in">
					<div class="panel-body">
					
					<form method="POST" action ="{$action}" class="form" role="form" id="analyze-graph">
						<div class="row">
					
							<div class="form-group col-xs-12 col-sm-12 col-md-4 col-lg-4">
								<label for="select_product">Producto:</label>
							    
						      	<input type="text" class="form-control" id="select_product" name="select_product" placeholder="Introduzca el nombre para buscar" required>

						    	<input type="hidden" class="form-control" id="product" name="product" placeholder="Clave del producto" >
									
							</div>
							
							<div class="form-group col-xs-12 col-sm-12 col-md-4 col-lg-4">
								<label for="productKey">Inicio subasta desde:</label>
								<input class="form-control" type="date" name="startDateFrom" required step="1" step=1>
							</div>
							
							<div class="form-group col-xs-12 col-sm-12 col-md-4 col-lg-4">
								<label for="productKey">Hasta:</label>
								<input class="form-control" type="date" name="startDateTo" required step="1" step=1>
							
							</div>
					
						</div>
				
						<div class="row">
							<div class="form-group col-xs-12 col-sm-12 col-md-1 col-lg-1 pull-left">
								<a class="btn btn-danger btn-sm" type="button" href="{$uriCancel}"> <span class="glyphicon glyphicon-arrow-left"></span>&nbsp;Regresar</a>
							</div>
									
							<div class="form-group col-xs-12 col-sm-12 col-md-1 col-lg-1 pull-right">
								<button  type="submit" class="btn btn-sm btn-primary btn-block" > <span class="glyphicon glyphicon-signal" > </span> Analizar</button>
							</div>

						</div>
											
					</form>
											
					</div>
					<!-- panel body -->
				</div>
			</div>
		</div>
	</div>
	
	<div id="resultTable" style="min-height: 400px;"></div>		
	
BODY;
		return $body;
	
	}
}
	?>
