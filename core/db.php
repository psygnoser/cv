<?php

namespace CV\core;

class DB
{
	protected $db;
	
	function __construct( $host, $user, $pasw, $name )
	{
		$this->db = new \mysqli( $host, $user, $pasw, $name );
		$this->db->set_charset('utf8');
	}
	
	public function getDb()
	{
		return $this->db;
	}
	
	protected function bind( $query, array $params )
	{
		foreach ( $params as $key=>$value ) {
			$query = str_replace( ':'. $key, $this->db->real_escape_string($value), $query );
		}
		return $query;
	}
	
	public function query( $query, $params = null )
	{
		$result = $this->db->query( $params ? $this->bind( $query, $params ) : $query );
		if ( !$result )
			throw new \Exception( $this->db->errno.', '.$this->db->error );
		return $result;
	}
	
	public function fetch( $query, $params = null )
	{
		$result = $this->query( $query, $params );
		$row = array();
		$assoc = array();
		while ( $row = $result->fetch_assoc() ) {
			$assoc[] = (object) $row;
		}
		return $assoc;
	}
		
	public function start()
	{
		$this->db->autocommit(false);
	}
	
	public function commit()
	{
		$this->db->commit();
		$this->db->autocommit(true);
	}
	
	public function rollback()
	{
		$this->db->rollback();
		$this->db->autocommit(true);
	}
}

?>