<?php

namespace CV\app\controllers;

class Show extends \CV\core\Controller
{
	public function preDispatch() 
	{
		if ( !$this->model('Sections')->validHash( $this->get->id ) )
            $this->navigate('index', 'e404');
        $this->setView('index');  
	}
    
    public function postDispatch() 
	{
        //$this->setAction('denied');
	}
	
    public function postRender()
	{
		$this->view()->layout()->head .= '<script type="text/javascript">$(function(){ $("#top").hide(); });</script>';
	}
    
	public function showAction() 
	{
	}
}

?>
