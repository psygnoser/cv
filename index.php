<?php

namespace CV;

use CV\core\Application;
use CV\core\Router;

require_once 'config.php';
require_once 'core/application.php';

class MyApplication extends Application
{
	protected function _routers()
	{
        Router::set( 'public', 'public/:id', [ 'controller'=>'show', 'action'=>'show' ] );
	}
	
	protected function _session()
	{
		if ( ! isset( $_SESSION ) && isset( $_COOKIE['remember'] ) || isset( $_POST['remember'] ) ) {
			session_set_cookie_params( 3600 * 24 * 365 );
			session_cache_expire( 60 * 24 * 365 );
		}
		if ( ! isset( $_SESSION ) )
			session_start();
	}
	
	protected function preInit()
	{
		$this->_routers();
		$this->_session();
	}
	
	protected function postInit()
	{
		# TODO: Should be handled by a yet-to-be-made ACL
        $loggedIn = $this->getHelper('login')->isLogged();
		if ( !$loggedIn && $this->controller == 'index' && $this->action == 'index' ) { 
			$this->controller = 'index';
			$this->action = 'intro';
			
		} else if ( !$loggedIn && !in_array( $this->controller, [ 'login', 'show', 'register' ] ) ) { 
			$this->controller = 'index';
			$this->action = 'denied';
		}
	}
}

$cv = new MyApplication;
print $cv->render();

?>