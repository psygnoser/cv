<?php

namespace CV\core;
use \CV\core\Data_Object as Obj;

class Controller 
{
	protected $app;
	private $diabledView = false;
	private $diabledLayout = false;
	private $_views;
	private $viewOverride;
	private $actionOverride;
	//private $layoutOverride = '';
	protected $subView = array();
	protected $layout;
	protected $get;
	protected $built = false;
	protected $reg;
	
	protected $controller;
	protected $action;
	
	function __construct()
	{
		$this->app = \CV\core\Application::getInstance();
		$this->get =& $this->app->params()->get;
		
		$this->controller = $this->app->controller();
		$this->action = $this->app->action(); 
	}
	
	protected function _setView()
	{ // var_dump($this->app->params()->sys->controller,$this->app->params()->sys->action);//var_dump($this->viewOverride);
		$this->controller = $this->viewOverride ? $this->viewOverride : $this->app->controller();
		$this->action = $this->app->action(); //$this->actionOverride ? $this->actionOverride : 
		$vievName = '\CV\app\views\\'. $this->controller. '\\'. $this->controller;
		if (class_exists($vievName) )
			$view = new $vievName;
		else
			throw new \Exception(__FILE__. ", ". __LINE__. ": View '$vievName' does not exist");
		if ( !isset( $this->_views[ $this->controller ] ) )
			$this->_views[ $this->controller ] = $view;
		if ( is_callable( array( $view, $this->action. 'Action' ) ) ) { // !$this->actionOverride && 
			//if ( !$this->actionOverride )
			$view->{$this->action. 'Action'}();
			$view->set();
			$this->layout =& $view->layout();
		}
		$this->built = true;
		//var_dump($controller,$action);
	}
	
	public function setAction( $action )
	{
		$this->actionOverride = $action;
		$this->app->params()->sys->action = $action;
		//if ( $this->built )
		//return $this->_setView();
	}
	
	public function setView( $controller )
	{//var_dump($this->built);
		$this->viewOverride = $controller;
		if ( $this->built )
			$this->_setView();
	}
	
	public function getView()
	{
		return $this->viewOverride;
	}
	
	public function getActionView()
	{
		return $this->actionOverride;
	}
	
	protected function disableView()
	{
		$this->diabledView = true;
	}
	
	public function setLayout( $override )
	{
		$this->app->setLayout( $override );
	}
	
	protected function disableLayout()
	{
		$this->diabledLayout = true;
	}
	
	protected function model( $name )
	{
		return Model::invoke( $name );
	}
	
	protected function view()
	{
		$controller = $this->viewOverride ? $this->viewOverride : $this->app->params()->sys->controller;
		if ( !isset( $this->_views[ $controller ] ) )
			$this->_views[ $controller ] = new \CV\core\View;
		return $this->_views[ $controller ];
	}
	
	protected function redirect( $redirect )
	{
		return header('Location: '. \CV\PATH. $redirect );
	}
	
	protected function navigate( $controller, $action )
	{
		$data = new Obj;
		$data->controller = $controller;
		$data->action = $action;
		throw new \CV\core\ApplicationException( $data );
	}

	public function preDispatch(){}
	
	public function postDispatch(){}
	
	public function preRender(){}
	
	public function postRender(){}
	
	public function isDisabledLayout()
	{
		return $this->diabledLayout;
	}
	
	public function &getLayoutParams()
	{
		return $this->layout;
	}

	public function render()
	{
		if ( !$this->diabledView ) {
			$this->preRender();
			$this->_setView();
			$this->postRender();
		}
	}
}

?>
