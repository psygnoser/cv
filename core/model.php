<?php

namespace CV\core;
use CV\core\Data_Object as Obj;
use CV\core\model\Insert;
use CV\core\model\Select;
use CV\core\model\Update;

/**
 * Class Model
 * @package CV\core
 */
class Model
{
	const T_VARCHAR = 'varchar';
	const T_INT = 'int';
	const T_FLOAT = 'float';
	const T_TEXT = 'text';

    /**
     * @var DB
     */
    private static $db;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var Data_Object
     */
    private static $models;

    /**
     * @var array
     */
    public static $fields = [];

    /**
     * @var string
     */
    public static $primKey = '';

    /**
     * @var array
     */
    public static $foreignKeys = [];

    /**
     * @var string
     */
    public $name;

    /**
     *
     */
    function __construct()
	{
		if ( !self::$db ) {
			self::$db = new DB( \CV\DB_HOST, \CV\DB_USER, \CV\DB_PASW, \CV\DB_NAME );
			self::$models = new Obj;
		}
		$this->name = get_called_class();
		$this->table = strtolower( substr( $this->name, strrpos( $this->name, '\\' )+1 ) );
	}

    /**
     * @return DB
     */
    public function &db()
	{
		return self::$db;
	}

    /**
     * @return string
     */
    public function table()
	{
		return $this->table;
	}

    /**
     * @param string $select
     * @return model\Select
     */
    protected function select( $select = '*' )
	{
		return new Select( $this, $select);
	}

    /**
     * @return model\Update
     */
    protected function update()
	{
		return new Update($this);
	}

    /**
     * @return model\Insert
     */
    protected function insert()
	{
		return new Insert($this);
	}

    /**
     * @param $id
     * @param string $additional
     * @return bool
     */
    protected function delete( $id, $additional = '' )
	{
		if ( !$id ) {
            return false;
        }
		try {
            $pkName = $this->name;
            $pk = $pkName::$primKey;
			self::$db->query( "DELETE FROM $this->table WHERE $pk = '". (int)$id. "' ". $additional );

		} catch ( Exception $e ) {

			return false;
		}

		return true;
	}

    /**
     * @param $name
     * @return null
     */
    public static function invoke( $name )
	{
		if ( !isset( self::$models->$name ) ) {
			$call = '\CV\app\models\\'. $name;
			self::$models->$name = new $call;
		}

		return self::$models->$name;
	}
}
