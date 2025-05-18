<?php

namespace App\Plugins\Pages\Models;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Model;

class Pagei18n extends Model {

	public $timestamps = false;
	protected $fillable  = [
		"title",
		"page_title",
		"description",
		"content",
		"meta",
		"page_id",
		"language_code",
		"create_datetime"
	];
	protected $table = "pages_i18n";

	// Query
	
	static function getPageByPageIdLanguageCode($pageId, $languageCode){
		return self::where("page_id", $pageId)->where("language_code", $languageCode)->first();
	}

	static function savePage($pageId, $data){
		$record = self::getPageByPageIdLanguageCode($pageId, $data["languageCode"]);

		if($record === NULL){
			return self::create([
				"title"=>$data["title"],
				"page_title"=>$data["pageTitle"],
				"description"=>$data["description"] ?? NULL,
				"meta"=>json_encode($data["meta"]),
				"content"=>json_encode($data["sections"]),
				"page_id"=>$pageId,
				"language_code"=>$data["languageCode"],
				"create_datetime"=>DateTime::getDateTime()
			]);
		}
		else {
			return self::where("page_id",$pageId)->where("language_code", $data["languageCode"])->update([
				"title"=>$data["title"],
				"page_title"=>$data["pageTitle"],
				"description"=>$data["description"] ?? NULL,
				"meta"=>json_encode($data["meta"]),
				"content"=>json_encode($data["sections"])
			]);
		}

	}
}
