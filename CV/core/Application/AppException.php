<?php

namespace CV\core\Application;

/**
 * Class AppException
 * @package CV\core\application
 */
class AppException extends \Exception
{
    /**
     * @var string
     */
    protected $data;

    /**
     * @param string $data
     */
    function __construct( $data )
    {
        $this->data = $data;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }
}

