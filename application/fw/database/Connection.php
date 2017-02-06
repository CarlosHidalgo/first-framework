<?php
/**
* Connection.php
* Crea una conexión hacia una Base de Datos.
* @version 1.0
*/

namespace App;

class Connection{
	
	private $user;
	private $password;
	private $host;
	private $database;
	private $port;
	private $error;
	private $affectedRows;
	private $lastId;
	private $mysqli;
	
	/**
	 * [[Constructor]] de la Clase.
	 * Si no se establece ninugn parámetro, se toma por defecto los configurados en ..\application\config\database.php
	 * @param string $host Dirección del servidor de la base de datos.
	 * @param string $user Nombre del usuario con privilegios de acceso a la base de datos.
	 * @param string $password Clave de acceso del Usuario.
	 * @param string $port puerto de conexión con el servidor de base de datos
	 * @param string $database Nombre de la base de datos.
	 */
	public function __construct($host = null, $user = null, $password = null , $database = null, $port = null){
		
		if ( !isset($host) || !isset($user) || !isset($password) || !isset($database) ){
			$config = \App\Configuration::getConfigDataBase();
			$user = $config['username'];
			$host = $config['host'];
			$password = $config['password'];
			$database = $config['database'];
			$port = $config['port'];
		}
		$this->user = $user;
		$this->host = $host;
		$this->password = $password;
		$this->database = $database;
		$this->port = $port;
	}
	
	/**
	 * Abrir una conexión con la base de datos.
	 */
	public function connect(){
		$this->error = null;
		$this->mysqli = new \mysqli($this->host,$this->user, $this->password,$this->database, $this->port);
		if ($this->mysqli->connect_error){
			$this->error = $this->mysqli->connect_error;
			return false;
		}
		return true;
	}
	
	/**
	 * comenzar una transacción
	 */
	public function beginTransaction(){
		if (isset($this->mysqli)){
			$this->mysqli->begin_transaction();
		}
	}
	
	/**
	 * rollback
	 */
	public function rollback(){
		if (isset($this->mysqli)){
			$this->mysqli->rollback();
		}
	}
	
	/**
	 * Cierra una conexión a la base de datos previamente abierta
	 */
	public function close(){
		if (isset($this->mysqli)){
			$this->mysqli->close();
		}
	}
	
	/**
	 * Ejecuta una consulta preparada para evitar inyección SQL
	 * @param string $queryString SELECT, INSERT, UPDATE (?,?,?)
	 * @param array $parameters arreglo de parametros para la consulta array ('keyUser' => 'clopezh')
	 * @param string $types tipos de datos a ingresar i (integer) d(double) s(string) b(blob)
	 * @param string $className nombre de la clase de objetos a retornar por defecto = 'stdClass'
	 * @return array Object
	 */
	public function prepareQuery($queryString , array $parameters, $types,  $className = 'stdClass'){
		$response = false;
		$this->affectedRows = 0;
		$this->lastId = 0;
		
		$types = $types == null ? "": $types;
	
		if ($this->connect()){
				
			$stm = $this->mysqli->prepare($queryString);
				
			if (!$stm){
				$this->error = $this->mysqli->error;
				\App\Log::info($this->error, get_class($this));
				return $response;
			}
				
			// [[ Bindig of parameters ]]
			if (count($parameters) > 0){
				array_unshift($parameters, $types);
				
				$metodoReflexionado = new \ReflectionMethod('mysqli_stmt', 'bind_param');
				$r = $metodoReflexionado->invokeArgs( $stm, $parameters );
			}
				
			$result = $stm->execute();
			
			$response = array();
				
			if ($result){
				
				$rQuery = $stm->get_result();
				$this->affectedRows = $stm->affected_rows;
				if ($rQuery){
					
					$this->affectedRows = $stm->affected_rows;
					
					while ($obj = $rQuery->fetch_object($className)){
						$response[] = $obj;
					}
					$rQuery->close();
					
				}else if ($stm->error){
					
					$this->error = $stm->error;
					$response = false;
					\App\Log::info($this->error, get_class($this));
				}else{
					$response = true;
					$this->lastId = $this->mysqli->insert_id;
				}
			}else if ($stm->error){
				
				$this->error = $stm->error;
				$response = false;
				\App\Log::info($this->error, get_class($this));
			}
			
			$this->close();
		}
		
		return $response;
	}
	
	/**
	 * Retornal el último Id insertado
	 * @return number
	 */
	public function getLastIdInsert(){
		return $this->lastId;
	}
	
	/**
	 * Permite ejecutar código SQL
	 * @param query $dml Operacion DML que se realizar¡ en la Base de datos.
	 * @param $className nombre de la clase de objeto que se desea obtener solo si se usa un SELECT
	 */
	public function execute ($dml, $className = 'stdClass'){
		$this->connect();
		$this->error = null;
		$response = null;
		
		$result = $this->mysqli->query($dml);
		
		if(is_bool($result)){
			$this->affectedRows = $this->mysqli->affected_rows;
			
			$response = $result;
					
		}else if($result){
			
			$response = array();
			
			while ($obj = $result->fetch_object($className)) {
				$response[] = $obj;
			}
				
			$result->close();
			
		}else if($this->mysqli->error){
			$this->error = $this->mysqli->error;
			$response = false;
		}
		
		$this->close();
		
		return $response;
	}
	
	/**
	 * Retorna el último error presentado en una operación SQL de existir
	 * @return NULL
	 */
	public function getError(){
		return $this->error;
	}
	
	/**
	 * Retorna el numero de filas afectadas por la última operación SQL
	 * @return number
	 */
	public function getAffectedRows(){
		return $this->affectedRows;
	}
	
}

?>