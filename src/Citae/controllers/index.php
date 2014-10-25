<?php

namespace CV\app\controllers;

use CV\core\Router;

class Index extends \CV\core\Controller
{
    public function preDispatch()
    {
        /*$loggedIn = $this->app->getHelper('login')->isLogged();
        var_dump($loggedIn,$this->controller);
        if ( !$loggedIn && $this->action != 'index' )
            $this->setAction( 'denied' );*/
    }

    /**
     * @Route("path":"killer/is/here/:b1/:c1", "params": {"b1": "foo", "c1": "boo"})
     * Templdate("tmp")
     */
    public function indexAction()
    {
        //print "SDFSDFSDFSDFSDFSDFFSDF";
        //var_dump($this->app->params(), Router::getRoutes());

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
        $model = $this->model( ucfirst( $this->get->type ) );
        if ( isset($_POST['parent_id']) )
            $id = $model->create( $_POST['parent_id'], $_POST['position'] );
        else
            $id = $model->create( $_POST['position'] );

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

    public function deniedAction()
    {
    }
    
    public function e404Action()
    {
    }

    public function introAction()
    {
    }
}

