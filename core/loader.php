<?php

namespace CV\core;

spl_autoload_register( [ 'CV\core\Loader', 'load' ] );

abstract class Loader
{
	public final static function load( $class )
	{
		$class = preg_replace('|^'. __NAMESPACE__.'\\\|', '', $class );
		$pathRaw = '../.'. '%s/'. str_replace('\\', '/', strtolower( $class ) ). '.php';		
		$paths = array ( '', '/app/controllers', '/core', '/app/models', '/app/views', '/lib' ); 
		foreach ( $paths as $pathNode ) {
			$path = sprintf( $pathRaw, $pathNode ); //var_dump($path);
			if ( file_exists( $path ) )
				require_once $path;
		}
	}
}

?>
