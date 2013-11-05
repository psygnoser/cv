<?php

namespace CV\app\views\index;

class Index extends \CV\core\View
{
	public function indexAction()
	{
		$this->view->sections = $this->model('Sections')->getAll();
		$this->view->fieldsets = $this->model('Fieldsets')->getAll();
		$this->view->fields = $this->model('Fields')->getAll();
		//var_dump(debug_backtrace(false));
	}
	
	public function fooAction()
	{
		$this->view->content = 'blue & GREEN';
	}
	
	public function deniedAction()
	{
	}
	
	public function introAction()
	{
	}
	
	public function showAction()
	{
		$this->view->sections = $this->model('Sections')->getByHash( $this->get->id );
		$this->view->fieldsets = $this->model('Fieldsets')->getAll();
		$this->view->fields = $this->model('Fields')->getAll();
		//var_dump(debug_backtrace(false));
	}
}

?>
