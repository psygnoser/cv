<?php

namespace CV\core\validator;

class FormStack
{
	private $stack;
	
	function __construct( array $stack )
	{
		$this->stack = (object) $stack;
	}
	
	function __get( $name ) 
	{
		if ( $name == 'value' ) {
			$node = $this->stack;
			return $node->value;
		}
		return $this->stack->$name;
	}
}
