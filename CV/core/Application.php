<?php

namespace CV\core;

//require_once 'loader.php';

/**
 * Class Application
 * @package CV\core
 */
abstract class Application
{
    /**
     * @var Application
     */
    protected static $app;

    /**
     * @var string
     */
    protected static $path;

    /**
     * @var
     */
    protected $return;

    /**
     * @var mixed
     */
    protected $controller;

    /**
     * @var mixed
     */
    protected $action;

    /**
     * @var mixed
     */
    protected $params;

    /**
     * @var
     */
    protected $output;

    /**
     * @var Layout
     */
    protected $layout;

    /**
     * @var
     */
    protected $controllerObj;

    /**
     * @var string
     */
    protected $layoutOverride = '';

    /**
     * @var Router
     */
    protected $router;

    /**
     *
     */
    function __construct()
    {
        date_default_timezone_set(\CV\TIME_ZONE);
        $this->preInit();
        self::$app = $this;
        self::$path = str_replace( [ '\\', '/core' ], '/', dirname( __FILE__ ) ). '/';
        $this->layout = new Layout;
        $this->router = new Router();

        /** @var \CV\core\Controller */
        $this->controller = $this->router->get('controller');

        $this->action = $this->router->get('action');
        $this->params = $this->router->get('params');
        $this->postInit();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function navigator()
    {
        $cn = '\CV\app\controllers\\'. $this->controller;
        if ( !class_exists( $cn )) {
            throw new \Exception('Invalid controller: '. $cn);
        }

        $this->controllerObj = new $cn;
        if ( !method_exists( $this->controllerObj, $this->action. 'Action' ) ) {
            throw new \Exception('Invalid action');
        }

        $this->controllerObj->preDispatch();
        $return = $this->controllerObj->{$this->action. 'Action'}();
        $this->controllerObj->postDispatch();

        return $return;
    }

    /**
     * @return Application
     */
    public static function getInstance()
    {
        return self::$app;
    }

    /**
     * @return string
     */
    public static function getPath()
    {
        return self::$path;
    }

    /**
     * @return mixed
     */
    public function &getController()
    {
        return $this->controllerObj;
    }

    /**
     * @return mixed
     */
    public function controller()
    {
        return $this->controller;
    }

    /**
     * @return mixed
     */
    public function action()
    {
        return $this->action;
    }

    /**
     * @return mixed
     */
    public function params()
    {
        return $this->params;
    }

    /**
     * @return \CV\core\Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return mixed
     */
    protected function getContent()
    {
        return $this->output;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getHelper( $name )
    {
        $name = '\CV\app\helpers\\'. $name;
        return new $name;
    }

    /**
     * @param $override
     */
    public function setLayout( $override )
    {
        $this->layoutOverride = $override;
    }

    /**
     *
     */
    private function _setLayout()
    {
        $path = \CV\core\Application::getPath(). 'layouts/'.
            ( $this->layoutOverride ? $this->layoutOverride : \CV\LAYOUT ).
            '.phtml';
        if ( file_exists( $path ) ) {
            require_once $path;
        }
    }

    /**
     *
     */
    protected function preInit()
    {

    }

    /**
     *
     */
    protected function postInit()
    {

    }

    /**
     *
     */
    protected function preRender()
    {

    }

    /**
     *
     */
    protected function postRender()
    {

    }

    /**
     * @return string
     */
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
            if ( !$this->controllerObj->isDisabledLayout() ) {
                $this->_setLayout();
            } else {
                $return = $this->output;
            }
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
