<?php

namespace CV\core;

class Validator
{
	private $stack = null;
	private $validators = null;
	private $msg = [];
	
	const POST = '_POST';
	const GET = '_GET';
	const REQUEST = '_REQUEST';
	
	function __construct()
	{
		// Var init
		$this->stack = (object) null;
		$this->validators = new validateValidators();
	}
	
	public function __call( $name, $args ) 
	{
		$defVal = isset( $args[0] ) ? $args[0] : '';
		$testFunc = isset( $args[1] ) ? $args[1] : '';
		$errorMsg = isset( $args[2] ) ? $args[2] : ''; 
		$method = isset( $args[3] ) ? $args[3] : self::POST;
		$this->stack->$name = new validateFormStack( array ( 
			'value'=>( isset( $GLOBALS[ $method ][ $name ] ) && $defVal != $GLOBALS[ $method ][ $name ] ? $GLOBALS[ $method ][ $name ] : '' ),
			'defVal'=>$defVal, 
			'testFunc'=>$testFunc, 
			'errorMsg'=>$errorMsg, 
			'error'=>false 
		) );
	}
	
	public function __get( $name ) 
	{
		return $this->stack->$name;
	}
	
	public function exe()
	{	
		foreach ( $this->stack as $name => $params ) {
			if ( !$params->testFunc )
				continue;
				
			if ( $params->testFunc == 'notEmpty' 
			&& $params->value && $params->value != $params->defVal )
				continue;
				
			if ( $params->value 
			&& $params->testFunc && is_string( $params->value ) && isset( $this->validators->{ $params->testFunc } ) 
			&& preg_match( $this->validators->{ $params->testFunc }, $params->value ) )
				continue;
				
			if ( $params->testFunc 
			&& isset( $this->validators->{ $params->testFunc } ) 
			&& is_callable( 'validateValidators::'. $this->validators->{ $params->testFunc } ) ) {	
				$call = $this->validators->{ $params->testFunc };
				if ( validateValidators::$call( $params->value ) )
					continue;
			}
			$params->error = true;
			$this->msg[$name] = [ $params->errorMsg ];	
		}
	}
	
	public function reset()
	{
		foreach ( $this->stack as $stackee ) {
			$stackee->value = '';
		}
	}
	
	public function getErrors()
	{
		return $this->msg;
	}
}

class validateFormStack
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

class validateValidators
{
	public $email = '/^([a-zA-Z0-9\.\-]+)\@([a-zA-Z\.\-]+)(\.[a-zA-Z\.]{2,6})$/';
	
	public function serviceType( $value )
	{
		if ( sizeof( $value ) > 0 )
			return true;
		return false;
	}
}

?>