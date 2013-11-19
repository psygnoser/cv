<?php

namespace CV\app\models;

class Fields extends \CV\core\model
{
	protected $primary = 'id';

	public function getAll()
	{
        $data = $this->select()
                ->OrderByFieldset_idAsc()
                ->OrderByPositionAsc()
                ->fetch();
		$remapped = [];
		foreach ( $data as $node ) {
			if ( !isset( $remapped[ $node->fieldset_id ] ) )
				$remapped[ $node->fieldset_id ] = [];
			$remapped[ $node->fieldset_id ][] = $node;
		}
		return $remapped;
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
}

?>
