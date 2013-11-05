<?php

namespace CV\core;
use \CV\core\Data_Object as Obj;

abstract class Registry
{
	protected static $stack;
	
	public static function set( $name, $data, $namespace = null )
	{
		if ( !self::$stack )
			self::$stack = new Obj;
		if ( $namespace ) {
			self::$stack->$namespace->$name = $data;
			return self::$stack->$namespace->$name;
		} else {
			self::$stack->$name = $data;
			return self::$stack->$name;
		}
	}
	
	public static function get( $name, $namespace = null )
	{
		if ( !self::$stack )
			self::$stack = new Obj;
		if ( $namespace && isset( self::$stack->$namespace ) && isset( self::$stack->$namespace->$name ) ) {
			return self::$stack->$namespace->$name;
		} else if ( isset( self::$stack->$name ) ) {
			return self::$stack->$name;
		}
		return null;
	}
	
	public static function kill( $name, $namespace = null )
	{
		if ( !self::$stack )
			self::$stack = new Obj;
		if ( $namespace && isset( self::$stack->$namespace ) && isset( self::$stack->$namespace->$name ) ) {
			unset( self::$stack->$namespace->$name );
		} else if ( isset( self::$stack->$name ) ) {
			unset( self::$stack->$name );
		}
	}
}

?>
