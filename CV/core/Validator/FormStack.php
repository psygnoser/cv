<?php

namespace CV\core\Validator;

/**
 * Class FormStack
 * @package CV\core\Validator
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
