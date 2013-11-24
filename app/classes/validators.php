<?php

namespace CV\app\classes;

class Validators extends \CV\core\validator\Validators 
{
    public function emailNotExists($param) 
    {        
        $model = \CV\core\Model::invoke('Users');
        return !$model->emailExists( $param ); 
    }
}
