<?php

namespace CV\app\controllers;
use CV\core\Data_Object as Obj;
use \CV\core\Registry as Reg;

class Login extends \CV\core\Controller
{
	public function preDispatch() 
	{
		//$this->setView('index');
	}
	
	public function indexAction() 
	{
		//print 'indexAction';
		//die('indexAction');
		//$this->setAction('show');
		//$this->setActionView('index');
		//$this->setView('index');
		//var_dump('overloaded');//exit;
		//var_dump(debug_backtrace(false));
		$this->redirect('');
	}
	
	
	public function setAction()
	{	
		$data = $this->model('Users')
			->getUser( $_POST['user'], $_POST['pasw'] );
		if ( !$data ) {
			print $this->view()->json( array( 
				'error'=>1, 
				'message'=>(object) array( 'username'=>array( 'Napačno uporabniško ime in/ali geslo' ) ) ) );
			exit;
		}
		//var_dump($data);exit;
		//print $this->view()->json( array( 'error'=>0, 'redirect'=>\CV\PATH. $_POST['referer'] ) );
		//die('sdfsf');
		session_regenerate_id( true );
		$u = new Obj;//& Reg::set( 'u', new Obj ); 
		$u->id = $data->id;
		$u->user = $_POST['user'];
		//$u->pasw = $_POST['pasw'];
		$u->hash = $data->hash;
		$u->role = 'registered';
		$_SESSION['u'] =& $u;
		
		if ( isset( $_POST['remember'] ) )
			setcookie( 'the_remember', true, ( time() + 3600 * 24 * 365 ) , '/' );
		//print json_encode( $_REQUEST );
		print $this->view()->json( array( 'error'=>0, 'redirect'=>$_POST['referer'] ) );
		
		$this->disableView();
		$this->disableLayout();
	}
	
	public function validateAction()
	{
		//$this->view()->setHeader( \CV\core\ViewHeader::JSON );
		//$data = $this->model('Users')
		//	->getUser( $_POST['user'], $_POST['pasw'] );
		//var_dump($data);exit;
		
		$validator = new \CV\core\Validator;
		$validator->user( '', 'email', 'Email fail' );
		$validator->pasw( '', 'notEmpty', 'Pasw empty' );
		$validator->exe();
		//var_dump($validator->getErrors());exit;
		$errors = $validator->getErrors();
		$response = empty( $errors ) ? 
			array( 'error'=>0 ) :
			array( 'message'=>(object) $errors );
		print $this->view()->json( $response );
		/*print $this->view()->json( array( 
			'error'=> isset( $data[0] ) ? 0 : 1, 
			'message'=>(object) array( 'user'=>array('Napačno uporabniško ime in/ali geslo!') ) ) );*/
		
		$this->disableView();
		$this->disableLayout();
	}
	
	public function unsetAction()
	{
		unset( $_SESSION['u'] );
		Reg::kill('u');
		$this->redirect('');
		
		$this->disableView();
		$this->disableLayout();
	}
	
	/*public function helperAction()
	{
		die('sdfsdf');
	}*/
	
	public function hashAction()
	{
		$salt = sha1(uniqid('', true). mt_rand(21474836, 2147483647));
		var_dump(\CV\core\Auth::getHash($this->get->pasw. $salt), $salt);
	}
}

?>
