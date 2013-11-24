<?php

namespace CV\app\helpers;

class Login extends \CV\core\Helper
{
	public function publicLink()
	{
		if ( $this->isLogged() )
			return ' | <a href="./public/'. $_SESSION['u']->hash. '">Public link</a>';
		else
			return '';
	}
	
	public function render()
	{
		if ( !$this->isLogged() )
			$this->setView( 'login', 'helper' );
		else
			$this->setView( 'login', 'logged_in' );
	}
	
	public function isLogged()
	{
		return isset( $_SESSION['u'] );
	}
    
    public function login($id, $email, $hash) 
    {
        $u = (object) null;
		$u->id = $id;
		$u->user = $email;
		$u->hash = $hash;
		$u->role = 'registered';
		$_SESSION['u'] =& $u;
    }
}

?>
