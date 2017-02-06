// [[ View BID  ]]

$(document).on('click', '.panel-bid-heading span.icon_minim', function (e) {
    var $this = $(this);
    if (!$this.hasClass('panel-collapsed')) {
        $this.parents('.panel').find('.panel-body').slideUp();
        $this.addClass('panel-collapsed');
        $this.removeClass('glyphicon-minus').addClass('glyphicon-plus');
    } else {
        $this.parents('.panel').find('.panel-body').slideDown();
        $this.removeClass('panel-collapsed');
        $this.removeClass('glyphicon-plus').addClass('glyphicon-minus');
    }
});

$(document).on('focus', '.panel-bid-footer input.bid_input', function (e) {
    var $this = $(this);
    if ($('#minim_bid_window').hasClass('panel-collapsed')) {
        $this.parents('.panel').find('.panel-body').slideDown();
        $('#minim_bid_window').removeClass('panel-collapsed');
        $('#minim_bid_window').removeClass('glyphicon-plus').addClass('glyphicon-minus');
    }
});

//[[ View chat  ]]
$(document).on('click', '.panel-chat-heading span.icon_minim', function (e) {
    var $this = $(this);
    if (!$this.hasClass('panel-collapsed')) {
        $this.parents('.panel').find('.panel-body').slideUp();
        $this.addClass('panel-collapsed');
        $this.removeClass('glyphicon-minus').addClass('glyphicon-plus');
    } else {
        $this.parents('.panel').find('.panel-body').slideDown();
        $this.removeClass('panel-collapsed');
        $this.removeClass('glyphicon-plus').addClass('glyphicon-minus');
    }
});

$(document).on('focus', '.panel-chat-footer input.chat_input', function (e) {
    var $this = $(this);
    if ($('#minim_chat_window').hasClass('panel-collapsed')) {
        $this.parents('.panel').find('.panel-body').slideDown();
        $('#minim_chat_window').removeClass('panel-collapsed');
        $('#minim_chat_window').removeClass('glyphicon-plus').addClass('glyphicon-minus');
    }
});

