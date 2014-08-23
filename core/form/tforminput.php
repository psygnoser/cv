<?php

namespace CV\core\form;

/**
 * Trait tFormInput
 * @package CV\core\form
 */
trait tFormInput
{
    /**
     * @var
     */
    protected $_id;

    /**
     * @var
     */
    protected $_name;

    /**
     * @var
     */
    protected $_value;

    /**
     * @var
     */
    protected $_className;

    /**
     * @var
     */
    protected $_style;

    /**
     * @var
     */
    protected $_label;

    /**
     * @var array
     */
    protected $_validate = [];

    /**
     * @var bool
     */
    public $closeTag = false;

    /**
     * @param $param
     * @return $this
     */
    public function id($param) 
    {
        $this->_id = $param;
        return $this;
    }

    /**
     * @param $param
     * @return $this
     */
    public function name($param) 
    {
        $this->_name = $param;
        if ( !$this->_id )
            $this->_id = $param;
        return $this;
    }

    /**
     * @param $param
     * @return $this
     */
    public function value($param) 
    {
        $this->_value = $param;
        return $this;
    }

    /**
     * @param $param
     * @return $this
     */
    public function className($param) 
    {
        $this->_className = $param;
        return $this;
    }

    /**
     * @param $param
     * @return $this
     */
    public function style($param) 
    {
        $this->_style = $param;
        return $this;
    }

    /**
     * @param $param
     * @return $this
     */
    public function label($param) 
    {
        $this->_label = $param;
        return $this;
    }

    /**
     * @param $func
     * @param $msg
     * @param null $grp
     * @return $this
     */
    public function validate($func, $msg, $grp = null) 
    {
        $ref = null;
        if ( strpos( $func, ':' ) !== false ) {
            $tmp = explode(':', $func );
            if ( $tmp[0] == 'sameAs' )
                $ref = $tmp[1];
        }
        $this->_validate[] = [$func, $msg, $grp, $ref];

        return $this;
    }

    /**
     * @param $name
     * @return mixed
     */
    function __get($name)
    {
        return $this->{'_'.$name};
    }
}
