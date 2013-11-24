<?php

namespace CV\core\validator;

class Validators
{
	private $validator;
    public $email = '/^([a-zA-Z0-9\.\-]+)\@([a-zA-Z\.\-]+)(\.[a-zA-Z\.]{2,6})$/';
    
    function __construct( \CV\core\Validator &$validator ) 
    {
        $this->validator =& $validator; 
    }

    public function sameAs( $value, $param )
	{
        $bff = $this->validator->getStack()->$param;
		if ( $value && $bff[0]->value && $value == $bff[0]->value )
			return true;
		return false;
	}
}

?>