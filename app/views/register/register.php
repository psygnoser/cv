<?php

namespace CV\app\views\register;

class Register extends \CV\core\View
{
	public function indexAction()
	{
        $form = new forms\Register();
        $this->view->form = $form->render();
	}
}

?>