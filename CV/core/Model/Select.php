<?php

namespace CV\core\Model;

/**
 * Class Select
 * @package CV\core\Model
 */
class Select
{
    /**
     * @var
     */
    private $add;

    /**
     * @var string
     */
    private $query = '';

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
     * @var array
     */
    private $ns = [];

    /**
     * @var array
     */
    private $func = [];

    /**
     * @var bool
     */
    private $join = false;

    /**
     * @param \CV\core\Model $model
     * @param $add
     */
    function __construct( \CV\core\Model $model, $add )
    {
        $this->stack = [];
        $this->db =& $model->db();
        $this->model =& $model;
        $this->db->start();
        $this->add = $add;
    }

    /**
     * @param $p
     * @param $attr
     * @return $this
     */
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
        else if ( isset($attr[0]) && $match == 'Limit' ) {
            $this->query .= "$query ". mysql_real_escape_string($attr[0]);
        } else if ( isset($attr[0]) ) {
            $this->query .= "$query = '". mysql_real_escape_string($attr[0]). "'";
        } else {
            $this->query .= $query;
        }

        return $this;
    }

    /**
     * @return array|\CV\core\Object
     */
    public function fetch()
    {
        $query = '';
        if ( $this->join ) {
            if ( $this->add == '*' ) {
                $select = [ $this->model->table(). '.*' ];
                foreach ( $this->ns as $table ) {
                    $pClass = '\CV\app\models\\'.ucfirst($table);
                    foreach ( $pClass::$fields as $field ) {
                        if ( $field == $pClass::$primKey ){
                            $select[] = $table. '.'. $field;
                        } else {
                            $select[] = $table. '.'. $field. ' AS '. $table. '_'. $field;
                        }
                    }
                }
                $select = implode(",\n", $select);
            } else {
                $select = $this->add;
            }
            $query = "SELECT $select FROM {$this->model->table()} ". $this->query;

            $fetch = $this->db->fetch($query);
            $return = new \CV\core\Object();
            $this->ns[ $this->model->table() ] = $this->model->table();
            foreach ( $this->ns as $ns ) {

                $return->$ns = [];
                $rns =& $return->$ns;
                $uniqIds = [];
                foreach ( $fetch as $node ) {
                    if ( isset( $uniqIds[ $node->{$ns.'_id'} ] ) ) {
                        continue;
                    }
                    if ( !isset( $node->{$ns.'_id'} ) ) {
                        continue;
                    }

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
                    } else {
                        $rns[] = $nodeN;
                    }
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
