<?php

namespace CV\core;
use CV\core\form\FormInput;

/**
 * Class Form
 * @package CV\core
 */
class Form
{
    /**
     * @var array
     */
    protected $inputs = [];

    /**
     * @var string
     */
    protected $name = '';
    /**
     * @var array
     */
    protected $render = [];

    /**
     * @param $param
     */
    function __construct($param) 
    {
        $this->name = $param;
        $this->inputs[$this->name] = [];

        return $this;
    }

    /**
     * @param FormInput $input
     * @return $this
     */
    public function input( FormInput &$input ) 
    { 
        $this->inputs[$this->name][] =& $input;

        return $this;
    }

    /**
     * @param bool $asString
     * @return object|string
     */
    public function render($asString = false) 
    {
        $return = [];
        foreach ($this->inputs[$this->name] as $input) {
            $ref = '';
            foreach ($input->validate as $vld) {
                if ( $vld[3] ) {
                    $ref = $vld[3];
                    break;
                }
            }
            $this->render[$input->name] = (object) null;

            $return =& $this->render[$input->name]->field;
            $return .= '<'. $input->tag. ' id="'. $input->id. '" name="'. $input->name. '" ref="'. $ref. '" ';
            $return .= $input->tag != $input->tagType ? ' type="'. $input->tagType. '" ' : '';
            $return .= $input->closeTag ? '>'. $input->value. "</$input->tag>" : ' value="'. $input->value. '" />';

            $this->render[$input->name]->label = $input->label;
        }

        return $asString ? implode('', $this->render) : (object) $this->render;
    }

    /**
     * @param null $vldClass
     * @param bool $single
     * @param string $method
     * @return array
     */
    public function validate( $vldClass = null, $single = false, $method = '_POST') 
    {
        $validator = new \CV\core\Validator($method);
        if ($single) {
            $validator->singleField();
        }
        if ( $vldClass && class_exists($vldClass) ) {
            $validator->setValidators($vldClass);
        }
        foreach ($this->inputs[$this->name] as $input) {

            $field = $input->name;
            if ( $input->validate ) {
                foreach ($input->validate as $valid) {
                    $validator->$field( $input->value, $valid[0], $valid[1], $valid[2], $valid[3] );
                }
            }
        }
        $validator->exe();
        $errors = $validator->getErrors();
        $response = empty( $errors ) ? 
            [ 'error'=>0 ] :
            [ 'message'=>(object)$errors ];
        
        return $response;
    }

    /**
     * @param $param
     * @return mixed
     */
    public static function _($param) 
    {
        $class= 'CV\core\form\FormInput';

        return new $class($param);
    }   
}
