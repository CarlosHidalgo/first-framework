<?php

namespace Controllers;

/**
 * Controla los paginadores
 * @author clopezh
 *
 */
abstract class DataTableController{
	
	protected $uri;
	
	protected $classModel;
	protected $parameters;
	protected $total = 0;
	protected $page = 0;
	
	public function __construct($uri, $classModel){
		$this->uri = $uri;
		$this->classModel = $classModel;
		$this->parameters = array();
	}
	
	/**
	 * Modificar los parametros de búsqueda
	 * @param array $parameters
	 */
	protected function setParameters(array $parameters){
		$this->parameters = $parameters;
	}
	
	/**
	 * Módifica la página actual del dataTable
	 * @param unknown $page
	 */
	protected function setPage($page){
		$this->page = $page;
	}
	
	/**
	 * Retorna el total de elementos de la consulta, utilizando $parameters de la clase
	 * setear el valor si se desea que coincida con la última búsqueda
	 */
	public abstract function getRecordCount();
	
	/**
	 * Retorna la busqueda
	 * @param number $page
	 * @param array $parameters
	 * @param string $isRecordCount
	 */
	public abstract function search($page, array $parameters, $isRecordCount = false);
	
	/**
	 * Generá el paginador con la página $toPage seleccionada
	 * @return string <html> del paginador
	 */
	public function getPaginator(){
		
		$model = $this->classModel;
		$step = $model::PER_PAGE;
		
		$total = $this->getRecordCount();
		$this->total = $total;
		
		$firstPage = 1;
		
		$lastPage = ceil($total / $step);
		$selectedPage = $this->page;
		
		// [[ PREVIOUS ]]
		$previous = $selectedPage - 1 >= $firstPage ? $selectedPage - 1 : $selectedPage;
		$uriPrevious = $route = \App\Route::getForNameRoute('POST', $this->uri, array($previous));
		$classPrevious = $selectedPage == $previous ? 'class="disabled"' : '';
		$pages= <<<PAGES
		
		<nav>
		  <ul class="pagination">
		    <li {$classPrevious}>
		      <a href="{$uriPrevious}" aria-label="Previous">
		        <span aria-hidden="true">&laquo;</span>
		      </a>
		    </li>
PAGES;

		for ($page = $firstPage; $page <= $lastPage ; $page++ ){
			$route = \App\Route::getForNameRoute('POST', $this->uri, array($page));
			
			$class = $page == $selectedPage ? 'class="active"' : ''; 
	
			$pages.= "<li {$class} ><a href=\"{$route}\"> {$page} </a></li>";		
		}
		
		// [[ NEXT ]]
		$next = $selectedPage+1 <= $lastPage ? $selectedPage+1 : $selectedPage;
		$uriNext = $route = \App\Route::getForNameRoute('POST', $this->uri, array($next));
		$classNext = $selectedPage == $next ? 'class="disabled"' : '';
		$pages .= <<<PAGES
		     <li {$classNext}>
				<a href="{$uriNext}" aria-label="Next">
		        <span aria-hidden="true">&raquo;</span>
		      </a>
		    </li>
		  </ul>
		</nav>
PAGES;
		
		return $pages;
	}
	
	/**
	 * Total de resultados de la consulta sin paginación
	 */
	public function getTotal(){
		return $this->total;
	}
	
	/**
	 * Realiza la busqueda con los parámetros anteriores
	 */
	protected function newSearch(){
		return $this->search(0,$this->parameters);
	}
	
}
?>