<?php

namespace App\Plugins\Components\Model;

use App\Classes\DateTime;
use App\Plugins\MediaCenter\Models\Media;
use Illuminate\Database\Eloquent\Model;

class ComponentDataSectionData extends Model
{
	public $timestamps = false;

	protected $fillable = [
		'label',
		'value',
		'component_group_id',
		'component_data_section_id',
		'create_datetime',
		'update_datetime'
	];

	function media()
	{
		return $this->belongsTo(Media::class, 'value', 'id');
	}

	function group()
	{
		return $this->belongsTo(ComponentGroup::class, 'component_group_id', 'id');
	}

	// Query: Save

	static function addSectionData($label, $value, $component_group_id, $component_data_section_id){
		return self::create([
			'label'=>$label,
			'value'=>$value,
			'component_group_id'=>$component_group_id,
			'component_data_section_id'=>$component_data_section_id,
			'create_datetime'=>DateTime::getDateTime()
		]);
	}

}
