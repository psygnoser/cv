<?php

namespace CV\core;

class Data_Object
{
	function __construct( array $data = null )
	{
		if ( !$data )
			return;
		foreach ( $data as $key=>$value )
			$this->$key = $value;
	}
	
	function __get( $name )
	{
		if ( !isset( $this->$name ) )
			$this->$name = null;
		return $this->$name;	
	}
	
	function __set( $name, $value )
	{
		$this->$name = $value;
	}
}

?>
