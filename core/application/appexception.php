<?php

namespace CV\core\application;

class AppException extends \Exception
{	
	protected $data;

	function __construct( $data )
	{
		$this->data = $data;
		parent::__construct();
	}
	
	public function getData()
	{
		return $this->data;
	}
}