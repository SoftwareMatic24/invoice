<?php

namespace App\Plugins\Components\Model;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Model;

use App\Plugins\Components\Model\ComponentDataSection;
use App\Plugins\Components\Model\ComponentDataSectioni18n;
use App\Plugins\Components\Model\ComponentGroup;

class Component extends Model {

	public $timestamps = false;

	protected $fillable = [
		"title",
		"slug",
		"max_entries",
		"persistence",
		"visibility",
		"create_datetime",
		"update_datetime"
	];


	function dataSections(){
	
		return $this->hasMany(ComponentDataSection::class, 'component_slug', 'slug');
	}

	function dataSectionsi18n(){
		return $this->hasMany(ComponentDataSectioni18n::class, 'component_slug', 'slug');
	}

	function groups(){
		return $this->hasMany(ComponentGroup::class, 'component_id', 'id');
	}

	static function basicRelation(){	
		return self::with('groups.schema')
			->with('dataSections.data.group.schema')
			->with('dataSections.data.media')
			->with('dataSectionsi18n.data.group.schema')
			->with('dataSectionsi18n.data.media');
	}

	// Query

	static function getComponents(){
		return self::basicRelation()->orderBy("id", "ASC")->get();
	}

	static function getComponentsFormatByKeys(){
		return self::basicRelation()->get()->keyBy("slug");
	}

	static function getComponent($componentId){
		return self::basicRelation()->where("id", $componentId)->first();
	}

	static function getComponentBySlug($slug){
		return self::basicRelation()->where('slug', $slug)->first();
	}

	static function findComponentsBySlugs($slugs){
		return self::basicRelation()->whereIn("slug", $slugs)->get();
	}

	static function updateComponent($componentSlug, $data){
		
		$component = self::where("slug", $componentSlug)->update([
			"title"=>$data["title"],
			"visibility"=>$data["visibility"] ?? "visible",
			"update_datetime"=>DateTime::getDateTime(),
		]);

		return $component;
	}

	static function deleteComponent($componentId){
		return self::where("id", $componentId)->delete();
	}


}

?>