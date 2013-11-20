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
        //die('dddd');
	}
    
    public function e404Action()
	{
        header("HTTP/1.0 404 Not Found");
	}
	
	public function introAction()
	{
	}
	
	public function showAction()
	{
		$data = $this->model('Sections')->getByHashAll( $this->get->id );
        $this->view->sections = $data->sections;
        $this->view->fieldsets = $data->fieldsets;
		$this->view->fields = $data->fields;
	}
}

?>
