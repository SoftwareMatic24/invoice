<?php

namespace App\Plugins\Setting\Model;

use Illuminate\Database\Eloquent\Model;

class Sitemap extends Model
{

	public $timestamps = false;
	protected $fillable = [
		"status"
	];
	protected $table = "sitemap";

	/**
	 * Query: Get
	 */

	static function getSitemap()
	{
		return self::first();
	}

	static function updateSitemapStatus($status)
	{
		return self::where("id", 1)->update(["status" => $status]);
	}

	static function updateSitemapExcludedURLs($data)
	{
		return self::where("id", 1)->update(["excluded_urls" => $data["urls"]]);
	}
}
