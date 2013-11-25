<?php

namespace CV\core\model;
use \CV\core\Data_Object as Obj;

class Insert
{
	protected $db;
	protected $stack;
	protected $model;
	
	function __construct( \CV\core\Model $model )
	{
		$this->stack = [];
		$this->db =& $model->db();
		$this->model =& $model;
		$this->db->start();
	}
	
	public function __set( $key, $value )
	{
		if ( !isset( $this->stack[$key] ) )
			$this->stack[$key] = [];
		$this->stack[$key][] = $value;
	}
    
    public function __call( $key, $value )
	{
		if ( !isset( $this->stack[$key] ) )
			$this->stack[$key] = [];
		$this->stack[$key][] = $value;
        return $this;
	}
	
	public function save()
	{
		$row = '';
		$j = 1;
		foreach ( $this->stack as $column=>$values ) {
			$val = mysql_real_escape_string( $values[0] );
			$row .= "$column = '{$val}'";
			if ( $j < sizeof( $this->stack ) )
				$row .= ', ';
			$j++;
		}
		$query =  "INSERT INTO {$this->model->table()} SET $row";
		$this->db->query( $query );
		$this->db->commit();
	}
	
	public function id()
	{
		$result = $this->db->fetch( 'SELECT LAST_INSERT_ID() as id' );
		return $result[0]->id;
	}
}