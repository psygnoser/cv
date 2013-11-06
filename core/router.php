<?php

namespace CV\core;
use \CV\core\Data_Object as Obj;

class Router
{
	protected static $routes = [];
	protected $uri;
	protected $paramsRaw;
	protected $paramsIndex;
	protected $params;
	protected $controller;
	protected $action;
	
	function __construct()
	{
		$this->uri = str_replace( \CV\PATH, '', $_SERVER['REQUEST_URI'] );
		$this->paramsRaw = explode( '/', $this->uri );
		$this->params = new Obj;
		$this->params->{'get'} = new Obj;
		$this->setRoute();
		for ( $i = $this->paramsIndex; $i < sizeof($this->paramsRaw) && sizeof($this->paramsRaw) > 2; $i++ ) {
			if ( $i & 1 )
				$this->params->get->{$this->paramsRaw[$i-1]} = isset($this->paramsRaw[$i]) ? $this->paramsRaw[$i] : null;
			else
				$this->params->get->{$this->paramsRaw[$i]} = null;
		}
		$this->params->{'sys'} = new Obj;
		$this->params->sys->{'controller'} = $this->controller;
		$this->params->sys->{'action'} =  $this->action;
		//var_dump($this->paramsIndex,$this->controller, $this->action, $this->params);
	}
	
	protected function defaultRoute()
	{
		$this->paramsIndex = 2;
		$this->controller = isset( $this->paramsRaw[0] ) && $this->paramsRaw[0] != '' ? $this->paramsRaw[0] : \CV\CONTROLLER;
		$this->action = isset( $this->paramsRaw[1] ) && $this->paramsRaw[1] != '' ? $this->paramsRaw[1] : \CV\ACTION;
	}
	
	protected function setRoute()
	{ //var_dump(self::$routes);
		if ( self::$routes )
			foreach ( self::$routes as $name=>$route ) {
				if ( \preg_match( '/'. str_replace( '/', '\/', $route['search'] ). '/', $this->uri ) ) {
					$routeRaw = explode( '/', $route['pattern'] );
					$inx = preg_match( "|{$route['search']}|", $this->uri, $uriValues );
					$i = 1;
					foreach( $routeRaw as $node ) {
						if ( preg_match( '/:controller|:action/', $node, $matches ) ) {
							$get = substr( $matches[0], 1, strlen( $matches[0] ) );
							$this->$get = $uriValues[$i++];
						} else if ( strpos( $node, ':' ) !== false ) {
							$get = substr( $node, 1, strlen( $node ) );
							$this->params->get->$get= $uriValues[$i++];
						}
					}
					$this->paramsIndex = sizeof($routeRaw);
					foreach ( $route['params'] as $pKey=>$pVal ) {
						$this->$pKey = $pVal;
					}
					return;
				}
			}
		return $this->defaultRoute();
	}

	public function get( $property )
	{
		if ( isset($this->$property) )
			return $this->$property;
	}
	
	public static function set( $name, $pattern, array $params )
	{
		$search = $pattern;
		foreach( $params as $key=>$value ) {
			$search = \str_replace( ':'. $key, $value, $search );
		}
		$search = \preg_replace('/:([a-z0-9_-]*)/', '([a-z0-9_-]*)', $search );
		self::$routes[$name] = [ 'pattern'=>$pattern, 'search'=>$search, 'params'=>$params ];
	}
}

?>
