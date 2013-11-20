<?php

namespace CV\app\models;

class Fieldsets extends \CV\core\model
{
    public static $fields = ['fieldsets_id', 'name', 'position'];
    public static $primKey = 'fieldsets_id';
    public static $foreignKeys = [
        'sections'=>'sections_id'
    ];

	public function getAll()
	{
        $data = $this->select()
                ->OrderBySections_idAsc()
                ->OrderByPositionAsc()
                ->fetch();
		$remapped = [];
		foreach ( $data as $node ) {
			if ( !isset( $remapped[ $node->sections_id ] ) )
				$remapped[ $node->sections_id ] = [];
			$remapped[ $node->sections_id ][] = $node;
		}
		return $remapped;
	}
	
	public function getBySectionId( $sectionId )
	{
        $data = $this->select()
                ->WhereSections_id( (int)$sectionId )
                ->OrderBySections_idAsc()
                ->OrderByPositionAsc()
                ->fetch();
		return $data;
	}
	
	public function savePositions( $data )
	{
		$update = $this->update();
		foreach ( $data as $position=>$id ) {
			$update->fieldsets_id = $id;
			$update->position = $position;
		}
		$update->save();
	}
	
	public function saveField( $id, $name, $data )
	{
		$update = $this->update();
		$update->fieldsets_id = $id;
		$update->$name = $data;
		$update->save();
	}
	
	public function create( $section_id, $position )
	{
		$insert = $this->insert();
		$insert->name = '...';
		$insert->sections_id = $section_id;
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