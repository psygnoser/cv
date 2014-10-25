<?php

namespace CV\core;

/**
 * Class Auth
 * @package CV\core
 */
class Auth
{
    const WEAK = 32;
    const NORMAL = 256;
    const STRONG = 16384;
    const EXTREME = 65000;

    /**
     * @param AuthInterface $auth
     */
    public function authenticate( AuthInterface $auth )
    {

    }

    /**
     * @param $input
     * @param int $strength
     * @return string
     */
    public static function getHash( $input, $strength = self::NORMAL )
    {
        for ( $i = 0; $i < $strength; $i++ ) {
            $input = sha1($input);
        }

        return $input;
    }
}

interface AuthInterface
{
    public function authenticate( $username );
}

