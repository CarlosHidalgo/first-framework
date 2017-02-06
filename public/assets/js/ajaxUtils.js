(function($){
	
	/**
	 * Requiere http://bootboxjs.com/#dependencies
	 */
	$.setAjax = function(options){
		
		// defauls values
		var defaults = {
			containerResults : "#resultTable",
			nameForm: "#searchForm",
			pagination: ".pagination > li a",
			changeState: ".changeState",
			msgConfirmation: "msgConfirmation",
			actionExecute: '#actionexecute',
			loading : '#divLoading',
		};
		
		var settings = $.extend({}, defaults, options);
		
		/* --------------------------------------------------------------------------------------
		 * BÚSQUEDA para vistas tipo consulta
		 * Envía resultados a settings.containerResults
		 * --------------------------------------------------------------------------------------
		 */
		$(document).on('submit', settings.nameForm, function (event) {
			event.preventDefault();
			sendFormData(settings.nameForm, settings.containerResults, false);
		});
		
		
		/* ------------------------------------------------------------------------------------
		 * PAGINACIÓN generado por los DataTableController
		 * ------------------------------------------------------------------------------------
		 */
		$(document).on('click',settings.pagination, function (event) {
			event.preventDefault();
			
			var postData = new FormData($(settings.nameForm)[0]); //var postData = $(settings.nameForm).serializeArray();
			var redireccion = $(this).attr('href');
			var contentT =  false;
			
			$.requestAjax(redireccion, "POST", postData, contentT, settings.containerResults, false,settings.loading, false, false); 
			
		});		
		
		/* ---------------------------------------------------------------------------------
		 * CAMBIO DE ESTADO para usar una confirmación y ejecutar la acción correspondiente 
		 * ---------------------------------------------------------------------------------
		 */
		$(document).on('click', settings.changeState, function (event) {
			event.preventDefault();
			
			var redireccion = $(this).attr('href');
			var msg = $(this).attr(settings.msgConfirmation);
			var contentT =  false;
			
			if (msg){
				
				bootbox.confirm(msg, function(result) {
					if(result){
						$.requestAjax(redireccion, "GET", "",settings.containerResults ,  contentT, settings.nameForm, settings.loading);					
					}
				});
				
			}else{
				
				$.requestAjax(redireccion, "GET", "",contentT, settings.containerResults , settings.nameForm, settings.loading);
			}
			            
		});
		
		/* --------------------------------------------------------------------------------------
		 * EJECUTAR UN MÉTODO
		 * --------------------------------------------------------------------------------------
		 */
		$(document).on('submit', settings.actionExecute, function (event) {
			event.preventDefault();
			
			var postData =  new FormData($(settings.actionExecute)[0]); //$(idForm).serializeArray();
		    var route = $(settings.actionExecute).attr('action');
		    
		    $.requestAjax(route, "POST", postData, false, false, false, false);			
				
		});
		
		
		/* -----------------------------
		 * ENVIAR DATOS del formulario
		 * -----------------------------
		 */
		function sendFormData(idForm, targetDiv, nameForm){
		
			var postData =  new FormData($(idForm)[0]); //$(idForm).serializeArray();
		    var route = $(idForm).attr('action');
		    var contentT =  false;
		    
		    $.requestAjax(route, "POST", postData, contentT, targetDiv, nameForm, settings.loading);
		}
		
	};	//setAjax
	
	
	/* ------------------------------
	 * AJAX, peticiones
	 * -----------------------------
	 */
	$.requestAjax = function (route, type, data, contentT, targetDiv, nameForm, loading, successFunction, errorFunction){
		
		var sf = successFunction;
		var ef = errorFunction;
		var processData = false;
		var contentType = contentT;
		
		if (contentType) {
			processData = true;
		}
		
		if (!ef){
			
			ef = function(jqXHR, textStatus, errorThrown) {
				
				if (loading){
					$(loading).removeClass('show');
				}
				
				$.notificationMessage('ERROR', "Ha ocurrido un error inesperado!", 'ERROR');
	        	
				console.log(jqXHR);
	        	
	        };
	        
	        
		}
		
		if (!sf){
			
			if (loading){
				$(loading).addClass('show');
			}
			
			sf = function(data, textStatus, jqXHR) {
				
	        	if(/msgError/.test(data)){		        		
	        		var mensaje = eval('(' + data + ')');       		
	        		$.notificationMessage('ERROR', mensaje.msgError);
	        	}else{
	        		
	        		if(/msgSuccess/.test(data)){
	        			var mensaje = eval('(' + data + ')');  
		        		$.notificationMessage('¡INFORMACIÓN!',mensaje.msgSuccess, 'INFORMATION');
	        		}else if( /msgDefault/.test(data) ){
	        			var mensaje = eval('(' + data + ')');  
		        		$.notificationMessage('¡INFORMACIÓN!',mensaje.msgDefault, 'MESSAGE');
	        		}
	        		
	        		if (nameForm){
	        			$(nameForm).submit();
	        		}else if (targetDiv){
	        			$(targetDiv).html( data );
	        		}
	
	        	}
	        	
	        	if (loading){
					$(loading).removeClass('show');
				}
	        };
		}
		
		$.ajax({
	        url : route,
	        type: type,
	        data : data,
	        dataType: 'html',
	        contentType : contentType,
	        processData : processData,
	        success:sf,
	        error: ef
	    });
		
	}
	
	/* ----------------------------------------------------
	 * NOTIFICACIÓN - Mensaje por 3 segundos. 
	 * ----------------------------------------------------
	 */
	$.notificationMessage = function(title,message, type, callback){
		
		var label = '';
		var alert = '';
		var automatic = true;
		var myCallback
		
		if (!callback){
			myCallback = function(){};
		}else{
			myCallback = callback;
		}
		
		switch(type){
			case 'INFORMATION':
				label = 'primary';
				alert = 'success';
				break;
			case 'ERROR':
				label = 'danger';
				alert = 'danger';
				break;
			case 'WARNING':
				label = 'warning';
				alert = 'warning';
				break;
			case 'MESSAGE':
				label = 'default';
				alert = 'info';
				automatic =  false;
				break;
			default:
				label = 'default';
				alert = 'info';
				break;
		}
		
		var titleText = '<h4 class="label label-'+label+'">'+title+'</h4>';
		var messageText = '<p class="alert alert-'+alert+'">'+message+'</p>';
		
		var box = bootbox.dialog({
            title: titleText,
            message: messageText
           
        });
		
		if (automatic == true){
			setTimeout(function() {
				box.modal('hide');
			}, 3000);
			myCallback();
		}
		
	}
	
})(jQuery);
