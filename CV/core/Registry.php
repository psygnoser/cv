<?php

namespace CV\core;
use \CV\core\Data_Object as Obj;

/**
 * Class Registry
 * @package CV\core
 */
abstract class Registry
{
    /**
     * @var
     */
    protected static $stack;

    /**
     * @param $name
     * @param $data
     * @param null $namespace
     * @return null
     */
    public static function set( $name, $data, $namespace = null )
    {
        if ( !self::$stack ) {
            self::$stack = new Obj;
        }
        if ( $namespace ) {
            self::$stack->$namespace->$name = $data;
            return self::$stack->$namespace->$name;
        } else {
            self::$stack->$name = $data;
            return self::$stack->$name;
        }
    }

    /**
     * @param $name
     * @param null $namespace
     * @return null
     */
    public static function get( $name, $namespace = null )
    {
        if ( !self::$stack ) {
            self::$stack = new Obj;
        }
        if ( $namespace && isset( self::$stack->$namespace ) && isset( self::$stack->$namespace->$name ) ) {
            return self::$stack->$namespace->$name;
        } else if ( isset( self::$stack->$name ) ) {
            return self::$stack->$name;
        }
        return null;
    }

    /**
     * @param $name
     * @param null $namespace
     */
    public static function kill( $name, $namespace = null )
    {
        if ( !self::$stack ) {
            self::$stack = new Obj;
        }
        if ( $namespace && isset( self::$stack->$namespace ) && isset( self::$stack->$namespace->$name ) ) {
            unset( self::$stack->$namespace->$name );
        } else if ( isset( self::$stack->$name ) ) {
            unset( self::$stack->$name );
        }
    }
}
