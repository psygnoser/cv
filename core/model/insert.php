<?php

namespace CV\core\model;

/**
 * Class Insert
 * @package CV\core\model
 */
class Insert
{
    /**
     * @var \CV\core\DB
     */
    protected $db;

    /**
     * @var array
     */
    protected $stack;
    /**
     * @var \CV\core\Model
     */
    protected $model;

    /**
     * @param \CV\core\Model $model
     */
    function __construct( \CV\core\Model $model )
	{
		$this->stack = [];
		$this->db =& $model->db();
		$this->model =& $model;
		$this->db->start();
	}

    /**
     * @param $key
     * @param $value
     */
    public function __set( $key, $value )
	{
		if ( !isset( $this->stack[$key] ) ) {
            $this->stack[$key] = [];
        }
		$this->stack[$key][] = $value;
	}

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function __call( $key, $value )
	{
		if ( !isset( $this->stack[$key] ) ) {
            $this->stack[$key] = [];
        }
		$this->stack[$key][] = $value;

        return $this;
	}

    /**
     *
     */
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

    /**
     * @return mixed
     */
    public function id()
	{
		$result = $this->db->fetch( 'SELECT LAST_INSERT_ID() as id' );

		return $result[0]->id;
	}
}
