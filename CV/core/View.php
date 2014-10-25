<?php

namespace CV\core;
use CV\core\Data_Object as Obj;

/**
 * Class View
 * @package CV\core
 */
class View
{
    /**
     * @var
     */
    protected $app;

    /**
     * @var
     */
    protected $view;

    /**
     * @var
     */
    protected $layout;

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
        $this->view = new Obj;
        $this->layout = new Obj;
        $this->get =& $this->app->params()->get;
    }

    /**
     * @param $path
     * @param array $params
     * @return view\Sub
     */
    public function sub( $path, array $params )
    {
        return new view\Sub( $path, $params );
    }

    /**
     * @param $path
     * @throws \Exception
     */
    public function renderView( $path )
    {
        $fullPath = \CV\core\Application::getPath(). 'app/views/'. strtolower( $path ). '.phtml';
        if ( !\file_exists( $fullPath ) ) {
            throw new \Exception( $fullPath );
        }
        @require $fullPath; //@todo .... baaaaad...
    }

    /**
     * @param $type
     * @param null $charset
     */
    public function setHeader( $type, $charset = null )
    {
        ViewHeader::set( $type, $charset );
    }

    /**
     * @param $buffer
     */
    public function beginCapture( $buffer )
    {
        if ( !isset( $this->layout->$buffer ) ) {
            $this->layout->$buffer = '';
        }
        ob_start();
    }

    /**
     * @param $buffer
     */
    public function endCapture( $buffer )
    {
        if ( !isset( $this->layout->$buffer ) ) {
            $this->layout->$buffer = '';
        }
        $this->layout->$buffer = ob_get_contents();
        ob_end_clean();
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
     * @return Data_Object
     */
    public function &layout()
    {
        return $this->layout;
    }

    /**
     * @param array $json
     * @param null $options
     * @return string
     */
    public function json( array $json, $options = null )
    {
        view\Header::set( view\Header::JSON, view\Header::CHARSET_UTF8 );
        return json_encode( $json, $options );
    }

    /**
     * @param null $view
     * @param null $action
     */
    public function set( $view = null, $action = null )
    {
        if ( !$view ) {
            $view = $this->app->getController()->getView() ? $this->app->getController()->getView() : $this->app->controller();
        }
        if ( !$action ) {
            $action = $this->app->getController()->getView() ? $this->app->getController()->getView() : $this->app->action();
        }
        $path = \CV\core\Application::getPath(). 'app/views/'. $view. '/templates/'. $action. '.phtml';
        if ( \file_exists( $path ) ) {
            require $path;
        }
    }
}

