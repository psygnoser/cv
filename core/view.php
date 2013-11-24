<?php

namespace CV\core;
use CV\core\Data_Object as Obj;

class View
{
	protected $app;
	protected $view;
	protected $layout;
	protected $get;
	
	function __construct()
	{
		$this->app = \CV\core\Application::getInstance();
		$this->view = new Obj;
		$this->layout = new Obj;
		$this->get =& $this->app->params()->get;
	}
	
	public function sub( $path, array $params )
	{
		return new ViewSub( $path, $params );
	}
	
	public function renderView( $path )
	{
		$fullPath = \CV\core\Application::getPath(). 'app/views/'. strtolower( $path ). '.phtml';
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
		ViewHeader::set( ViewHeader::JSON, ViewHeader::CHARSET_UTF8 );
		return json_encode( $json, $options );
	}

	public function set( $view = null, $action = null )
	{
		if ( !$view )
			$view = $this->app->getController()->getView() ? $this->app->getController()->getView() : $this->app->controller();
		if ( !$action )
			$action = $this->app->getController()->getView() ? $this->app->getController()->getView() : $this->app->action();
		$path = \CV\core\Application::getPath(). 'app/views/'. $view. '/templates/'. $action. '.phtml';
		if ( \file_exists( $path ) ) 
			require $path;
	}
}

class ViewSub
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

abstract class ViewHeader
{
	const HTML = 'text/html';
	const XML = 'text/xml';
	const JSON = 'application/json';
	const BINARY = 'application/octet-stream';
	const PLAIN = 'text/plain';
	
	const CHARSET_ISO_8859_1 = 'iso-8859-1';
	const CHARSET_UTF8 = 'UTF-8';
	
	static function set( $type, $charset = null )
	{
		\header( 'Content-Type: '. $type. ( $charset ? '; charset='. $charset : '' ) );
	}
}

?>
