<?php

namespace CV\app\controllers;

class Edit extends Index
{
	public function preDispatch() 
	{
		$this->setView('index');
	}
	
	public function postRender()
	{
		$this->view()->layout()->head .= '<script type="text/javascript" src="js/main.js"></script>';
	}
}

?>
