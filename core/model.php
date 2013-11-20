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
		return new ModelSelect( $this, $select);
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
            $pkName = $this->name;
            $pk = $pkName::$primKey;
			self::$db->query( "DELETE FROM $this->table WHERE $pk = '$id' ". $additional );
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
    private $ns = [];
    private $func = [];
    private $join = false;
     
	function __construct( \CV\core\Model $model, $add )
	{
		$this->stack = [];
		$this->db =& $model->db();
		$this->model =& $model;
		$this->db->start();
        $this->add = $add;
    }
    
    function __call( $p, $attr ) 
    {   
        $special = ['Join', 'Asc', 'Desc', 'Limit'];
        preg_match_all( '/[A-Z]/', $p, $matches, PREG_OFFSET_CAPTURE );
        $query = '';
        $func = '';
        for ($i = 0, $c = sizeof($matches[0]); $i < $c; $i++) {
            $begin = $matches[0][$i][1];
            $end = !empty( $matches[0][$i+1][1] ) ? $matches[0][$i+1][1] - $begin : strlen($p) - $begin;
            $match = substr($p, $begin, $end );    
            $pfx = isset($attr[1]) && isset( $this->ns[ $attr[1] ] ) ? $this->ns[ $attr[1] ] : $this->model->table();
            $query .= ( $i+1 == $c && !in_array($match, $special) ? " $pfx." : ' ' ). $match;
            if ( $i == 0 ) $func = $match;
        } 
        $query = strtolower($query);
        if ( $func == 'Order' && in_array($func, $this->func))
            $query = str_replace('order by', ',', $query);
        $this->func[] = $func;

        if ( $match == 'Join' ) {
            $this->join = true;
            $jt = strtolower($attr[0]);
            $jtClass = '\CV\app\models\\'.ucfirst($jt);
            $pkModel = $this->model->name;
            
            if ( !isset($pkModel::$foreignKeys[$jt]) ) {   
                foreach ($jtClass::$foreignKeys as $pModel => $pKey);
                $pClass = '\CV\app\models\\'.ucfirst($pModel);
                $primaryKey = $pKey;
                $foreignKey = "$pModel.". $pClass::$primKey;
            } else {
                $primaryKey = $jtClass::$primKey;
                $foreignKey = $this->model->table(). '.'. $pkModel::$foreignKeys[$jt]; 
            }
            $this->query .=  "\n". $query. " $jt ON {$jt}.{$primaryKey} = {$foreignKey}";
            $this->ns[$jt] = $jt;
        }
        else if ( isset($attr[0]) && $match == 'Limit' )
            $this->query .= "$query ". mysql_real_escape_string($attr[0]);
        else if ( isset($attr[0]) )
            $this->query .= "$query = '". mysql_real_escape_string($attr[0]). "'";
        else
            $this->query .= $query;
        return $this;
    }
    
    public function fetch() 
    { 
        $query = '';
        if ( $this->join ) {    
            if ( $this->add == '*' ) { 
                $select = [ $this->model->table(). '.*' ];
                foreach ( $this->ns as $table ) {
                    $pClass = '\CV\app\models\\'.ucfirst($table);
                    foreach ( $pClass::$fields as $field ) {
                        if ( $field == $pClass::$primKey )
                            $select[] = $table. '.'. $field;
                        else
                            $select[] = $table. '.'. $field. ' AS '. $table. '_'. $field;
                    }
                }
                $select = implode(",\n", $select);
            } else
                $select = $this->add;
            $query = "SELECT $select FROM {$this->model->table()} ". $this->query;
            
            $fetch = $this->db->fetch($query);
            $return = new \CV\core\Object();
            $this->ns[ $this->model->table() ] = $this->model->table();
            foreach ( $this->ns as $ns ) {
                $return->$ns = [];
                $rns =& $return->$ns;
                $uniqIds = [];
                foreach ( $fetch as $node ) {
                    if ( isset( $uniqIds[ $node->{$ns.'_id'} ] ) ) continue;
                    if ( !isset( $node->{$ns.'_id'} ) ) continue;
                    $nodeN = new \CV\core\Object();        
                    foreach ( $node as $key=>$value ) {
                        if ( $key != $ns.'_id' )
                            $key = str_replace( $ns. '_', '', $key );
                        $nodeN->$key = $value;
                    }
                    $pClass = '\CV\app\models\\'.ucfirst($ns);
                    if ( !empty( $pClass::$foreignKeys ) ) {
                        foreach ($pClass::$foreignKeys as $pModel => $pKey);
                        $rns[ $node->{$pModel.'_id'} ][] = $nodeN;
                    } else
                        $rns[] = $nodeN;
                    $uniqIds[ $node->{$ns.'_id'} ] = true;
                }
            } 
        } else {
            $query = "SELECT $this->add FROM {$this->model->table()} ". $this->query;  
            $return = $this->db->fetch($query);
        }
        return $return;
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
		$pkModel = $this->model->name;
        try {
			$rows = $this->stack[ $pkModel::$primKey ];
			for ( $i = 0; $i < sizeof( $rows ); $i++ ) {
				$row = '';
				$where = '';
				$j = 1;
				foreach ( $this->stack as $column=>$values ) {
					$val = mysql_real_escape_string( $values[$i] );
					if ( $column == $pkModel::$primKey )
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