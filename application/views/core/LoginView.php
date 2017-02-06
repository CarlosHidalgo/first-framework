<?php 

/**
 * Vista para el login
 * @author clopezh
 *
 */

namespace Views\Core;

class LoginView extends \App\View{
	
	public function ccsFiles(){
		
		$scripts = array('signin.css');
		$css = $this->generateUriFileCSS($scripts);
		
		return $css;
	}
	
	public function bodyContent($params = null){
		$action = \App\Route::getForNameRoute(\App\Route::POST, 'login');
		
		$logo = \App\Route::assets('assets/img/logo.png');
		$UrlTerminos = \App\Route::getForNameRoute(\App\Route::GET, 'terminos');
		
		//Varibles enviadas;
		global $msg_error;
		
		$body = <<<BODY
<div class="container">
	 
      <form class="form-signin" method="POST" action="{$action}">
        
      	<div class="row">
        <div class="col-md-6 col-md-offset-2 col-sm-6 col-sm-offset-2 col-xs-6 col-xs-offset-2">
      		<div class="img-responsive center-block" >
		  		<img src="{$logo}" />
		  	</div>
		  	
		  	</div>
		 </div>
      	
      	<h2 class="form-signin-heading text-center">First Framework </h2>
        <label for="inputEmail" class="sr-only">* Correo electrónico:</label>
        <input type="email" id="inputEmail" name="inputEmail" class="form-control" placeholder="Correo electrónico" required autofocus>
        <label for="inputPassword" class="sr-only">* Contraseña:</label>
        <input type="password" id="inputPassword" name="inputPassword" class="form-control" placeholder="Contraseña" required>
        <div class="checkbox">
          <label>
            <input name="inputAcept" type="checkbox" > Aceptar <a href="{$UrlTerminos}"> Terminos y condiciones </a></input>
          </label>
        </div>
        
        <button class="btn btn-lg btn-primary btn-block" type="submit"> Ingresar </button>
        
      </form>
      <br />
      
      <div class="alert alert-danger" role="alert" {$this->condition(isset($msg_error), '', 'hidden')}>
	  	<span class="glyphicon glyphicon-exclamation-sign"></span>
	   	<span class="sr-only">Error: </span>
	   		{$msg_error}
      </div>
</div> <!-- /container -->
BODY;
		return $body;
	}
	
}

?>