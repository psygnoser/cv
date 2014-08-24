<?php

namespace CV\core\model;

/**
 * Class Update
 * @package CV\core\model
 */
class Update
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
        if ( !isset( $this->stack[$key] ) )
            $this->stack[$key] = [];
        $this->stack[$key][] = $value;
    }

    /**
     *
     */
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
                    if ( $column == $pkModel::$primKey ) {
                        $where = "$column = '{$val}'";
                    } else {
                        $row .= "$column = '{$val}'";
                        if ( $j < sizeof( $this->stack ) ) {
                            $row .= ', ';
                        }
                    }
                    $j++;
                }
                $query = "UPDATE {$this->model->table()} SET ". $row. ' WHERE '. $where;
                $this->db->query( $query );
            }
        } catch ( \Exception $e ) {
            $this->db->rollback();
            print $this->db->getDb()->error;
            exit; // @todo - ugly
        }
        $this->db->commit();
    }
}
