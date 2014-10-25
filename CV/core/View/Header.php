<?php

namespace CV\core\View;

/**
 * Class Header
 * @package CV\core\View
 */
abstract class Header
{
    const HTML = 'text/html';
    const XML = 'text/xml';
    const JSON = 'application/json';
    const BINARY = 'application/octet-stream';
    const PLAIN = 'text/plain';

    const CHARSET_ISO_8859_1 = 'iso-8859-1';
    const CHARSET_UTF8 = 'UTF-8';

    /**
     * @param $type
     * @param null $charset
     */
    static function set( $type, $charset = null )
    {
        \header( 'Content-Type: '. $type. ( $charset ? '; charset='. $charset : '' ) );
    }
}
