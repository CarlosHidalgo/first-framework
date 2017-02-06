<?php
/**
 * Configurations
 * @author clopezh
 */

namespace App;

class Configuration{
	
	private static $paths; 
	private static $generalConfigs;
	private static $dataBaseConfigs;
	private static $dnConfigs;
	
	public static function init(){
		self::loadPaths();
		self::loadRoutes();
		date_default_timezone_set(self::getGeneralConfigs()['timezone']);
		session_save_path(self::getStoragePath().'/sessions');
	}
	
	/**
	 * Carga el archivo de configuración de paths
	 */
	private static function loadPaths(){
		if (!isset(self::$paths)){
			
			$p = realpath(__DIR__.'/../config/paths.php');
			
			self::$paths = include_once $p;
			
			foreach (self::$paths as $index => $path){
				
				self::$paths[$index] = realpath($path);
		
			}
		}
	}
	
	/**
	 * Cargar las rutas para respuesta de solictudes
	 */
	private static function loadRoutes(){
		return include_once self::$paths['application'].'/fw/routing/Routes.php';
	}
	
	/**
	 * Retorna configuraciones generales de la aplicaci�n
	 */
	public static function getGeneralConfigs(){
		if (!isset(self::$generalConfigs)){
			self::$generalConfigs = include_once self::$paths['application'].'/config/app_config.php';'';
		}
		return self::$generalConfigs;
	}
	
	/**
	 * Retorna la configuración establecida por el usaurio para la base de datos.
	 */
	public static function getConfigDataBase(){
		if (!isset(self::$dataBaseConfigs)){
			self::$dataBaseConfigs = include_once self::$paths['application'].'/config/database.php';
		}
		return self::$dataBaseConfigs;
	}
	
	/**
	 * Retorna la configuración del Distinguished Name para los certificados.
	 */
	public static function getConfigDN(){
		if (!isset(self::$dnConfigs)){
			self::$dnConfigs = include_once self::$paths['application'].'/config/dn.php';
		}
		return self::$dnConfigs;
	}
	
	/**
	 * Directorio sobre el que se ubican los controllers, models, etc.
	 */
	public static function getApplicationDir(){
		self::loadPaths();
		return realpath(self::$paths['application']);	
	}
	
	/**
	 * Directorio sobre el que se despliegan archivos públicos
	 * dado la configuración de dominio del sistema.
	 * @return string
	 */
	public static function getPublicDir(){
		self::loadPaths();
		return realpath(self::$paths['public']);
	}
	
	/**
	 * Retona el directorio raíz de archivos de la aplicación.
	 * @return string
	 */
	public static function getRootPath(){
		self::loadPaths();
		return realpath(self::$paths['base']);
	}
	
	/**
	 * Retorna el directorio de almacenamiento de la aplicación
	 * @return string
	 */
	public static function getStoragePath(){
		self::loadPaths();
		return realpath(self::$paths['storage']);
	}
	
	/**
	 * Genera el CRT y las claves públic y privada para el sistema en '\application\config\key'
	 * Ejecute esta función solo para generar claves nuevas.
	 * Tenga en cuenta que estas claves son usadas para la autentificación JWT
	 * Tienen una duración por defecto de 1ño.
	 */
	public static function generateSSLCertAndKeys(){
		$dn = self::getConfigDN();
		$genConfigs = self::getGeneralConfigs();
		
		$Configs = array(
				'digest_alg' => 'sha1',
				'x509_extensions' => 'v3_ca',
				'req_extensions' => 'v3_req',
				'private_key_bits' => 2048,
				'private_key_type' => OPENSSL_KEYTYPE_RSA,
				'encrypt_key' => true,
				'encrypt_key_cipher' => OPENSSL_CIPHER_3DES
		);
		
		$privkey = openssl_pkey_new($Configs);
		// Generar una petici�n de firma de certificado
		$csr = openssl_csr_new($dn, $privkey);
		// Certificado autofirmado que es v�lido por 365 d�as
		$sscert = openssl_csr_sign($csr, null, $privkey, 365);
		
		//[[ Certificate Signing Request]]
		openssl_csr_export_to_file($csr, \App\Configuration::getRootPath().'\application\config\key\pyo_csr.csr');
		
		//[[ Certificado ]]
		openssl_x509_export_to_file($sscert, \App\Configuration::getRootPath().'\application\config\key\pyo_cert.pem');
		
		// [[ Private key]]
		openssl_pkey_export_to_file($privkey, \App\Configuration::getRootPath().'\application\config\key\pyo_privateKey.pem',$genConfigs['passphrase']);
	}
	
}
?>