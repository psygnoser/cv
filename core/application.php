<?php

namespace CV\core;
use \CV\core\Data_Object as Obj;

require_once 'loader.php';

abstract class Application
{
	protected static $app;
	protected static $path;
	
	protected $return;
	protected $controller;
	protected $action;
	protected $params;
	protected $output;
	protected $layout;
	protected $controllerObj;
	protected $layoutOverride = '';
	protected $router;
	
	function __construct()
	{
		date_default_timezone_set(\CV\TIME_ZONE);
		$this->preInit();
		self::$app = $this;
		self::$path = str_replace( [ '\\', '/core' ], '/', dirname( __FILE__ ) ). '/';
		$this->layout = new Layout;
		$this->router = new Router();
		$this->controller = $this->router->get('controller');
		$this->action = $this->router->get('action');
		$this->params = $this->router->get('params');
		$this->postInit();
	}
	
	protected function navigator()
	{
		$cn = '\CV\app\controllers\\'. $this->controller;
		if ( !class_exists( $cn ))
			throw new \Exception('Invalid controller');
		$this->controllerObj = new $cn; //var_dump($action);
		if ( !method_exists( $this->controllerObj, $this->action. 'Action' ) )
			throw new \Exception('Invalid action');
		$this->controllerObj->preDispatch();
		$return = $this->controllerObj->{$this->action. 'Action'}();
		$this->controllerObj->postDispatch();
		return $return;
	}
	
	public static function getInstance()
	{
		return self::$app;
	}
	
	public static function getPath()
	{
		return self::$path;
	}
	
	public function &getController()
	{
		return $this->controllerObj;
	}
	
	public function controller()
	{
		return $this->controller;
	}
	
	public function action()
	{
		return $this->action;
	}
	
	public function params()
	{
		return $this->params;
	}
	
	protected function getContent()
	{
		return $this->output;
	}
	
	public function getHelper( $name )
	{
		$name = '\CV\app\helpers\\'. $name;
		return new $name;
	}
	
	public function setLayout( $override )
	{
		$this->layoutOverride = $override;	
	}
	
	private function _setLayout()
	{
		$path = \CV\core\Application::getPath(). 'layouts/'. ( $this->layoutOverride ? $this->layoutOverride : \CV\LAYOUT ). '.phtml'; 
		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
	
	protected function preInit()
	{
		
	}
	
	protected function postInit()
	{
		
	}
	
	protected function preRender()
	{
		
	}
	
	protected function postRender()
	{
		
	}
	
	/*protected function setRoute( $route, array $params )
	{
		Router::set( $route, $params );
	}*/

	public function render()
	{
		$this->preRender();
		try {
			ob_start(); 
			$this->navigator();
			$this->controllerObj->render();
			$this->output = ob_get_contents();
			ob_end_clean();
			
			$params = (array) $this->controllerObj->getLayoutParams();
			foreach( $params as $key=>$value ) {
				$this->layout->$key = $value;
			}
			$return = '';
			if ( !$this->controllerObj->isDisabledLayout() )
				$this->_setLayout();
			else
				$return = $this->output;
			$this->postRender();
			return $return;
		} catch ( application\AppException $e ) {
			$this->controller = $e->getData()->controller;
			$this->action = $e->getData()->action;
			return $this->render();
		} catch ( \Exception $e ) {
			return $e->getMessage();
		}
	}
}