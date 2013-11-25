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
	private static $models;
    
    public static $fields = [];
    public static $primKey = '';
    public static $foreignKeys = [];
    
    public $name;
            
	function __construct()
	{
		if ( !self::$db ) {
			self::$db = new DB( \CV\DB_HOST, \CV\DB_USER, \CV\DB_PASW, \CV\DB_NAME );
			self::$models = new Obj;
		}
		$this->name = get_called_class();
		$this->table = strtolower( substr( $this->name, strrpos( $this->name, '\\' )+1 ) );
	}
	
	public function &db()
	{
		return self::$db;
	}
	
	public function table()
	{
		return $this->table;
	}
    
    protected function select( $select = '*' )
	{
		return new model\Select( $this, $select);
	}
	
	protected function update()
	{
		return new model\Update($this);
	}
	
	protected function insert()
	{
		return new model\Insert($this); 
	}
	
	protected function delete( $id, $additional = '' )
	{
		if ( !$id )
			return false;
		try {
            $pkName = $this->name;
            $pk = $pkName::$primKey;
			self::$db->query( "DELETE FROM $this->table WHERE $pk = '". (int)$id. "' ". $additional );
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