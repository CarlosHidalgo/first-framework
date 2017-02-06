<?php

namespace Views\Core;

abstract class TableView implements \App\Schema{
	
	protected $user;
	
	public function __construct(){
	
		$user = unserialize(\Security\Session::get(\Models\User::TABLE));
	
		if ($user){
			$this->user = $user;
		}else {
			$this->user = new \Models\User();
		}
	
	}
	
	/**
	 * Wrapper del método \Security\Policy::($user, $method, $uriName)
	 * @param unknown $user
	 * @param unknown $method
	 * @param unknown $uriName
	 * @return boolean
	 */
	protected function can($user, $method, $uriName){
	
		return  \Security\Policy::can($user, $method, $uriName);
	
	}
	
	public abstract function bodyContent($params = null);
	
	public function ccsFiles(){}
	
	public function jScriptFiles(){}
	
	public function jScript($params = null){}
	
	public function footerContent(){}
	
	public function paginator(){}
	
	/**
	* Wrapper para aplicar una condición a sintaxis heredoc
	* @param string $condition condición a ser evaluada
	* @param string $true valor a devolver si la condición es correcta
	* @param string $false valor a devolver si la condición es incorrecta
	* @return string
	*/
	protected function condition($condition, $true, $false){
		return $condition ? $true : $false;
	}
	
	public function getPageHtml($params = null ){
		$response = null;
		$paginator = '';
		
		if (isset($params)){
			if (is_object($params)){
				$response = $params;
			}else{
				$response = (object) $params;
			}
			$paginator = $response->paginator;
		}
		
		$rows = $this->bodyContent($response);
		$total = isset($response) ? $response->total  : 0;
		
		$table = <<<TABLE
		
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="panel-body">
				<section class="panel">
					<div class="table-responsive">
						<table class="table table-striped table-advance table-hover table-condensed table-bordered">
                           <tbody>
                           	{$rows}	
		
                           </tbody>
						</table>
					</section>
				<strong><i>Resultados:</i></strong> {$total}
				{$paginator}
				</div>
			</div>
		</div>
		
TABLE;
                    
		return $table;
	}
	
}
?>