<?php

namespace CV\app\models;

class Fields extends \CV\core\model
{
    public static $fields = ['fields_id', 'name', 'data', 'position'];
	public static $primKey = 'fields_id';
    public static $foreignKeys = [
        'fieldsets'=>'fieldsets_id'
    ];

	public function getAll()
	{
        $data = $this->select()
                ->OrderByFieldsets_idAsc()
                ->OrderByPositionAsc()
                ->fetch();
		$remapped = [];
		foreach ( $data as $node ) {
			if ( !isset( $remapped[ $node->fieldsets_id ] ) )
				$remapped[ $node->fieldsets_id ] = [];
			$remapped[ $node->fieldsets_id ][] = $node;
		}
		return $remapped;
	}
	
	public function savePositions( $data )
	{
		$update = $this->update();
		foreach ( $data as $position=>$id ) {
			$update->fields_id = $id;
			$update->position = $position;
		}
		$update->save();
	}
	
	public function saveField( $id, $name, $data )
	{
		$update = $this->update();
		$update->fields_id = $id;
		$update->$name = $data;
		$update->save();
	}
	
	public function create( $fieldset_id, $position )
	{
		$insert = $this->insert();
		$insert->name = '...';
		$insert->data = '...';
		$insert->fieldsets_id = $fieldset_id;
		$insert->position = $position;
		$insert->save();
		
		return $insert->id();
	}
	
	public function remove( $id )
	{
		return $this->delete( $id );
	}
}

?>
