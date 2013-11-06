<?php

namespace CV\app\models;

class Fieldsets extends \CV\core\model
{
	protected $primary = 'id';

	public function getAll()
	{
		$data = $this->select( 'ORDER BY section_id ASC, position ASC' );
		$remapped = [];
		foreach ( $data as $node ) {
			if ( !isset( $remapped[ $node->section_id ] ) )
				$remapped[ $node->section_id ] = [];
			$remapped[ $node->section_id ][] = $node;
		}
		return $remapped;
	}
	
	public function getBySectionId( $sectionId )
	{
		$data = $this->select( 'WHERE section_id = '. (int)$sectionId. 'ORDER BY section_id ASC, position ASC' );
		return $data;
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
	
	public function create( $section_id, $position )
	{
		$insert = $this->insert();
		$insert->name = '...';
		$insert->section_id = $section_id;
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
