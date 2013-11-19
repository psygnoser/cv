<?php

namespace CV\app\models;

class Sections extends \CV\core\model
{
	protected $primary = 'id';
	
	function __construct() 
	{
		parent::__construct();
		$this->secondary[] = 'user_id';
	}

	public function getAll()
	{
		$uid = $_SESSION['u']->id;
        $data = $this->select()
                ->WhereUser_id($uid)
                ->OrderByPositionAsc()
                ->fetch();
		return $data;
	}
	
	public function getByHash( $hash )
	{
        $data = $this->select()
                ->LeftJoin('users')
                ->WhereHash($hash, 'users')
                ->OrderByPositionAsc()
                ->fetch();
		return $data;
	}
	
	public function validHash( $hash )
	{
        $data = $this->select()
                ->LeftJoin('users')
                ->WhereHash($hash, 'users')
                ->Limit('1')
                ->fetch();
		return isset($data[0]);
	}
	
	public function savePositions( $data )
	{
		$update = $this->update();
		foreach ( $data as $position=>$id ) {
			$update->id = $id;
			$update->position = $position;
		}
		$update->save();
	}
	
	public function saveField( $id, $name, $data )
	{
		$update = $this->update();
		$update->id = $id;
		$update->$name = $data;
		$update->save();
	}
}

?>
