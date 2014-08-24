<?php

namespace CV\core;
use \CV\core\Data_Object as Obj;

/**
 * Class Controller
 * @package CV\core
 */
class Controller 
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var bool
     */
    private $diabledView = false;

    /**
     * @var bool
     */
    private $diabledLayout = false;

    /**
     * @var
     */
    private $_views;

    /**
     * @var
     */
    private $viewOverride;

    /**
     * @var
     */
    private $actionOverride;

    /**
     * @var array
     */
    protected $subView = [];

    /**
     * @var
     */
    protected $layout;

    /**
     * @var
     */
    protected $get;

    /**
     * @var bool
     */
    protected $built = false;

    /**
     * @var
     */
    protected $reg;

    /**
     * @var mixed
     */
    protected $controller;

    /**
     * @var mixed
     */
    protected $action;

    /**
     *
     */
    function __construct()
    {
        $this->app = \CV\core\Application::getInstance();
        $this->get =& $this->app->params()->get;

        $this->controller = $this->app->controller();
        $this->action = $this->app->action();
    }

    /**
     * @throws \Exception
     */
    protected function _setView()
    {
        $this->controller = $this->viewOverride ? $this->viewOverride : $this->app->controller();
        $this->action = $this->app->action();
        $vievName = '\CV\app\views\\'. $this->controller. '\\'. $this->controller;

        if (class_exists($vievName) ) {
            $view = new $vievName;
        } else {
            throw new \Exception(__FILE__. ", ". __LINE__. ": View '$vievName' does not exist");
        }

        if ( !isset( $this->_views[ $this->controller ] ) ) {
            $this->_views[ $this->controller ] = $view;
        }

        if ( is_callable( [ $view, $this->action. 'Action' ] ) ) {
            $view->{$this->action. 'Action'}();
            $view->set();
            $this->layout =& $view->layout();
        }

        $this->built = true;
    }

    /**
     * @param $action
     */
    public function setAction( $action )
    {
        $this->actionOverride = $action;
        $this->app->params()->sys->action = $action;
    }

    /**
     * @param $controller
     */
    public function setView( $controller )
    {
        $this->viewOverride = $controller;
        if ( $this->built )
            $this->_setView();
    }

    /**
     * @return mixed
     */
    public function getView()
    {
        return $this->viewOverride;
    }

    /**
     * @return mixed
     */
    public function getActionView()
    {
        return $this->actionOverride;
    }

    /**
     *
     */
    protected function disableView()
    {
        $this->diabledView = true;
    }

    /**
     * @param $override
     */
    public function setLayout( $override )
    {
        $this->app->setLayout( $override );
    }

    /**
     *
     */
    protected function disableLayout()
    {
        $this->diabledLayout = true;
    }

    /**
     * @param $name
     * @return null
     */
    protected function model( $name )
    {
        return Model::invoke( $name );
    }

    /**
     * @return mixed
     */
    protected function view()
    {
        $controller = $this->viewOverride ? $this->viewOverride : $this->app->params()->sys->controller;
        if ( !isset( $this->_views[ $controller ] ) ) {
            $this->_views[ $controller ] = new \CV\core\View;
        }

        return $this->_views[ $controller ];
    }

    /**
     * @param $redirect
     */
    protected function redirect( $redirect )
    {
        return header('Location: '. \CV\PATH. $redirect );
    }

    /**
     * @param $controller
     * @param $action
     * @throws application\AppException
     */
    protected function navigate( $controller, $action )
    {
        $data = new Obj;
        $data->controller = $controller;
        $data->action = $action;
        throw new application\AppException( $data );
    }

    /**
     *
     */
    public function preDispatch(){}

    /**
     *
     */
    public function postDispatch(){}

    /**
     *
     */
    public function preRender(){}

    /**
     *
     */
    public function postRender(){}

    /**
     * @return bool
     */
    public function isDisabledLayout()
    {
        return $this->diabledLayout;
    }

    /**
     * @return mixed
     */
    public function &getLayoutParams()
    {
        return $this->layout;
    }

    /**
     *
     */
    public function render()
    {
        if ( !$this->diabledView ) {
            $this->preRender();
            $this->_setView();
            $this->postRender();
        }
    }
}

