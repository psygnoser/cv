<?php

namespace CV\app\controllers;

class Edit extends Index
{
	public function preDispatch() 
	{
		$this->setView('index');
	}
	
	public function postRender()
	{
		$this->view()->layout()->head .= '<script type="text/javascript" src="js/main.js"></script>';
	}

    /**
     * @Route("path":"wham-bu-lan-ce")
     */
    public function buhuAction()
    {
        $params = $this->app->params();
        var_dump($params);
    }
}

