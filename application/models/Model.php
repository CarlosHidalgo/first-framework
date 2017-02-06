<?php

namespace Models;

use App\Connection;
/**
 * Clase base para los modelos
 * @author clopezh
 *
 */
abstract class Model{
	
	const TABLE = " ";
	const COLUMNS = array();
	const PRIMARY_KEY = 'id'; // Autoincrement (i)
	const PER_PAGE = 15;
	
	/** TYPES
	 * i	la variable correspondiente es de tipo entero
	 * d	la variable correspondiente es de tipo double
	 * s	la variable correspondiente es de tipo string
	 * b	la variable correspondiente es un blob y se envía en paquetes
	 */
	
	/**
	 * Obtiene todos los items del modelo que invoca.
	 * @param array $columns
	 * @return Object Class del modelo que invoca
	 */
	public static function all(array $columns = array('*')) {
		
		$parameters = array();
		$all = self::find($parameters);
		
		return $all;
	}
	
	/**
	 * Retorna un objeto que coincide con el id especificado para el modelo invocado.
	 * @param int $id id del Modelo a buscar
	 * @param array $columns columnas del modelo que se desean obtener por defecto (*) todas.
	 * @return Object class del modelo que invoca
	 */
	public static function findById($id, $columns = array('*')){
		
		$parameters = array( static::PRIMARY_KEY => &$id);
		$one = self::findOne($parameters);
		
		return $one;
	}
	
	/**
	 * Obtiene una lista de objetos que coincidan con los parametros indicados
	 * @param array $parameters  { key, {value, comparator (default = ), operator (default AND)} } , {key2, {value2, comparator2, operator2} }
	 * @param boolean $isRecordCount
	 * @param array $columns
	 * @param int $size número de objectos a devolver
	 * @param int $offset número a partir del cual se comenzará a buscar los objetos.
	 * @return arrayO<bject> Class del modelo que invoca
	 */
	
	public static function find(array $parameters, $isRecordCount = false, $size = null, $offset = null , array $columns = array('*')){
		$search = null;
		$types = '';
		$params = array();	
		$defultComp = ' = ';
		$defaultOp = 'AND';
		
		
		if (self::isValidPrimaryKey()){
			$connection = new \App\Connection();
			$className = get_called_class();
			$arrayColums = static::COLUMNS;
			$fields = array();
			$operators = array();
			$comparators = array();
			
			foreach ($parameters as $key => $options){
				
				$col = $arrayColums[$key];
				
				if (is_array($options)){
					
					//crear referencia
					$$options[0] = $options[0];
					$params[$key] = &$$options[0];
					
					if (isset($options[2])){
						$fields[] =  array($col[0],$options[1], $options[2]);
					}else{
						$fields[] =  array($col[0],$options[1], $defaultOp);
					}
					
				}else{
					//Crear referencia.
					$$options = $options;
					$params[$key] = &$$options;
					$fields[] = array($col[0], $defultComp, $defaultOp);
				}
				
				$types.= $col[1];
			}			
			if ($isRecordCount){
				$columns = array(' count(*) as total');
				$className = 'stdClass';
			}
			
			$queryString = self::buildBasicSelectInject(static::TABLE, $columns, $fields, $isRecordCount, $size, $offset);
			
			\App\Log::info($queryString, \Models\Model::class);
			
			$search = $connection->prepareQuery($queryString, $params, $types, $className);
			
		}
		
		return $search;
	}
	
	/**
	 * Retorna el primer modelo encontrado, usar esta función cuando se esta seguro de solo obtener un resultado
	 * @param array $parameters
	 * @param array $columns
	 * @return Object Class del modelo que ejecuta la función
	 */
	public static function findOne(array $parameters, array $columns = array('*')){
		$response = static::find($parameters,false,null, null, $columns);
		$one = null;
		
		if (isset($response) && isset($response[0])){
			$one = $response[0];
		}
		
		if (is_array($one)){
			$one = false;	
		}
		
		return $one;
	}
	
