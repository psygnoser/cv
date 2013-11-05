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
		$data = $this->select( "WHERE user_id = '$uid' ORDER BY position ASC" );
		return $data;
	}
	
	/*public function getByUserId( $id )
	{
		$id = (int)$id;
		$data = $this->select( "WHERE user_id = '$id' ORDER BY position ASC" );
		return $data;
	}*/
	
	public function getByHash( $hash )
	{
		$data = $this->select( "LEFT JOIN users u ON u.id = user_id WHERE hash = '". mysql_real_escape_string($hash). "' ORDER BY position ASC" );
		return $data;
	}
	
	public function validHash( $hash )
	{
		$data = $this->select( "LEFT JOIN users u ON u.id = user_id WHERE hash = '". mysql_real_escape_string($hash). "' LIMIT 1" );
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
