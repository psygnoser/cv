<?php

namespace CV\core\view;

class Node extends \CV\core\Data_Object
{       
    public function html() 
    {print'987';
        $this->stack = htmlentities($this->stack);
        return $this;
    }
    
    function __toString() 
    {
        return $this->stack;
    }
}
