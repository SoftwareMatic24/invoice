<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
	use HasFactory;

	public $timestamps = false;
	protected $fillable = [
		"title",
		"slug",
		"type",
		"version",
		"image",
		"author",
		"website",
		"description",
		"status",
		"presistence",
		"visibility",
		"order",
		"options",
		"create_datetime"
	];



	/**
	 * ===== Query
	 */

	static function getActivePluginSlugs()
	{
		return self::where("status", "active")->select("slug")->pluck("slug")->toArray();
	}

	static function getSidebarPlugins()
	{
		return self::where("status", "active")
			->where(function ($query) {
				$query->where("visibility", "sidebar")
					->orWhere("visibility", "all");
			})
			->orderBy("order")
			->get();
	}

	static function getActivePlugins()
	{
		return self::where("status", "active")
			->orderBy("order")
			->get();
	}

	static function getPluginBySlug($slug){
		return self::where("slug",$slug)->first();
	}

}
