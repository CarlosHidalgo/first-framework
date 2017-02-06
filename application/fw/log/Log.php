<?php

namespace App;

/**
 * Logger del sistema
 * @author clopezh
 *
 */
class Log{
	
	const GENERAL_FILE_NAME = 'ReverseAuction';
	
	private function __construct(){}
	
	/**
	 * Escribe log info del sistema
	 * @param unknown $message
	 * @param unknown $className
	 */
	public static function info($message, $className){
		
		$hour = date("H:i:s");
		$day = date("d-m-Y");
		$write = 'INFO '.$className.' ['.$hour.'] '.$message."\n";
		
		error_log($write, 3, \App\Configuration::getStoragePath()."/logs/".$day."-".static::GENERAL_FILE_NAME.".log");
	}
	
}

?>