<?php

namespace App\Plugins\MediaCenter\Models;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{

	public $timestamps = false;
	protected $fillable = [
		"url",
		"thumbnail",
		"type",
		"options",
		"user_id",
		"folder_id",
		"private",
		"create_datetime",
		"update_datetime"
	];


	/**
	 * ===== Relation
	 */

	function folder()
	{
		return $this->belongsTo(MediaCenterFolder::class, "folder_id", "id");
	}
	

	/**
	 * ===== Query
	 */
	
	static function getMedia($id){
		return self::where("id", $id)->first();
	}

	static function getMediaByIds($ids){
		return self::whereIn("id", $ids)->get();
	}

	static function getMediaByUrl(string $url){
		return self::where('url', $url)->first();
	}

	static function getUserMedia($userid, $id){
		return self::where("id", $id)->where("user_id", $userid)->first();
	}

	static function getFolderMedia($folderId){
		return self::where("folder_id", $folderId)->get();
	 }

	static function addMedia($data){
		
		$options = NULL;
		if(isset($data["options"])) $options = json_encode($data["options"]);

		return self::create([
			"url"=>$data["url"],
			"thumbnail"=>$data["thumbnail"] ?? NULL,
			"type"=>$data["type"],
			"options"=>$options,
			"user_id"=>$data["user_id"],
			"folder_id"=>$data["folder_id"],
			"private"=>$data["private"] ?? false,
			"create_datetime"=>$data["create_datetime"] ?? DateTime::getDateTime(),
			"update_datetime"=>$data["update_datetime"] ?? NULL
		]);

	}

	static function updateColumn($id, $columnName, $columnValue){
		return self::where("id", $id)->update([
			$columnName=>$columnValue
		]);
	}

	static function updateOptions($id, $options){
		if($options !== NULL) $options = json_encode($options);
		return self::where("id", $id)->update(["options"=>$options]);
	}

	static function changeMediaFolder($fromFolder, $toFolderId, $mediaIds){
		if(sizeof($mediaIds) > 0) return self::whereIn("id", $mediaIds)->update(["folder_id"=>$toFolderId]);
		else return self::where("folder_id", $fromFolder)->update(["folder_id"=>$toFolderId]);
	}

	static function deleteMedia($ids) {
		return self::whereIn("id", $ids)->delete();
	}

};
