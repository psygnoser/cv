<?php

namespace CV\core\model;
use \CV\core\Data_Object as Obj;

class Update
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