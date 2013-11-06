<?php

namespace CV;
use \CV\core\Data_Object as Obj;

require_once 'config.php';
require_once 'core/application.php';

class MyApplication extends core\Application
{
	protected function _routers()
	{
		//http://localhost/cv/index/public/45353453/foo
		//core\Router::set( 'public', ':controller/public/:id/:action', [ /*'controller'=>'index', 'action'=>'foo'*/ ) );	
		//core\Router::set( 'public', 'public/:id', [ 'controller'=>'index', 'action'=>'public' ) );
		
        core\Router::set( 'public', 'public/:id', [ 'controller'=>'show', 'action'=>'show' ] );
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
		$loggedIn = $this->getHelper('login')->isLogged();
		//var_dump($loggedIn,$this->controller);
		if ( !$loggedIn && $this->controller == 'index' && $this->action == 'index' ) { //die('sfs');
			$this->controller = 'index';
			$this->action = 'intro';
			
		} else if ( !$loggedIn && !in_array( $this->controller, [ 'login', 'show' ] ) ) { //die('sfs'); //'index','edit','printing'
			$this->controller = 'index';
			$this->action = 'denied';
		}
	}
}

$cv = new MyApplication;
print $cv->render();

?>