// [[ WEB SOCKET ]]
$(document).ready(function(){
	
	var wsUri = "ws://"+uriWebSocket; 	
	var divBidBox = '#boxBid';
	var divBidUser = '#userBid-';
	var divChat = "#boxContainerMsg";
	var inputChat = '#message';
	var inputBid = '#bid';
	var btnChat = '#send-btn-chat';
	var btnBid = '#send-btn-bid';
	// BEST BIDS
	var divEntityBid = "#entityBid-";
	var divDeleteBidEntity = '#entityDeleteBid-';//boton
	var divDeleteBid= '.deleteBid';
	var divNameUserBestBid = '#nameUserBestBid-';
	
	const SYSTEM_TYPE = 'system';
	const USER_TYPE = 'user';
	const AUTH_TYPE = 'auth';
	const CHAT_KIND = 'chat';
	const BID_KIND = 'bid';
	const DISCONNECTION_KIND = 'disconnection';
	//ACTIONS
	const ACTION_ADD = 'add';
	const ACTION_DELETE = 'delete';
	
	var websocket = new WebSocket(wsUri); 
	
	websocket.onopen = function(ev) {
		
		if (ev.isTrusted){
			
			var route = uriChats; //enviar desde clase que invoca a este script
			var type = "POST";
			var nameForm = false;
			var data = { idAuction: myIdAuction, keyRole: myKeyRole}; //enviar desde clase que invoca a este script
			var targetDiv = null;
			var contentType = 'application/x-www-form-urlencoded';
			
			$.requestAjax(route, type, data, contentType , targetDiv,  nameForm, null, function(data, textStatus, jqXHR){
				
				var chats = jQuery.parseJSON(data);
				
				if (chats){
					for (var i = 0; i < chats.length; i++){
						
						appendMessage(divChat, USER_TYPE, CHAT_KIND, ACTION_ADD, chats[i]);
						
					}
				}
			});
			
			route = uriBids;
			data = { idAuction: myIdAuction, idUser: myIdUser}; //enviar desde clase que invoca a este script
			
			$.requestAjax(route, type, data, contentType , targetDiv,  nameForm, null, function(data, textStatus, jqXHR){
				
				var bids = jQuery.parseJSON(data);
				
				if (bids){
					for (var i = 0; i < bids.length; i++){
						
						appendMessage(divBidUser+bids[i].idUser, USER_TYPE, BID_KIND, ACTION_ADD, bids[i]);
						
					}
				}
			});
			
			// [[ Mensaje de mapeo en el servidor]]
			var myUser = $('#user').val();
			var myType = AUTH_TYPE;
			var myAction = ACTION_ADD;
			var myKind = '';
			var myData = {};
			
			if (myUser){
				
				myData.user = jQuery.parseJSON(myUser);
				
				send(websocket, myType, myKind ,myAction, myData);
			}
			
		}
	}
	
	/**--------------------------
	/ * 			CHAT
	/ *--------------------------*/
	$(document).on('submit', btnChat, function (event) { 
		
		event.preventDefault();
		
		var myMessage = $('#message').val(); 
		var myAuction = $('#idAuction').val();
		var myUser = $('#user').val();
		var myType = USER_TYPE;
		
		if(!myMessage){
			$.notificationMessage('ERROR','Debe ingresar mensaje', 'ERROR');
			return false;
		}
		
		if(!myUser){
			$.notificationMessage('ERROR','No existe identificador de usuario', 'ERROR');
			return false;
		}
		
		if(!myAuction){
			$.notificationMessage('ERROR','No existe identificador de subasta', 'ERROR');
			return false;
		}
		
		sendMessage(websocket,myType, myMessage, myAuction, myUser);
	});
	
	/**--------------------------
	 * 			OFERTA
	 *--------------------------*/
	$(document).on('submit', btnBid, function (event) { 
		
		event.preventDefault();
		
		var myEntity = $('#entity').val(); 
		var myAuction = $('#idAuction').val();
		var myUser = $('#user').val();
		var myBid = $('#bid').val();
		var myUnitM = $('#idUnit').val();
		
		var myType = USER_TYPE;
		var myKind = BID_KIND;
		var myAction = ACTION_ADD;
		
		if(!myBid){
			$.notificationMessage('ERROR','Debe ingresar Oferta', 'ERROR');
			return false;
		}
		
		if(!myUser){
			$.notificationMessage('ERROR','No existe identificador de usuario', 'ERROR');
			return false;
		}
		
		if(!myAuction){
			$.notificationMessage('ERROR','No existe identificador de subasta', 'ERROR');
			return false;
		}
		
		if(!myEntity){
			$.notificationMessage('ERROR','No existe identificador de entidad', 'ERROR');
			return false;
		}
		
		// ---------------------
		// [[ VALIDATE 15% ]]
		// --------------------
		var e = jQuery.parseJSON(myEntity);
		var entityName = e.nameEntity;
		
		
		var bestB = parseInt( $(divEntityBid+e.idEntity ).val());
		var bid = parseInt( myBid );
		var percent = 15;
		var limit = 1 - (15 / 100);
		
		if (bestB){
			
			if ( bid/bestB <= limit ){ 
			
				bootbox.confirm('Tu oferta de <b>$'+bid+'</b> representa un <b>'+percent+ "% menos</b> que la mejor oferta de: <b>$"+bestB+'</b> para  <b>'+entityName+'</b> ¿Deseas continuar?', function(result) {
			
					if (result){
						sendBid(websocket, myType, myBid, myAuction, myUser, myEntity, myUnitM);
						return;
					}
			
				});
			}else{
				sendBid(websocket, myType, myBid, myAuction, myUser, myEntity, myUnitM);
			}
			
		}else{
			
			sendBid(websocket, myType, myBid, myAuction, myUser, myEntity, myUnitM);
		}
				
		
	});
	
	/** -------------------------
	 * ELIMINAR OFERTAS
	 *----------------------------*/
	$(document).on('click',divDeleteBid, function (event) {
		
		var deleteBid = $(this).attr('value');
		
		if (!deleteBid){
			$.notificationMessage('ERROR','No existe oferta a eliminar', 'ERROR');
			return false;
		}
		
		var myType = USER_TYPE;
		var myKind = BID_KIND;
		var myAction = ACTION_DELETE;
		var myData = { auctionBid: jQuery.parseJSON(deleteBid) };
		
		send(websocket, myType, myKind , myAction, myData);
		
	});
	
	/** -----------------------------------------
	 * [[ Message received from server ]]
	 * --------------------------------------*/
	websocket.onmessage = function(ev) {

		var msg = JSON.parse(ev.data);
		
		var targetDiv = '';
		
		if (msg.kind === CHAT_KIND){
			targetDiv = divChat;
		}else if (msg.kind === BID_KIND){
			
			targetDiv = divBidUser+msg.data.user.idUser;
		}
		
		appendMessage(targetDiv,msg.type, msg.kind, msg.action, msg.data);

	};
	
	websocket.onerror	= function(ev){$(divChat).append("<span class='label label-danger'> Error - "+ev.data+"</span>");}; 
	
	websocket.onclose 	= function(ev){
		
		$(divChat).append("<span class='label label-info'> Conexión cerrada </span>");
		$(inputChat).prop( "disabled", true );
		$(inputBid).prop( "disabled", true );
		$(btnChat).prop( "disabled", true );
		$(btnBid).prop( "disabled", true );
			
	};
	
	// -----------------------
	// 		FUNCTIONS 
	// -----------------------
	
	/**
	 * Enviar un mensaje de chat del servidor
	 * @param websocket servidor
	 * @param myType tipo de mensaje {system | user}
	 * @param myMessage mensaje
	 * @param myAuction identificador de subasta
	 * @param myUser usuario
	 */
	function sendMessage(websocket,myType, myMessage, myAuction, myUser){
		
		var data = {};
		var myKind = CHAT_KIND;
		var myAction = ACTION_ADD;

		// Data
		if (myMessage){
			data.message = myMessage;
		}
		
		if (myAuction){
			data.idAuction = myAuction;
		}
		
		if (myUser){
			data.user = jQuery.parseJSON(myUser);
		}
		
		//convert and send data to server
		send(websocket, myType, myKind , myAction, data);
		$(inputChat).val(''); //reset text
		
	}
	
	/**
	 * Enviar una oferta al servidor
	 * @param websocket servidor
	 * @param myType tipo de mensaje {system | user}
	 * @param myBid oferta
	 * @param myAuction identificador de subasta
	 * @param myUser identificador de usuario
	 * @param myEntity entidad
	 * @param myUnitM unidad de medida
	 */
	function sendBid(websocket, myType, myBid, myAuction, myUser, myEntity, myUnitM){
		
		var data = {};
		var myKind =  BID_KIND;
		var myAction = ACTION_ADD;
		
		if (myBid){
			data.bid = myBid;
		}
		
		if (myAuction){
			data.idAuction = myAuction;
		}
		
		if (myUser){
			data.user = jQuery.parseJSON (myUser);
		}
		
		if (myEntity){
			data.entity = jQuery.parseJSON (myEntity);
		}
		
		if (myUnitM){
			data.idUnitMeasure = myUnitM;
		}
		
		//convert and send data to server
		send(websocket, myType, myKind ,myAction, data);
		$(inputBid).val(''); //reset text
	
	}

	/**
	 * Enviar un mensaje al servidor WS 
	 * @param websocket ws al que se enviará el mensaje
	 * @param myType tipo de mensaje {system | user}
	 * @param myKind clase de mensaje {chat, bid}
	 * @param myAction tipo de accion {ADD | DELETE}
	 * @param myData datos enviados en el mensaje
	 */
	function send(websocket, myType, myKind , myAction, myData){
		// myAuction, myUser, myName, myEntity, myUnitM
		
		var msg = {};
		
		if (myType){
			msg.type = myType;
		}
		
		if (myKind){
			msg.kind = myKind;
		}
		
		if (myData){
			msg.data = myData;
		}
		
		if (myAction){
			msg.action = myAction;
		}
		
		//convert and send data to server
		websocket.send(JSON.stringify(msg));
	}

	/**
	 * Agrega un mensaje del servidor a la vista actual
	 * @param targetDiv div sobre el que se agregarÃ¡ el data recivido
	 * @param myType tipo de mensaje recibido {system | user}
	 * @param myKind clase de mensaje {chat | bit }
	 * @param myAction (ADD | DELETE)
	 * @param data array con los datos enviados desde el server socket
	 */
	function appendMessage(targetDiv, myType, myKind, myAction, data){
		var text = '';
		
		switch(myType) {
		    case USER_TYPE:
		        
		    	var name = (data.name)  ? data.name : data.user.name;
		    	if (myKind == CHAT_KIND){
		    	 
		    		text = "<div class='row msg_container base_send'>" +
					"<div class='messages msg_sent col-lg-12 col-md-12 col-sm-12 col-xs-12'>" +
						"<small class='pull-right time'><i class='fa fa-clock-o'></i> " + data.time+ "</small>" +
						"<h5 class='media-heading' ><b>"+ name +"</b></h5><small class='col-lg-10 col-sm-10 col-md-10 col-xs-10'> "+data.message+"</small></div></div>";
		    		
		    		
		    	}else if (myKind == BID_KIND){
		    		
		    		if ( !$(targetDiv).length && 'idUser' in data && myAction == ACTION_ADD) { 
			    		
		    			$(divBidBox).append("<div class='row bids' id='"+targetDiv.replace("#", "")+"'>" +
		    					"<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'> <span class='glyphicon glyphicon-user'></span>"+name+" </div></div><br/>");
		    		}
		    				
		    		text += 
		    				"<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'> "+ data.nameEntity+" </div> " +
		    				"<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'> $ "+ data.bid+" </div> " +
		    				"<div class='col-lg-4 col-md-4 col-sm-12 col-xs-12'> "+ data.bidDate+"</div>";
		    		
		    		if(data.best){
		    			//actualizar si es la mejor oferta
		    			
		    			var nameU = '';
		    			
		    			if ('name' in data && 'idUser' in data ){
		    				nameU = data.name
		    			}else if('user' in data && 'idUser' in data.user){
		    				nameU = data.user.name
		    			}
		    			
		    			$(divNameUserBestBid+data.idEntity).html(nameU);
		    			
		    			var bestDiv = divEntityBid+data.idEntity;
		    			
		    			if (data.bid){
		    				$(bestDiv).val(data.bid);
		    			}else{
		    				$(bestDiv).val('No hay oferta');
		    			}
		    			
		    			if ( $(bestDiv).hasClass('flash') ){ //workarround flash
		    				
		    				$(bestDiv).addClass('flash2');
		    				
		    				$(bestDiv).removeClass('flash');
		    				
		    			}else{
		    				
		    				$(bestDiv).addClass('flash');
		    				
		    				$(bestDiv).removeClass('flash2');
		    				
		    			}
		    	
		    			//actualizar boton eliminar
		    			if (myAction == ACTION_DELETE){
		    				
			    			$(divDeleteBidEntity+data.idEntity).attr('value',null);
			    			
		    				return;
		    				
		    			}else{
		    				
			    			$(divDeleteBidEntity+data.idEntity).attr('value',JSON.stringify(data));
		    			}
		    			
		    		}
		    		
		    	}
		    	
		        break;
		    case SYSTEM_TYPE:
		    	
		    	if (myKind == DISCONNECTION_KIND){
		    		window.location.replace(uriFinishedAuction);
		    	}
		    	
		        break;
	    
		}
		
		$(targetDiv).append(text);
		// [[ SCROLLING ]]
		var mydiv = $(targetDiv);
		mydiv.scrollTop(mydiv.prop('scrollHeight'));
	}
	
	
	// -------------------------------------------
	// COUNTER
	// -------------------------------------------
		
	$('#countdown').countdown({
		timestamp	: endDateAuction,
		urlSystemDate : urlSystemDate,
	});
});