<?php

namespace CV\app\controllers;

class Register extends \CV\core\Controller
{
	public function indexAction() 
	{  
    }
    
    public function validateAction() 
	{
        $form = new \CV\app\views\register\forms\Register(); ### Change app namespace
        $response = $form->validate( '\CV\app\classes\Validators' );
        print $this->view()->json( $response );

        $this->disableView();
        $this->disableLayout();
    }
    
    public function setAction() 
	{
        $model = $this->model('Users');
        $user = $model->create( $_POST['email'], $_POST['pasw'] );
        $this->app->getHelper('Login')->login( $user['id'], $_POST['email'], $user['hash'] );
        print $this->view()->json( [ 'error'=>0, 'redirect'=>'./' ] );
        $this->disableView();
        $this->disableLayout();
    }
}

?>
