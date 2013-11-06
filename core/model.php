<?php

namespace CV\core;
use \CV\core\Data_Object as Obj;

class Model
{
	const T_VARCHAR = 'varchar';
	const T_INT = 'int';
	const T_FLOAT = 'float';
	const T_TEXT = 'text';
	
	private static $db;
	protected $table;
	protected $primary;
	protected $secondary = [];
	private static $models;
	
	function __construct()
	{
		if ( !self::$db ) {
			self::$db = new DB( \CV\DB_HOST, \CV\DB_USER, \CV\DB_PASW, \CV\DB_NAME );
			self::$models = new Obj;
		}
		$ccn = get_called_class();
		$this->table = strtolower( substr( $ccn, strrpos( $ccn, '\\' )+1 ) );
	}
	
	public function &db()
	{
		return self::$db;
	}
	
	public function primary()
	{
		return $this->primary;
	}
	
	public function secondary()
	{
		return $this->secondary;
	}
	
	public function table()
	{
		return $this->table;
	}
	
	protected function select( $additional = '' )
	{
		return self::$db->fetch( "SELECT * FROM $this->table ". $additional );
	}
	
	protected function update()
	{
		return new ModelUpdate($this);
	}
	
	protected function insert()
	{
		return new ModelInsert($this); 
	}
	
	protected function delete( $id, $additional = '' )
	{
		if ( !$id )
			return false;
		try {
			self::$db->query( "DELETE FROM $this->table WHERE $this->primary = '$id' ". $additional );
		} catch ( Exception $e ) {
			return false;
		}
		return true;
	}
	
	public static function invoke( $name )
	{
		if ( !isset( self::$models->$name ) ) {
			$call = '\CV\app\models\\'. $name;
			self::$models->$name = new $call;
		}
		return self::$models->$name;
	}
}

class ModelAttributes
{
	
}

class ModelInsert
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

class ModelUpdate
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
	
	public function save()
	{
		try {
			$rows = $this->stack[ $this->model->primary() ];
			for ( $i = 0; $i < sizeof( $rows ); $i++ ) {
				$row = '';
				$where = '';
				$j = 1;
				foreach ( $this->stack as $column=>$values ) {
					$val = mysql_real_escape_string( $values[$i] );
					if ( $column == $this->model->primary() )
						$where = "$column = '{$val}'";
					else {
						$row .= "$column = '{$val}'";
						if ( $j < sizeof( $this->stack ) )
							$row .= ', ';
					}
					$j++;
				}
				$query = "UPDATE {$this->model->table()} SET ". $row. ' WHERE '. $where;
				$this->db->query( $query );
			}
		} catch ( \Exception $e ) {
			$this->db->rollback();
			print $this->db->getDb()->error;
			exit;
		}
		$this->db->commit();
	}
}

?>