	/**
	 * Elimina el objeto actual de la base de datos
	 */
	public function delete(){
		$connection = new \App\Connection();
		$className = get_called_class();
		
		if (self::isValidPrimaryKey() ){
			
			$id = $this->getPrimaryKeyVal();
			
			$query = "DELETE FROM ".static::TABLE." WHERE ".static::PRIMARY_KEY." =  $id;";
			
			$connection->execute($query);
			
			if ($connection->getError()){
				return false;
			}else {
				return $connection->getAffectedRows();
			}
		}
	}
	
	/**
	 * Crea un nuevo objeto en la base de datos o lo actualiza
	 */
	public function saveOrUpdate(){
		$reponse = false;
		
		if (self::isValidPrimaryKey()){
			
			$id = $this->getPrimaryKeyVal();
			$properties = $this->getProperties();
			
			list($queryString, $params, $types) = $this->buildInsertOrUpdate($properties, $id);
			
			$parameters = \App\Utils::makeArrayValuesToReferenced($params);
			
			$connection = new \App\Connection();
			$connection->prepareQuery($queryString, $parameters, $types);
			
			if ($connection->getLastIdInsert()){
				$this->setPrimaryKeyVal($connection->getLastIdInsert());
			}

			if (!$connection->getError()){
				$reponse =  $connection->getAffectedRows() == 0 ? 1: $connection->getAffectedRows();
			}else{
				$reponse = $connection->getError();
			}
		}
		
		return $reponse;
	}
	
	
	/**
	 * Permite hacer una unión de dos modelos
	 * @param string $model nombre del modelo (class) al que se desea unir el modelo actual
	 * @param int $propertySelf propiedad del modelo actual sobre la que se comparará el primaryKey del $modelo
	 */
	protected function join($propertySelf, $model, array $columns = array('*')){
		$cols = implode($columns, ',');
		$m1 = 'm1';
		$m2 = 'm2';
		
		$query = "SELECT $cols FROM ".static::TABLE." as $m1  INNER JOIN ".$model::TABLE." as $m2 ";
		$query.= " ON $m1.".$propertySelf." =  $m2.".$model::PRIMARY_KEY;
		$query.= " WHERE $m1.".static::PRIMARY_KEY."  = ".$this->getPrimaryKeyVal();
		
		$className = get_called_class();
		$connection = new \App\Connection();
		$resp = $connection->prepareQuery($query, array(), array(),$className);
		
		return  $resp;
	}
	
	/**
	 * Generá una relación entre dos modelos, teniendo un modelo intermedio (pivot)
	 * @param string $targetModel modelo que se requiere obtener datos
	 * @param string $pivot modelo intermedio que permite el acceso a $targetModel
	 * @param string $idTargetModel id que hace referencia de pivo a $targetModel
	 * @param string $idPivot id que hace referencia de $pivot al modelo que realiza la invocación
	 * @param array $columns datos que se desean consultar
	 * @return Ambigous <multitype:, boolean, multitype:object >
	 */
	protected function hasToMany($targetModel, $pivot, $idPivot, $idTargetModel, array $columns = array('*')){
		$cols = implode($columns, ',');
		
		$model = static::TABLE;
		$pk1 = static::PRIMARY_KEY;
		$id = static::getId();
		
		$model2 = $targetModel::TABLE;
		$pk2 = $targetModel::PRIMARY_KEY;
		
		$query = "SELECT $cols FROM $model 
		INNER JOIN $pivot
		ON $model.$pk1 = $pivot.$idPivot
		INNER JOIN $model2
		ON $model2.$pk2 = $pivot.$idTargetModel
		WHERE $model.$pk1 = $id";
		
		$className = $targetModel;
		$connection = new \App\Connection();
		
		$resp = $connection->prepareQuery($query, array(), array(),$className);
		
		return  $resp;
	}
	
	//  ----------------------------------------------------------------------------------------------
	
	/**
	 * Verifica que la clave primaria exista para el modelo que realiza la llamada
	 * @return boolean
	 */
	private static function isValidPrimaryKey(){
		$className = get_called_class();
		
		if (property_exists($className, static::PRIMARY_KEY)){
			return true;
		}
		return false;
	}
	
