<?php

namespace CV\core\form;

trait tFormInput
{
    protected $_id;   
    protected $_name;
    protected $_value;
    protected $_className;   
    protected $_style;
    protected $_label;
    protected $_validate = [];
    public $closeTag = false;
    
    public function id($param) 
    {
        $this->_id = $param;
        return $this;
    }
    
    public function name($param) 
    {
        $this->_name = $param;
        if ( !$this->_id )
            $this->_id = $param;
        return $this;
    }
    
    public function value($param) 
    {
        $this->_value = $param;
        return $this;
    }
    
    public function className($param) 
    {
        $this->_className = $param;
        return $this;
    }
    
    public function style($param) 
    {
        $this->_style = $param;
        return $this;
    }
    
    public function label($param) 
    {
        $this->_label = $param;
        return $this;
    }
    
    public function validate($func, $msg, $grp = null) 
    {
        $this->_validate[] = [$func, $msg, $grp];
        return $this;
    }
    
    function __get($name)
    {
        return $this->{'_'.$name};
    }
}

?>