<?php

namespace CV\app\views\register\forms;
use CV\core\Form;

class Register extends Form 
{    
    function __construct() {
        parent::__construct('register');
        
        $this->input( Form::_('text')
                    ->label('E-mail')
                    ->name('email')
                    ->validate('notEmpty', 'Write your e-mail', 4) 
                    ->validate('email', 'Invalid e-mail', 5)
                    ->validate('emailNotExists', 'E-mail already exists') )
                
                ->input( Form::_('password')
                    ->label('Password')
                    ->name('pasw')
                    ->validate('notEmpty', 'Write your password', 1) )
                
                ->input( Form::_('password')
                    ->label('Password Repeat')
                    ->name('paswr')
                    ->validate('notEmpty', 'Repeat your password', 1)
                    ->validate('sameAs:pasw', 'Passwords don\'t match', 2) )
                
                ->input( Form::_('submit')
                    ->name('submit')
                    ->value('Submit'));
    }
}
