<?php

namespace CV\app\controllers;

class Show extends \CV\core\Controller
{
	public function preDispatch() 
	{
		$this->setView('index');
	}
	
	public function showAction() 
	{
		//$this->setAction('show');
		//$this->setActionView('index');
		//$this->setView('index');
		//var_dump('overloaded');//exit;
		//var_dump(debug_backtrace(false));
	}
}

?>
