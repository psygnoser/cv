<?php

namespace CV\app\models;

class Users extends \CV\core\model
{
	public static $fields = ['users_id', 'email', 'pasw', 'salt', 'hash'];
    public static $primKey = 'users_id';
    public static $foreignKeys = [
    ];

	public function getUser( $user, $pasw )
	{
        $dataSalt = $this->select()->WhereEmail($user)->fetch();
		if ( !isset( $dataSalt[0] ) )
			return false; 
		$salt = $dataSalt[0]->salt; 
		$pasw = \CV\core\Auth::getHash( $pasw. $salt );
        $data = $this->select()->WhereEmail($user)->AndPasw($pasw)->fetch();
		return isset( $data[0] ) ? $data[0] : false;
	}
}

?>
