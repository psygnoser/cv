<?php

namespace CV\core\form;

class FormInput
{
	use tFormInput;
    public $tagType;
    public $tag;
    
    function __construct($input) {
        $this->tagType = $input;
        $this->$input();
    }
    
    public function text()
    {
        $this->tag = 'input';
    }
    
    public function password()
    {
        $this->tag = 'input';
    }
    
    public function hidden()
    {
        $this->tag = 'input';
    }
    
    public function checkbox()
    {
        $this->tag = 'input';
    }
    
    public function radio()
    {
        $this->tag = 'input';
    }
    
    public function submit()
    {
        $this->tag = 'input';
    }
    
    public function reset()
    {
        $this->tag = 'input';
    }
    
    public function button()
    {
        $this->tag = 'button';
    }
    
    public function select()
    {
        $this->tag = 'select';
        $this->closeTag = true;
    }
    
    public function textarea()
    {
        $this->tag = 'textarea';
        $this->closeTag = true;
    }
         

}