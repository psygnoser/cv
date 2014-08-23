<?php

namespace CV\core;

use \CV\core\Data_Object as Obj;

/**
 * Class Router
 * @package CV\core
 */
class Router
{
    /**
     * @var array
     */
    protected static $routes = [];

    /**
     * @var mixed
     */
    protected $uri;

    /**
     * @var array
     */
    protected $paramsRaw;

    /**
     * @var
     */
    protected $paramsIndex;

    /**
     * @var Data_Object
     */
    protected $params;

    /**
     * @var
     */
    protected $controller;

    /**
     * @var
     */
    protected $action;

    /**
     *
     */
    function __construct()
	{
		$this->uri = str_replace( \CV\PATH, '', $_SERVER['REQUEST_URI'] );
		$this->paramsRaw = explode( '/', $this->uri );
		$this->params = new Obj;
		$this->params->{'get'} = new Obj;
        $this->parseAnnotations();
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
	}

    /**
     *
     */
    protected function defaultRoute()
	{
		$this->paramsIndex = 2;
		$this->controller = isset( $this->paramsRaw[0] ) && $this->paramsRaw[0] != '' ? $this->paramsRaw[0] : \CV\CONTROLLER;
		$this->action = isset( $this->paramsRaw[1] ) && $this->paramsRaw[1] != '' ? $this->paramsRaw[1] : \CV\ACTION;
	}

    /**
     *
     */
    protected function setRoute()
	{
		if ( self::$routes ) {

            foreach ( self::$routes as $name => $route ) {
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
        }
		$this->defaultRoute();
	}

    /**
     * @return array
     */
    public static function getRoutes()
    {
        return self::$routes;
    }

    /**
     * @param $property
     * @return mixed
     */
    public function get( $property )
	{
		if ( isset($this->$property) ) {
            return $this->$property;
        }
	}

    /**
     * @param $name
     * @param $pattern
     * @param array $params
     */
    public static function set( $name, $pattern, array $params )
	{
		$search = $pattern;
		foreach( $params as $key=>$value ) {
			$search = \str_replace( ':'. $key, $value, $search );
		}
		$search = \preg_replace('/:([a-z0-9_-]*)/', '([a-z0-9_-]*)', $search );
		self::$routes[$name] = [ 'pattern'=>$pattern, 'search'=>$search, 'params'=>$params ];
	}

    /**
     *
     */
    public function parseAnnotations()
    {
        $controllers = $this->getControllers();

        foreach ($controllers as $cName => $controller) {

            foreach ($controller as $action) {
                $reflectionMethod = new \ReflectionMethod($action->class, $action->name);
                $annotations = $reflectionMethod->getDocComment();
                $fns = basename($reflectionMethod->getFileName());

                preg_match_all("/@([a-zA-Z]+)\(([^)]+)\)/", $annotations, $annotationsAll);
                $annotationsMap = [];
                if (!empty($annotationsAll)) {
                   //continue;
                }

                for ($i = 0; $i < sizeof($annotationsAll) - 1 && isset($annotationsAll[1][$i]); $i++) {
                    $annotationsMap[$annotationsAll[1][$i]] = $annotationsAll[2][$i];
                }

                if (isset($annotationsMap['Route']) && $annotationsMap['Route']) {
                    $annotationParams = $annotationsMap['Route'];
                    $annotationParams = '{'. $annotationParams. '}';
                    $annotationObj = json_decode($annotationParams);
                    /*if (isset($annotationObj['params']) && $annotationObj['params']) {
                        foreach ($annotationObj['params'] as $key => $param) {
                            $annotationObj['path']
                        }
                    }*/
                    //$this->controllerObj->setView($annotationParams);
                    $ac = str_replace('Action', '', $action->name);

                    if ($fns == $cName. '.php') {
                        self::set($ac, $annotationObj->path, [ 'controller'=>$cName, 'action'=>$ac ] );
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getControllers()
    {
        $controllers = [];
        $classes = scandir(Application::getPath(). '/app/controllers/');
        foreach ($classes as $class) {
            if ($class == '.' || $class == '..') {
               continue;
            }
            $class = explode('.', $class);
            $class = $class[0];
            $classMethods = (new \ReflectionClass('CV\app\controllers\\' .ucfirst($class)))->getMethods();

            $actionMethods = [];
            foreach ($classMethods as $method) {
                if (strstr($method, 'Action') !== false) {
                    $actionMethods[] = $method;
                }
            }
            $controllers[$class] = $actionMethods;
        }

        return $controllers;
    }
}