	/**
	 * Construye una consulta preparada para SELECT
	 * @param string $table
	 * @param array $columns columnas que desean obtenerse
	 * @param array $fields campos de la consulta a realizar
	 */
	private static function buildBasicSelectInject($table, array $columns, array $fields, $isRecordCount, $size, $offset){
		$cols = implode($columns, ',');
		$where = self::generateWhere($fields);
		
		if (!$isRecordCount && isset($size) && isset($offset)){
			
			$where.= " LIMIT $size OFFSET $offset ";
		}
		$query = "SELECT $cols FROM $table $where";
		
		return $query;
	}
	
	/**
	* Genera una consulta preparada para INSERT o UPDATE
	* @param int $id id del modelo a actualizar,
	* @return array <string, array, string> (query, parameters, types)
	*/
	private function buildInsertOrUpdate(&$properties, &$id = null){
		$query = "";
		$params = array();
		$types = '';
			
		// UPDATE
		$set = "";
		// SAVE
		$nameValues = " (".static::PRIMARY_KEY.",";
		$values = "  VALUES(NULL,";
			
		foreach (static::COLUMNS as $colum => $colAndType){
			
			foreach ($properties as $index => &$property){
				$property->setAccessible(true);
					
				if ( strcmp($property->getName(), static::PRIMARY_KEY ) != 0 
						&& strcmp($property->getName(), $colum ) == 0  ){
					
					$aux = $property->getValue($this);
					
					$params[$colum] = $aux;
					$types.= $colAndType[1];
		
					// [[UPDATE]]
					if (isset($id)){
						$set.= "  $colum = ?,";
					}else{
						// [[SAVE]]
						$nameValues.= "$colum,";
						$values.= "?,";
					}
		
					unset($properties[$index]);
					break;
				}
				
			}// Fin IF
		}//FIn foreach COLUMS
			
		//		[[ UPDATE ]]
		if (isset($id)){
			$query = " UPDATE ".static::TABLE. " SET ";
			$query.= trim($set,",")." WHERE ".static::PRIMARY_KEY." = ?;";
			
			//Agregar id y su type
			$arrayColums = static::COLUMNS;
			$aCol = $arrayColums[static::PRIMARY_KEY];
			
			$params[$aCol[0]] = &$id;
			$types.= $aCol[1];
		
		}else{
			//	[[ SAVE ]]
			$query = " INSERT INTO ".static::TABLE;
		
			$nameValues = trim($nameValues, ",").") ";
			$values = trim($values,",").") ";
			$query.= $nameValues.$values;
		}
		return array($query, $params, $types);
	}
	
	/**
	 * Genera la clausula where para los parametros indicados en $parameters
	 * @param array $fields campos de la consulta a realizar
	 * @return String
	 */
	private static function generateWhere(array $fields){
		$response = "";
	
		if (!isset($fields) || count($fields) == 0){
			return $response;
		}
		
		foreach ($fields as $index => $param){
			$response.= " $param[0]  $param[1] ? ";
			if ($index < count($fields) - 1 ){
				$response.= $param[2];
			}
		}
		
		$response = "WHERE ".$response;
	
		return $response;
	}
	
	/**
	 * Obtiene las propiedades del objeto que invoca
	 * @return mixed
	 */
	private function getProperties(){
		$className = get_called_class();
	
		$reflectionClass = new \ReflectionClass($className);
		$properties = $reflectionClass->getProperties();
	
		return $properties;
	}
	
	/**
	 * Obtiene el valor de la clave primaria del objeto que invoca
	 * @deprecated utilice en su lugar getId()
	 * @return int
	 */
	private function getPrimaryKeyVal(){
		$className = get_called_class();
	
		$reflectionClass = new \ReflectionClass($className);
		$property = $reflectionClass->getProperty(static::PRIMARY_KEY);
		$property->setAccessible(true);
		$id = $property->getValue($this);
	
		return $id;
	}
	
	/**
	 * Modifica el valor de la clave primaria
	 * @param unknown $value
	 */
	private function setPrimaryKeyVal($value){
		$className = get_called_class();
	
		$reflectionClass = new \ReflectionClass($className);
		$property = $reflectionClass->getProperty(static::PRIMARY_KEY);
		$property->setAccessible(true);
		$property->setValue($this, $value);
	
	}
	
	/**
	 * Obtiene el valor de la clave primaria dle objeto que invoca
	 * @return int
	 */
	public abstract function getId();
	
}
?>