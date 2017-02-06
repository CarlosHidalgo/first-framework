<?php

namespace Views\Core;
/**
 * Múestra el ménu principal del sistema 
 * @author clopezh
 *
 */
class PrincipalView extends \App\View{
	
	public function ccsFiles(){
	
		$scripts = array('font-awesome.min.css');
		$css = $this->generateUriFileCSS($scripts);
	
		return $css;
	}
	
	public function bodyContent($params =  null){
		
		$urlUsers = \App\Route::getForNameRoute(\App\Route::GET, 'users');
		$urlProducts = \App\Route::getForNameRoute(\App\Route::GET, 'products');
		$urlAuctions = \App\Route::getForNameRoute(\App\Route::GET, 'auctions');
		 
		$body = <<<BODY
		<section>
		<div class="row">
				<a {$this->condition( $this->can($this->user, 'GET', 'auctions'), '', 'hidden')} 
						class="col-lg-4 col-md-4 col-sm-12 col-xs-12" role="button" href="{$urlAuctions}" style="text-decoration: none">
					<div class="info-box blue-bg">
						<i class="fa fa-usd"></i>
						<div class="count">Subastas</div>
						<div class="title">Licitaciones</div>
												
					</div><!--/.info-box-->			
				</a><!--/.col-->
				
				<a {$this->condition( $this->can($this->user, 'GET', 'users'), '', 'hidden')} 
					class="col-lg-4 col-md-4 col-sm-12 col-xs-12" role="button" href="{$urlUsers}" style="text-decoration: none" >
					<div class="info-box brown-bg">
						<i class="fa fa-users"></i>
						<div class="count">Usuarios</div>			
						<div class="title">Usuarios</div>		
											
					</div><!--/.info-box-->			
				</a><!--/.col-->	
				
				<a {$this->condition( $this->can($this->user, 'GET', 'products'), '', 'hidden')} 
					class="col-lg-4 col-md-4 col-sm-12 col-xs-12" role="button" href="{$urlProducts}" style="text-decoration: none">
					<div class="info-box teal-bg">
						<i class="fa fa-coffee"></i>
						<div class="count">Productos</div>
						<div class="title">Articulos</div>						
					</div><!--/.info-box-->			
				</a><!--/.col-->
				
			</div><!--/.row-->
		</section>
BODY;
		return $body;
	}
}
?>