<?php

namespace CV\core;
use CV\core\Data_Object as Obj;
use CV\core\Application;
use CV\core\view\Header;
use CV\core\view\Node;

class View
{
	protected $app;
	protected $view;
	protected $layout;
	protected $get;
	
	function __construct()
	{
		$this->app = Application::getInstance();
		$this->view = new Node;
		$this->layout = new Obj;
		$this->get =& $this->app->params()->get;
	}
	
	public function sub( $path, array $params )
	{
		return new view\Sub( $path, $params );
	}
	
	public function renderView( $path )
	{
		$fullPath = Application::getPath(). 'app/views/'. strtolower( $path ). '.phtml';
		if ( !\file_exists( $fullPath ) )
			throw new \Exception( $fullPath );
		@require $fullPath;
	}
	
	public function setHeader( $type, $charset = null )
	{
		ViewHeader::set( $type, $charset );
	}
	
	public function beginCapture( $buffer )
	{
		if ( !isset( $this->layout->$buffer ) )
			$this->layout->$buffer = '';
		ob_start();
	}
	
	public function endCapture( $buffer )
	{
		if ( !isset( $this->layout->$buffer ) )
			$this->layout->$buffer = '';
		$this->layout->$buffer = ob_get_contents();
		ob_end_clean();
	}
	
	protected function model( $name )
	{
		return Model::invoke( $name );
	}
	
	public function &layout()
	{
		return $this->layout;
	}
	
	public function json( array $json, $options = null )
	{
		view\Header::set( Header::JSON, Header::CHARSET_UTF8 );
		return json_encode( $json, $options );
	}

	public function set( $view = null, $action = null )
	{
		if ( !$view )
			$view = $this->app->getController()->getView() ? $this->app->getController()->getView() : $this->app->controller();
		if ( !$action )
			$action = $this->app->getController()->getView() ? $this->app->getController()->getView() : $this->app->action();
		$path = Application::getPath(). 'app/views/'. $view. '/templates/'. $action. '.phtml';
		if ( \file_exists( $path ) ) 
			require $path;
	}
}
