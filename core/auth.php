<?php

namespace CV\core;
use Data_Object as Obj;

class Auth
{
	const WEAK = 32;
	const NORMAL = 256;
	const STRONG = 16384;
	const EXTREME = 65000;
	
	public function authenticate( AuthInterface $auth )
	{
		
	}

	public static function getHash( $input, $strength = self::NORMAL )
	{
		for ( $i = 0; $i < $strength; $i++ )
			$input = sha1($input);
		return $input;
	}
}

interface AuthInterface
{
	public function authenticate( $username );
}

?>
