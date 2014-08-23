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
    
    public function inRange( $value, $param )
	{
        $bff = explode('-', $param);
		if ( $value && isset($bff[0]) && isset($bff[1]) && $value >= $bff[0] && $value <= $bff[1] )
			return true;
		return false;
	}
    
    public function lenghtRange( $value, $param )
	{
        $bff = explode('-', $param);
		if ( $value && isset($bff[0]) && isset($bff[1]) && strlen($value) >= $bff[0] && strlen($value) <= $bff[1] )
			return true;
		return false;
	}
}

?>