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
		$this->redirect('');
	}
	
	public function setAction()
	{	
		$data = $this->model('Users')
			->getUser( $_POST['user'], $_POST['pasw'] );
		if ( !$data ) {
			print $this->view()->json( [ 
				'error'=>1, 
				'message'=>(object) [ 'username'=>[ 'Invalid e-mail and/or password' ] ] ] );
			exit;
		}
		session_regenerate_id( true );
		$u = new Obj;
		$u->id = $data->users_id;
		$u->user = $_POST['user'];
		$u->hash = $data->hash;
		$u->role = 'registered';
		$_SESSION['u'] =& $u;
		
		if ( isset( $_POST['remember'] ) )
			setcookie( 'the_remember', true, ( time() + 3600 * 24 * 365 ) , '/' );

		print $this->view()->json( [ 'error'=>0, 'redirect'=>$_POST['referer'] ] );
		
		$this->disableView();
		$this->disableLayout();
	}
	
	public function validateAction()
	{
		$validator = new \CV\core\Validator;
		$validator->user( '', 'notEmpty', 'Write your e-mail', 1 );
        $validator->user( '', 'email', 'Invalid e-mail', 2 );
		$validator->pasw( '', 'notEmpty', 'Write your password' );
		$validator->exe();
		
		$errors = $validator->getErrors();
		$response = empty( $errors ) ? 
			[ 'error'=>0 ] :
			[ 'message'=>(object) $errors ]; 
		print $this->view()->json( $response );
        
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
	
	public function hashAction()
	{
		$salt = sha1(uniqid('', true). mt_rand(21474836, 2147483647));
		var_dump(\CV\core\Auth::getHash($this->get->pasw. $salt), $salt);
        $this->disableView();
		$this->disableLayout();
	}
}

?>
