<?php

namespace CV\core;
use \CV\core\Data_Object as Obj;

class Helper
{
	protected $app;
	protected $get;
	
	function __construct()
	{
		$this->app = \CV\core\Application::getInstance();
		$this->get =& $this->app->params()->get;
	}
	
	protected function setView( $controller, $action )
	{
		$vievName = '\CV\app\views\\'. $controller. '\\'. $controller;
		if (class_exists($vievName) )
			$view = new $vievName;
		else
			throw new \Exception("View '$vievName' does not exist");
		if ( is_callable( [ $view, $action. 'Action' ] ) ) { 
			$view->{$action. 'Action'}();
			$view->set( $controller, $action );
		}
	}

	public function render()
	{
		
	}
}

?>
