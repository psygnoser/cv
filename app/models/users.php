<?php

namespace CV\app\models;

class Users extends \CV\core\model
{
	protected $primary = 'id';

	public function getUser( $user, $pasw )
	{
        $dataSalt = $this->select()->WhereEmail($user)->fetch();
		if ( !isset( $dataSalt[0] ) )
			return false; 
		$salt = $dataSalt[0]->salt; 
		$pasw = \CV\core\Auth::getHash( $pasw. $salt );
        $data = $this->select()->WhereEmail($user)->AndPasw($pasw)->fetch();
		return isset( $data[0] ) ? $data[0] : false;
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
	
	public function create( $fieldset_id, $position )
	{
		$insert = $this->insert();
		$insert->name = '...';
		$insert->data = '...';
		$insert->fieldset_id = $fieldset_id;
		$insert->position = $position;
		$insert->save();
		
		return $insert->id();
	}
	
	public function remove( $id )
	{
		return $this->delete( $id );
	}
    
    /*public function sel($user) {
        $dataSalt = $this->sel1()
            ->LeftJoin('Sections', 'user_id', 'id')
            ->WhereEmail($user)
            ->AndId('1')
            ->fetch();
        var_dump($dataSalt);
    }*/
}

?>
