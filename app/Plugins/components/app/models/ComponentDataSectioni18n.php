<?php

namespace App\Plugins\Components\Model;

use Illuminate\Database\Eloquent\Model;

class ComponentDataSectioni18n extends Model {

	public $timestamps = false;
	protected $table = 'component_data_section_i18n';
	
	protected $fillable = [
		'component_slug',
		'language_code',
		'component_number'
	];

	function data(){
		return $this->hasMany(ComponentDataSectionDatai18n::class, 'component_data_section_id', 'id');
	}

	// Query: Save

	static function addSection($componentSlug, $languageCode, $componentNumber){
		return self::create([
			'component_slug'=>$componentSlug,
			'language_code'=>$languageCode,
			'component_number'=>$componentNumber
		]);
	}

	// Query: Delete

	static function deleteSectionBySlugAndLanguage($componentSlug, $languageCode){
		return self::where('component_slug', $componentSlug)->where('language_code', $languageCode)->delete();
	}

}

?>