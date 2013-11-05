<?php

namespace CV\app\controllers;

class Printing extends Index
{
	public function preDispatch() 
	{
		$this->setView( 'index' );
		$this->setLayout( 'printing' );
	}
	
	public function postRender()
	{
		$this->view()->layout()->head = '';
	}
}

?>
