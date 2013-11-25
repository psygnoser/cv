<?php

namespace CV\core;

class Validator
{
	private $stack = null;
	private $validators = null;
	private $msg = [];
    private $errorGrps = [];
    private $single = false;
	
    private $method;
	const POST = '_POST';
	const GET = '_GET';
	const REQUEST = '_REQUEST';
	
	function __construct( $method = '_POST' )
	{
		$this->stack = (object) null;
		$this->validators = new validator\Validators($this);
        $this->method = $method;
	}
	
	public function __call( $name, $args ) 
	{
		$defVal = isset( $args[0] ) ? $args[0] : '';
		$testFunc = isset( $args[1] ) ? $args[1] : '';
		$errorMsg = isset( $args[2] ) ? $args[2] : ''; 
        $errorGrp = isset( $args[3] ) ? $args[3] : null;
        $ref = isset( $args[4] ) ? $args[4] : null;
        
        if ( $errorGrp ) {
            if ( !isset( $this->errorGrps[$errorGrp] ) )
                $this->errorGrps[$errorGrp] = [];
            $this->errorGrps[$errorGrp][$name] = $testFunc;
        }
        $value = '';
        if ( $this->method == self::REQUEST && isset( $_REQUEST[$name] ) )
            $value = $_REQUEST[$name];
        else if ( isset( $GLOBALS[$this->method][$name] ) )
            $value = $GLOBALS[$this->method][$name];
        if ($this->single && !$value) return;
        
        $vldNode =& $this->stack->$name;
		$vldNode[] = new validator\FormStack( array ( ### CHECK THIS CODE !!!
			'value'=>( $defVal != $value ? $value : '' ),
			'defVal'=>$defVal, 
			'testFunc'=>$testFunc, 
            'ref'=>$ref,
			'errorMsg'=>$errorMsg,
            'errorGrp'=>$errorGrp,
			'error'=>false 
		) );
	}
	
	public function __get( $name ) 
	{
		return $this->stack->$name;
	}
    
    public function getStack() 
	{
		return $this->stack;
    }
    
    public function singleField() 
	{
		$this->single = true;
    }
    
    public function setValidators( $validators )
    {
        $vldObj = new $validators($this);
        if ( $vldObj instanceof \CV\core\validator\Validators  ) { 
            $this->validators = $vldObj;}
    }
    
    public function exe()
	{              // print_r($this->stack);
		foreach ( $this->stack as $name => $paramsAll ) {
            foreach ( $paramsAll as $params ) {
                if ( !$params->testFunc )
                    continue;
                if ( $params->testFunc == 'notEmpty' 
                && $params->value && $params->value != $params->defVal ) {
                    $this->updateGrp($params->errorGrp, $name);
                    continue;
                }
                if ( $params->value 
                && is_string( $params->value ) && isset( $this->validators->{ $params->testFunc } ) 
                && preg_match( $this->validators->{ $params->testFunc }, $params->value ) ) {
                    $this->updateGrp($params->errorGrp, $name);
                    continue;
                }
                $funcParam = '';
                if ( strpos( $params->testFunc, ':' ) !== false ) {
                    $tmp = explode(':', $params->testFunc );
                    $params->testFunc = $tmp[0];
                    $funcParam = $tmp[1];
                } 
                if ( method_exists( $this->validators, $params->testFunc) ) {	
                    $call = $params->testFunc;
                    if ( $this->validators->$call( $params->value, $funcParam ) ) {
                        $this->updateGrp($params->errorGrp, $name); 
                        continue;
                    }
                }                
                if ( !$this->single && $params->errorGrp 
                && isset( $this->errorGrps[ $params->errorGrp-1 ] ) 
                && !empty( $this->errorGrps[ $params->errorGrp-1 ] ) )
                    continue;                
                $params->error = true;
                if ( !isset( $this->msg[$name] ) )
                    $this->msg[$name] = [];    
                $this->msg[$name][] = [ $params->errorMsg ];	
            }
		}//print_r($this->errorGrps);print_r($this->msg);exit;
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
    
    private function updateGrp($errorGrp, $name)
	{
		if ( $errorGrp && isset( $this->errorGrps[ $errorGrp ][ $name ] ) )
            unset( $this->errorGrps[ $errorGrp ][ $name ] );
	}
}