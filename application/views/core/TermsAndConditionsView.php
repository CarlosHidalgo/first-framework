<?php 

/**
 * Vista para Terminos y condiciones
 * @author clopezh
 *
 */

namespace Views\Core;

class TermsAndConditionsView extends \App\View{
	
	public function bodyContent($params = null){
		
		$urlLogin = \App\Route::getForNameRoute(\App\Route::GET, 'login');
		$urPrincipal = \App\Route::getForNameRoute(\App\Route::GET, 'principal');
		$hidden = "style= 'display:none'";
		
		$body = <<<BODY
<div class="container" >
	 
      <h1> Terminos y condiciones.</h1> <br /><br />
				
	  <p>Ustede debe aceptar los terminos y condiciones para participar en la subasta.</p>
				
		<a class="btn btn-primary dropdown-toggle" {$this->condition($this->user->getId() == null, '', $hidden)} href="$urlLogin" > Regresar a login</a>
		<a class="btn btn-primary dropdown-toggle" {$this->condition($this->user->getId() == null, $hidden, '')} href="$urPrincipal" > Regresar a Menú</a>
				
</div> <!-- /container -->	
BODY;
		
		return $body;
	}
}

?>