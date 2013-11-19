<?php

namespace CV\app\views\index;

class Index extends \CV\core\View
{
	public function indexAction()
	{
		$this->view->sections = $this->model('Sections')->getAll();
		$this->view->fieldsets = $this->model('Fieldsets')->getAll();
		$this->view->fields = $this->model('Fields')->getAll();
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
        //var_dump($this->model('Sections')->validHash( $this->get->id ));exit;
        //var_dump($this->model('Fieldsets')->getBySectionId('2')); exit;
		$this->view->sections = $this->model('Sections')->getByHash( $this->get->id );
		$this->view->fieldsets = $this->model('Fieldsets')->getAll();
		$this->view->fields = $this->model('Fields')->getAll();
	}
}

?>
