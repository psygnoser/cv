<?php

namespace CV\core;

spl_autoload_register( [ 'CV\core\Loader', 'load' ] );

abstract class Loader
{
	public final static function load( $class )
	{
		$class = preg_replace('|^'. __NAMESPACE__.'\\\|', '', $class );
		$path = '../.'. '/'. str_replace('\\', '/', strtolower( $class ) ). '.php';
        if ( file_exists($path) )
            require_once $path;
	}
}

?>
