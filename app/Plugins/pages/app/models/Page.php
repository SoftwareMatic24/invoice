<?php

namespace App\Plugins\Pages\Models;

use App\Classes\DateTime;
use App\Plugins\MediaCenter\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Exception;

class Page extends Model
{

	public $timestamps = false;
	protected $fillable  = [
		"title",
		"page_title",
		"description",
		"slug",
		"persistence",
		"status",
		"featured_image",
		"featured_video",
		"featured_video_thumbnail",
		"content",
		"meta",
		"create_datetime"
	];

	// Relation

	function featuredImage()
	{
		return $this->belongsTo(Media::class, "featured_image", "id");
	}

	function featuredVideo()
	{
		return $this->belongsTo(Media::class, "featured_video", "id");
	}

	function featuredVideoThumbnail()
	{
		return $this->belongsTo(Media::class, "featured_video_thumbnail", "id");
	}

	function pagei18n()
	{
		return $this->hasMany(Pagei18n::class, "page_id", "id");
	}

	// Build

	static function basicRelation()
	{
		$withPagei18n = function ($query) {
			$query->select("title", "page_title","description", "content", "meta", "page_id", "language_code");
		};
		return self::with(["pagei18n" => $withPagei18n])
			->with("featuredImage")
			->with("featuredVideo")
			->with("featuredVideoThumbnail");
	}


	// Query

	static function getPages()
	{
		return self::all();
	}

	static function getPage($pageId)
	{
		$relation = self::basicRelation();
		return $relation->where("id", $pageId)->first();
	}

	static function getPagesByStatus($status)
	{
		return self::basicRelation()->where("status", $status)->get();
	}

	static function getPageBySlug($pageSlug)
	{
		try {
			return self::where("slug", $pageSlug)->first();
		} catch (Exception $e) {
			return null;
		}
	}

	static function addPage($data)
	{

		return self::create([
			"title" => $data["title"],
			"page_title" => $data["pageTitle"],
			"description" => $data["description"] ?? NULL,
			"slug" => $data["slug"],
			"status" => $data["status"],
			"featured_image" => $data["featuredImageURL"] ?? NULL,
			"featured_video" => $data["featuredVideoURL"] ?? NULL,
			"featured_video_thumbnail" => $data["featuredVideoThumbnailURL"] ?? NULL,
			"meta" => json_encode($data["meta"]),
			"content" => json_encode($data["sections"]),
			"create_datetime" => DateTime::getDateTime(),
		]);
	}

	static function updatePage($pageId, $data)
	{

		$languages = Cache::get("languages");
		$primaryLanguage = NULL;

		foreach ($languages as $language) {
			if ($language["type"] === "primary") $primaryLanguage = $language;
		}

		if ($data["languageCode"] !== NULL && $primaryLanguage["code"] !== $data["languageCode"]) {
			return Pagei18n::savePage($pageId, $data);
		}

		return self::where("id", $pageId)->update([
			"title" => $data["title"],
			"page_title" => $data["pageTitle"],
			"description" => $data["description"] ?? NULL,
			"slug" => $data["slug"],
			"status" => $data["status"],
			"featured_image" => $data["featuredImageURL"] ?? NULL,
			"featured_video" => $data["featuredVideoURL"] ?? NULL,
			"featured_video_thumbnail" => $data["featuredVideoThumbnailURL"] ?? NULL,
			"meta" => json_encode($data["meta"]),
			"content" => json_encode($data["sections"])
		]);
	}

	static function deletePage($pageId)
	{
		return self::where("id", $pageId)->delete();
	}
}
