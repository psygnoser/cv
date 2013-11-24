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
    
    public function create( $email, $pasw ) 
    {
		$salt = sha1(uniqid('', true). mt_rand(21474836, 2147483647));
        $hash = sha1(uniqid('', true). mt_rand(21474836, 2147483647));
        $insert = $this->insert();
		$insert->email = $email;
		$insert->pasw = \CV\core\Auth::getHash( $pasw. $salt );;
        $insert->salt = $salt;
        $insert->hash = $hash;
		$insert->save();
		
		return ['id'=>$insert->id(), 'hash'=>$hash];
	}
    
    public function emailExists( $email )
	{
        $data = $this->select()->WhereEmail($email)->fetch();
		return isset( $data[0] ) ? true : false;
	}
}

?>
