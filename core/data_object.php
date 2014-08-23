<?php

namespace CV\core;

/**
 * Class Data_Object
 * @package CV\core
 */
class Data_Object
{
    /**
     * @param array $data
     */
    function __construct( array $data = null )
	{
		if ( !$data ) {
            return;
        }
		foreach ( $data as $key=>$value ) {
            $this->$key = $value;
        }
	}

    /**
     * @param $name
     * @return null
     */
    function __get( $name )
	{
		if ( !isset( $this->$name ) ) {
            $this->$name = null;
        }

        return $this->$name;
	}

    /**
     * @param $name
     * @param $value
     */
    function __set( $name, $value )
	{
		$this->$name = $value;
	}
}

