<?php

namespace CV\core;

/**
 * Class Helper
 * @package CV\core
 */
class Helper
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var
     */
    protected $get;

    /**
     *
     */
    function __construct()
    {
        $this->app = \CV\core\Application::getInstance();
        $this->get =& $this->app->params()->get;
    }

    /**
     * @param $controller
     * @param $action
     * @throws \Exception
     */
    protected function setView( $controller, $action )
    {
        $vievName = '\CV\app\views\\'. $controller. '\\'. $controller;
        if (class_exists($vievName) ) {
            $view = new $vievName;
        } else {
            throw new \Exception("View '$vievName' does not exist");
        }
        if ( is_callable( [ $view, $action. 'Action' ] ) ) {
            $view->{$action. 'Action'}();
            $view->set( $controller, $action );
        }
    }

    /**
     *
     */
    public function render()
    {

    }
}

