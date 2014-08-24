<?php

namespace CV\core;

/**
 * Class DB
 * @package CV\core
 */
class DB
{
    /**
     * @var \mysqli
     */
    protected $db;

    /**
     * @param $host
     * @param $user
     * @param $pasw
     * @param $name
     */
    function __construct( $host, $user, $pasw, $name )
    {
        $this->db = new \mysqli( $host, $user, $pasw, $name );
        $this->db->set_charset('utf8');
    }

    /**
     * @return \mysqli
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @param $query
     * @param array $params
     * @return mixed
     */
    protected function bind( $query, array $params )
    {
        foreach ( $params as $key=>$value ) {
            $query = str_replace( ':'. $key, $this->db->real_escape_string($value), $query );
        }

        return $query;
    }

    /**
     * @param $query
     * @param null $params
     * @return bool|\mysqli_result
     * @throws \Exception
     */
    public function query( $query, $params = null )
    {
        $result = $this->db->query( $params ? $this->bind( $query, $params ) : $query );
        if ( !$result )
            throw new \Exception( $this->db->errno.', '.$this->db->error );

        return $result;
    }

    /**
     * @param $query
     * @param null $params
     * @return array
     */
    public function fetch( $query, $params = null )
    {
        $result = $this->query( $query, $params );
        $row = [];
        $assoc = [];
        while ( $row = $result->fetch_assoc() ) {
            $assoc[] = (object) $row;
        }

        return $assoc;
    }

    /**
     *
     */
    public function start()
    {
        $this->db->autocommit(false);
    }

    /**
     *
     */
    public function commit()
    {
        $this->db->commit();
        $this->db->autocommit(true);
    }

    /**
     *
     */
    public function rollback()
    {
        $this->db->rollback();
        $this->db->autocommit(true);
    }
}
