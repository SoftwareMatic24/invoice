<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
	use HasFactory;

	public $timestamps = false;
	
	protected $fillable = [
		"title",
		"slug",
		"status",
		"author",
		"website",
		"image",
		"options",
		"create_datetime"
	];


	// Query

	static function getThemes(){
		return self::get();
	}

	static function getActiveTheme(){
		return self::where("status", "active")->first();
	}

	static function activateTheme($themeId){
		self::deactivateThemes();

		return self::where("id", $themeId)->update([
			"status"=>"active"
		]);
	}

	static function deactivateThemes(){
		return self::where("status", "active")->update([
			"status"=>"inactive"
		]);
	}

	static function updateOptions($slug, $options){
		return self::where("slug", $slug)->update([
			"options"=>$options !== NULL ? json_encode($options) : NULL
		]);
	}

	// Rows

	static function getRowBySlug($slug){
		return self::where("slug", $slug)->first();
	}

}
