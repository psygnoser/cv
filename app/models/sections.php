<?php

namespace CV\app\models;

class Sections extends \CV\core\model
{
    public static $fields = ['sections_id', 'name', 'position'];
    public static $primKey = 'sections_id';
    public static $foreignKeys = [
        'users'=>'users_id'
    ];

	public function getAll()
	{
		$uid = $_SESSION['u']->id;
        $data = $this->select()
                ->WhereUsers_id($uid)
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
    
    public function getByHashAll( $hash )
	{
        $data = $this->select()//'sections.sections_id,sections.name,fieldsets.name,fields.name')
                ->LeftJoin('users')
                ->LeftJoin('fieldsets')
                ->LeftJoin('fields')
                ->WhereHash($hash, 'users')
                ->OrderByPositionAsc()
                ->fetch();
        $data->sections = $data->sections[ $data->users[0]->users_id ]; 
		return $data;
	}
	
	public function validHash( $hash )
	{
        $data = $this->select()
                ->LeftJoin('users')
                ->WhereHash($hash, 'users')
                ->Limit('1')
                ->fetch(); //       var_dump( $data); exit;
		return !empty($data->sections);
	}
	
	public function savePositions( $data )
	{
		$update = $this->update();
		foreach ( $data as $position=>$id ) {
			$update->sections_id = $id;
			$update->position = $position;
		}
		$update->save();
	}
	
	public function saveField( $id, $name, $data )
	{
		$update = $this->update();
		$update->sections_id = $id;
		$update->$name = $data;
		$update->save();
	}
    
    public function create( $position )
	{
		$insert = $this->insert();
		$insert->name = '...';
		$insert->position = $position;
        $insert->users_id = $_SESSION['u']->id;
		$insert->save();
		
		return $insert->id();
	}
}

?>
