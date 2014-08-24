<?php

namespace CV\core\view;
use CV\core\Data_Object as Obj;

class Sub
{
    private $app;
    private $view;

    function __construct( $path, array $params )
    {
        $this->app = \CV\core\Application::getInstance();
        $this->view = new Obj( $params );
        $view = $this->app->getController()->getView() ? $this->app->getController()->getView() : $this->app->controller();
        $fullPath = \CV\core\Application::getPath(). 'app/views/'. $view. '/templates/'. strtolower( $path ). '.phtml';
        if ( \file_exists( $fullPath ) )
            $this->_setSub( $fullPath );
    }

    public function sub( $path, array $params )
    {
        $class = __CLASS__;
        return new $class( $path, $params );
    }

    protected function _setSub( $path )
    {
        require $path;
    }
}
