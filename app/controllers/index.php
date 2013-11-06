<?php

namespace CV\app\controllers;

class Index extends \CV\core\Controller
{
	public function preDispatch() {
		/*$loggedIn = $this->app->getHelper('login')->isLogged();
		var_dump($loggedIn,$this->controller);
		if ( !$loggedIn && $this->action != 'index' )
			$this->setAction( 'denied' );*/
	}

	public function indexAction()
	{

	}
	
	public function positionAction()
	{
		$this->model( ucfirst( $this->get->type ) )
			->savePositions( $_POST['data'] );
		
		$this->disableView();
		$this->disableLayout();
	}
	
	public function saveAction()
	{
		$value = $_POST['value'];
		$this->model( ucfirst( $this->get->type ) )
			->saveField( $_POST['id'], $this->get->name, $value );
		
		print $_POST['value'];
		
		$this->disableView();
		$this->disableLayout();
	}
	
	public function createAction()
	{
		$id = $this->model( ucfirst( $this->get->type ) )
			->create( $_POST['parent_id'], $_POST['position'] );

		$this->view()->setHeader( \CV\core\ViewHeader::JSON );
		print json_encode(['fid'=>$id]);
		
		$this->disableView();
		$this->disableLayout();
	}
	
	public function removeAction()
	{
		$deleted = $this->model( ucfirst( $this->get->type ) )
			->remove( $_POST['id'] );
		
		if ( !$deleted )
			$json = [ 'error'=>1, 'msg'=>'INTERNAL ERROR: The node was not deleted.' ];
		else
			$json = [ 'error'=>0 ];
	
		$this->view()->setHeader( \CV\core\ViewHeader::JSON );
		print json_encode($json);
		$this->disableView();
		$this->disableLayout();
	}
	
	public function fooAction()
	{

	}
	
	public function loginAction()
	{
		//$this->navigate('login', 'index');
	}
	
	public function subAction()
	{
		if ( !$this->get->tpl )
			throw new \Exception( 'No template!' );
		$this->view()->renderView( 'index/templates/sub/'. $this->get->tpl );
		$this->disableLayout();
	}
	
	public function headersAction()
	{
		$this->view()->setHeader( \CV\core\ViewHeader::JSON );
		print "{neki:[0,1,2],boo:'gggg'}";
		/*print '<?xml version="1.0" encoding="UTF-8"?><base><neki>boo</neki></base>';*/
		$this->disableView();
		$this->disableLayout();
	}
	
	/*public function publicAction()
	{//die('sf');
		//$this->setView('edit');
		//var_dump($this->get->id);
		//$data = $this->model('Sections')->validHash( $this->get->id );
		//var_dump($data);
		if ( $this->model('Sections')->validHash( $this->get->id ) )
			$this->setAction('index');
		else
			$this->setAction('denied');
			//return $this->deniedAction();
			//$this->setView('index');
		//return $this->indexAction();
	}
	*/
	public function deniedAction()
	{
		//die('ACCESS DENIED');
	}
	
	public function introAction()
	{
		//die('ACCESS DENIED');
	}
}

?>
