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
	
	/*protected function select( $additional = '' )
	{
		return self::$db->fetch( "SELECT * FROM $this->table ". $additional );
	}*/
    
    protected function select( $additional = '' )
	{
		return new ModelSelect( $this, $additional );
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

class ModelSelect {

    private $add;
    private $query = '';
    
    protected $db;
	protected $stack;
	protected $model;
    private $j = 0;
    private $ns = [];
    private $func = [];
     
	function __construct( \CV\core\Model $model, $add )
	{
		$this->stack = [];
		$this->db =& $model->db();
		$this->model =& $model;
		$this->db->start();
        $this->add = $add;
    }
    
    function __call($p,$attr) 
    {   
        $special = ['Join', 'Asc', 'Desc', 'Limit'];
        preg_match_all('/[A-Z]/', $p, $matches, PREG_OFFSET_CAPTURE);
        $query = '';
        $func = '';
        for ($i = 0, $c = sizeof($matches[0]); $i < $c; $i++) {
            $begin = $matches[0][$i][1];
            $end = !empty($matches[0][$i+1][1]) ? $matches[0][$i+1][1] - $begin : strlen($p) - $begin;
            $match = substr($p, $begin, $end );    
            $pfx = isset($attr[1]) && isset( $this->ns[ $attr[1] ] ) ? $this->ns[ $attr[1] ] : 'xxy';
            $query .= ( $i+1 == $c && !in_array($match, $special) ? " $pfx." : ' ' ). $match;
            if ( $i == 0 ) $func = $match;
        } //var_dump($func);
        $query = strtolower($query);
        if ( $func == 'Order' && in_array($func, $this->func))
            $query = str_replace('order by', ',', $query);
        $this->func[] = $func;
        if ( $match == 'Join' ) {
            $jt = strtolower($attr[0]);
            $primaryKey = $this->model->primary();
            $secondaryKey = $this->model->secondary()[0];
            $this->query .=  $query. " $jt xyz{$this->j} ON xyz{$this->j}.{$primaryKey} = xxy.{$secondaryKey}";
            $this->ns[$jt] = 'xyz'.$this->j;
        } 
        else if ( isset($attr[0]) && $match == 'Limit' )
            $this->query .= "$query ". mysql_real_escape_string($attr[0]);
        else if ( isset($attr[0]) )
            $this->query .= "$query = '". mysql_real_escape_string($attr[0]). "'";
        else
            $this->query .= $query;
        $this->j++;
        return $this;
    }
    
    public function fetch() { //var_dump("SELECT * FROM {$this->model->table()} xxy ". $this->query. $this->add);//exit;
        return $this->db->fetch( "SELECT * FROM {$this->model->table()} xxy ". $this->query. $this->add );
    }
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