<?php

namespace Socket;

/**
 * http://lucumr.pocoo.org/2012/9/24/websockets-101/
 * @author clopezh
 *
 */
abstract class WebSocketServer {
	
	protected $host;
	protected $port;
	protected $socket;
	protected $clients;
	protected $null = NULL;
	
	public function __construct($host, $port){
		$this->host = $host;
		$this->port = $port;
		
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		
		socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
		
		socket_bind($this->socket, 0, $this->port);
		
		socket_listen($this->socket);
		
		$this->clients[intval($this->socket)] = $this->socket; 
		
		$info = "\n PHP WebSocket Server running....";
		$info .= "\n Server Started : ".date('Y-m-d H:i:s');
		$info .= "\n Listening on   : ".$this->host." port: ".$this->port;
		$info .= "\n Master socket  : ".$this->socket."\n";
		$info .= "\n.... awaiting connections ...";
		$info .= "\n-------------------------------------------------------------\n";
		
		$this->logInfo($info);
		
	}
	
	/**
	 * Lógica del socket, proceso iterativo
	 */
	public abstract function proccess();
	
	/**
	 * Ejecutar el processo siempre
	 */
	public function start(){
		while (true) {
			$this->proccess();
		}
		// close the listening socket
		socket_close($this->socket);
	}
	
	/**
	 * Enviar mensaje a todos los clientes
	 * @param unknown $msg
	 * @return boolean
	 */
	protected function sendMessageToAll($msg){
		
		foreach($this->clients as $socketClient){
			@socket_write($socketClient,$msg,strlen($msg));
		}
		return true;
	}
	
	/**
	 * Enviar un mensaje al cliente indicado
	 * @param unknown $socketClient
	 * @param unknown $msg
	 * @return number
	 */
	protected function sendMessage($socketClient, $msg){
	
		return @socket_write($socketClient,$msg,strlen($msg));
		
	}
	
	/**
	 * Decodificar mensaje
	 * @param unknown $text
	 * @return Ambigous <string, boolean>
	 */
	protected function unmask($text) {
		
		$length = ord($text[1]) & 127;
		
		if($length == 126) {
			$masks = substr($text, 4, 4);
			$data = substr($text, 8);
		}
		else if($length == 127) {
			$masks = substr($text, 10, 4);
			$data = substr($text, 14);
		}else {
			$masks = substr($text, 2, 4);
			$data = substr($text, 6);
		}
		
		$text = "";
		for ($i = 0; $i < strlen($data); ++$i) {
			$text .= $data[$i] ^ $masks[$i%4];
		}
		return $text;
	}
	
	/**
	 * Codificar el mensaje para ser enviado.
	 * @param unknown $text
	 * @return string
	 */
	protected function mask($text){
		$b1 = 0x80 | (0x1 & 0x0f);
		
		$length = strlen($text);
	
		if($length <= 125){
			$header = pack('CC', $b1, $length);
		}
		else if($length > 125 && $length < 65536){
			$header = pack('CCn', $b1, 126, $length);
		}
		else if($length >= 65536) {
			$header = pack('CCNN', $b1, 127, $length);
		}
		
		return $header.$text;
	}
	
	/**
	 * Autentificar
	 * @param unknown $header
	 * @param unknown $client
	 * @param unknown $host
	 * @param unknown $port
	 */
	protected function performHandshaking($headers,$client, $host, $port){
	
		$secKey = $headers['Sec-WebSocket-Key'];
		
		$secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
		
		//hand shaking header
		$upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
				"Upgrade: websocket\r\n" .
				"Connection: Upgrade\r\n" .
				"WebSocket-Origin: $host\r\n" .
				"WebSocket-Location: ws://$host:$port/\r\n".
				"Sec-WebSocket-Accept:$secAccept\r\n\r\n";
		
		socket_write($client,$upgrade,strlen($upgrade));
	}
	
	/**
	 * Lee las cabeceras del websocket y las retorna en un array
	 * @param unknown $recevedHeader
	 * @return multitype:unknown
	 */
	protected function loadHeadersToArray($recevedHeader){
		$headers = array();
		$lines = preg_split("/\r\n/", $recevedHeader);
		
		foreach($lines as $line){
				
			$line = chop($line);
			if(preg_match('/\A(\S+): (.*)\z/', $line, $matches)){
				$headers[$matches[1]] = $matches[2];
			}
		}
		
		return $headers;
	}
	
	/**
	 * Realizá un echo 
	 * @param unknown $info
	 */
	protected function logInfo($info){
		echo $info;
	}
	
	
}

?>