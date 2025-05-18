<?php

namespace App\Plugins\Components\Model;

use Illuminate\Database\Eloquent\Model;
use App\Plugins\Components\Model\ComponentDataSectionData;

class ComponentDataSection extends Model
{
	public $timestamps = false;
	
	protected $fillable = [
		'component_slug',
		'component_number'
	];

	function data(){
	
		return $this->hasMany(ComponentDataSectionData::class, 'component_data_section_id', 'id');
	}

	
	// Query: Save

	static function addSection($componentSlug, $componentNumber){
		return self::create([
			'component_slug'=>$componentSlug,
			'component_number'=>$componentNumber
		]);
	}

	// Query: Delete

	static function deleteSectionBySlug($componentSlug){
		return self::where('component_slug', $componentSlug)->delete();
	}

}
