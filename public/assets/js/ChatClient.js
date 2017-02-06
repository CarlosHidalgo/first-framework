// [[ View chat]]

$(document).on('click', '.panel-heading span.icon_minim', function (e) {
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
$(document).on('focus', '.panel-footer input.chat_input', function (e) {
    var $this = $(this);
    if ($('#minim_chat_window').hasClass('panel-collapsed')) {
        $this.parents('.panel').find('.panel-body').slideDown();
        $('#minim_chat_window').removeClass('panel-collapsed');
        $('#minim_chat_window').removeClass('glyphicon-plus').addClass('glyphicon-minus');
    }
});

// [[ WEB SOCKET ]]
$(document).ready(function(){
	
	var wsUri = "ws://reverseauction.proteinasyoleicos.com:8000/"; 	
 	
	var websocket = new WebSocket(wsUri); 
	
	websocket.onopen = function(ev) {
		
		var route = uriChats; //enviar desde clase que invoca a este script
		var type = "POST";
		var nameForm = false;
		var data = { idAuction: myIdAuction}; //enviar desde clase que invoca a este script
		var targetDiv = null;
		
		$.requestAjax(route, type, data, nameForm, targetDiv, function(data, textStatus, jqXHR){
			
			var chats = jQuery.parseJSON(data);
			
			for (var i = 0; i < chats.length; i++){
				
				appendMessage(chats[i].name, chats[i].message, 'usermsg', chats[i].time);
			}
		});
		
		if (ev.isTrusted){
			
			var myName = $('#name').val();
			var myMessage = '¡'+myName + ' conectado!';
			var myType = 'system';
			
			sendMessage(websocket,myName, myMessage, myType)
		}
	}

	$('#send-btn').click(function(){ 
		
		var myMessage = $('#message').val(); 
		var myName = $('#name').val();
		var myAuction = $('#idAuction').val();
		var myUser = $('#idUser').val();
		var myType = "usermsg";
		
		if(!myName){
			$.notificationMessage('ERROR','No existe nombre', 'ERROR');
			return false;
		}
		
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
		
		sendMessage(websocket, myName, myMessage, myType, myAuction, myUser);
	});
	
	// [[ Message received from server ]]
	websocket.onmessage = function(ev) {
		var msg = JSON.parse(ev.data);

		appendMessage(msg.name, msg.message, msg.type, msg.time);
	};
	
	websocket.onerror	= function(ev){$('#boxContainerMsg').append("<span class=\"label label-danger\"> Error - "+ev.data+"</span>");}; 
	websocket.onclose 	= function(ev){$('#boxContainerMsg').append("<span class=\"label label-info\"> Conexión cerrada </span>");}; 
});
	
	
/**
 * Enviar un mensaje al servidor
 * @param myName
 * @param myMessage
 * @param myType
 * @param myAuction
 * @param myUser
 */
function sendMessage(websocket, myName, myMessage, myType, myAuction, myUser){
	
	var msg = {};
	
	if (myName){
		msg.name = myName;
	}
	
	if (myMessage){
		msg.message = myMessage;
	}
	
	if (myType){
		msg.type = myType;
	}
	
	if (myAuction){
		msg.idAuction = myAuction;
	}
	
	if (myUser){
		msg.idUser = myUser;
	}
	
	//convert and send data to server
	websocket.send(JSON.stringify(msg));
	$('#message').val(''); //reset text
}

/**
 * Agrega un mensaje del servidor al cliente actual
 * @param myName
 * @param myMessage
 * @param myType
 * @param myTime
 */
function appendMessage(myName, myMessage, myType, myTime){
	
	var text = '';
	if(myType == 'usermsg') {
		
		text = "<div class=\"row msg_container base_send\">" +
						"<div class=\"messages msg_sent col-lg-12 col-sm-12 col-md-12 col-xl-12\">" +
							"<small class=\"pull-right time\"><i class=\"fa fa-clock-o\"></i> " + myTime+ "</small>" +
							"<h5 class=\"media-heading\" ><b>"+myName+"</b></h5><small class=\"col-lg-10 col-sm-10 col-md-10 col-xl-10\"> "+ myMessage+"</small></div></div>";
		
	}
	
	if(myType == 'system'){
		text = "<div class=\"system_msg\">"+myMessage+"</div>";
	}
	
	$("#boxContainerMsg").append(text);
	
	// [[ SCROLLING ]]
	var mydiv = $('#boxContainerMsg');
	mydiv.scrollTop(mydiv.prop('scrollHeight'));
}