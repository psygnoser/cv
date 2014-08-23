<?php

namespace CV\core\validator;

/**
 * Class FormStack
 * @package CV\core\validator
 */
class FormStack
{
    /**
     * @var object
     */
    private $stack;

    /**
     * @param array $stack
     */
    function __construct( array $stack )
	{
		$this->stack = (object) $stack;
	}

    /**
     * @param $name
     * @return mixed
     */
    function __get( $name )
	{
		if ( $name == 'value' ) {
			$node = $this->stack;

			return $node->value;
		}

		return $this->stack->$name;
	}
}
