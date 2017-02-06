<?php

namespace App;

/**
 * Constructor SQL
 * @author clopezh
 *
 */
class QueryBuilder{
	
	private $query = '';
	private $parameters =  array();
	private $existWhere = false;
	private $tableFrom = '';
	
	const SELECT = 'SELECT';
	const FROM = 'FROM';
	const WHERE = 'WHERE';
	
	const INSERT = ' INSERT INTO';
	
	const DEFAULT_OPERATOR = self::AND_OPERATOR;
	const AND_OPERATOR = 'AND';
	const OR_OPERATOR = 'OR';
	
	const INNER_JOIN = 'INNER JOIN';
	const LEFT_JOIN = 'LEFT JOIN';
	const RIGHT_JOIN = 'RIGHT JOIN';
	
	const DEFAULT_ORDER = self::ORDER_ASC;
	const ORDER_ASC = 'ASC';
	const ORDER_DES = 'DESC';
	
	/*
	 *  SELECT
		FROM
		WHERE
		ORDER BY
	 * */
	
	/**
	 * SELECT
	 * @param string $queryString
	 * @return \App\QueryBuilder
	 */
	public function select($queryString = '*'){
		if (!stripos($queryString, self::SELECT)){
			$queryString = self::SELECT." ".$queryString;
		}
		
		$this->append($queryString);
		
		return $this;
	}
	
	/**
	 * FROM
	 * @param unknown $table
	 * @return \App\QueryBuilder
	 */
	public function from($table){
		
		$this->tableFrom = $table;
		
		if (!stripos($table, self::FROM)){
			$table = ' '.self::FROM.' '.$table.' ';
		}
		
		$this->append($table);
		
		return $this;
	}
	
	/**
	 * Clausula WHERE
	 * @param unknown $queryString
	 * @param unknown $parameters
	 * @param unknown $operator
	 * @return \App\QueryBuilder
	 */
	public function where($queryString, $parameters , $operator = self::DEFAULT_OPERATOR){
		
		if (is_array($parameters)){
			$this->addParameters($parameters);
		}else{
			$this->addParameter($parameters);
		}
		
		if ($this->existWhere){
			$this->append($operator . ' '.$queryString);
		}else{
			if (!stripos($queryString, self::WHERE)){
				$queryString = self::WHERE.' '.$queryString;
			}
			
			$this->existWhere = true;
			$this->append($queryString);
		}
		
		return $this;
	}
	
	/**
	 * Construye un JOIN SQL
	 * @param unknown $table1 
	 * @param unknown $propertySelf propiedad con la que se hará el join
	 * @param unknown $table2 tabla de unión
	 * @param unknown $propertyT2 propiedad de unión de tabla 2
	 * * @param unknown $join tipo de join
	 * @return \App\QueryBuilder
	 */
	public function join($table1, $propertySelf, $table2, $propertyT2, $join = self::INNER_JOIN){
		
		$this->append(" $join  $table2 ON   $table1.$propertySelf  =  $table2.$propertyT2 ");
		
		return $this;
	}
	
	/**
	 * INNER JOIN
	 * @param unknown $propertySelf propiedad con la que se hará el join
	 * @param unknown $table2 tabla de unión
	 * @param unknown $propertyT2 propiedad de unión de tabla 2
	 */
	public function innerJoin($propertySelf, $table2, $propertyT2){
		$join = static::INNER_JOIN;
		$table1 = $this->tableFrom;
		return $this->join( $table1, $propertySelf, $table2, $propertyT2, $join);
	}
	
	/**
	 * LEFT JOIN
	 * @param unknown $propertySelf propiedad con la que se hará el join
	 * @param unknown $table2 tabla de unión
	 * @param unknown $propertyT2 propiedad de unión de tabla 2
	 * @return \App\QueryBuilder
	 */
	public function leftJoin($propertySelf, $table2, $propertyT2){
		$join = static::LEFT_JOIN;
		$table1 = $this->tableFrom;
		return $this->join( $table1, $propertySelf, $table2, $propertyT2, $join);
	}
	
	/**
	 * RIGHT JOIN
	 * @param unknown $propertySelf propiedad con la que se hará el join
	 * @param unknown $table2 tabla de unión
	 * @param unknown $propertyT2 propiedad de unión de tabla 2
	 * @return \App\QueryBuilder
	 */
	public function rightJoin($propertySelf, $table2, $propertyT2){
		$join = static::RIGHT_JOIN;
		$table1 = $this->tableFrom;
		return $this->join($table1, $propertySelf, $table2, $propertyT2 , $join);
	}
	
	/**
	 * Agrega código sql
	 * @param string $query
	 */
	public function append($query){
		$this->query.= $query;
	
		return $this;
	}
	
	/**
	 * Offset
	 * @param unknown $offset
	 */
	public function setResultOffset($offset){
		$this->append(" OFFSET $offset  ");
		
		return $this;
	}
	
	/**
	 * Size
	 * @param unknown $size
	 */
	public function setResultMaxSize($size){
		$this->append(" LIMIT $size  ");
		
		return $this;
	}
	
	/**
	 * Order by
	 * @param unknown $columOrder
	 * @param unknown $sort
	 * @return \App\QueryBuilder
	 */
	public function orderBy($columOrder, $sort = self::DEFAULT_ORDER){
		$this->append(" ORDER BY $columOrder  $sort ");
		
		return $this;
	}
	
	/**
	 * Agregar un parámetro
	 * @param unknown $param
	 */
	public function addParameter($param){
		$this->parameters[] = $param; 
	}
	
	/**
	 * Agregar multiples parámetros
	 * @param array $parameters
	 */
	public function addParameters(array $parameters){
		$this->parameters = array_merge($this->parameters, $parameters);
	}
	
	/**
	 * Retorna la representación SQL del query actual.
	 */
	public function getQueryString(){
		return $this->query;
	}
	
	/**
	 * Obtener los parámetros
	 * @return Ambigous <multitype:, unknown, unknown>
	 */
	public function getParameters(){
		return $this->parameters;
	}
	
	
	/**
	 * Modificar parámetros
	 * @param unknown $parameters
	 */
	public function setParameters($parameters){
		$this->parameters = $parameters;
	}
}
?>