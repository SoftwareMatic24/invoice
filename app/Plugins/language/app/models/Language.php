<?php

namespace App\Plugins\Language\Models;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Model;

class Language extends Model {

	public $timestamps = false;
	protected $fillable = [
		"name",
		"code",
		"status",
		"type",
		"direction",
		"create_datetime"
	];


	// Query

	static function getLanguages($status = "all", $options = []){
		$relation = self::orderBy("id", "ASC");

		$columns = $options["columns"] ?? NULL;
		if($status !== "all") $relation = $relation->where("status", $status);
		if($columns !== NULL) $relation->select($columns);

		return $relation->get();
	}

	static function getLanguage($id){
		return self::where("id", $id)->first();
	}

	static function getLanguageByCode($code){
		return self::where("code", $code)->first();
	}

	static function addLanguage($data){

		if(($data["type"] ?? "secondary") === "primary") self::setPrimaryLanguageTo("secondary");

		return self::create([
			"name"=>$data["name"],
			"code"=>$data["code"],
			"status"=>$data["status"] ?? "active",
			"type"=>$data["type"] ?? "secondary",
			"direction"=>$data["direction"] ?? "ltr",
			"create_datetime"=>DateTime::getDateTime()
		]);
	}

	static function updateLanguageByCode($whereCode, $data){

		if(($data["type"] ?? "secondary") === "primary") self::setPrimaryLanguageTo("secondary");

		return self::where("code", $whereCode)->update([
			"name"=>$data["name"],
			"code"=>$data["code"],
			"status"=>$data["status"] ?? "active",
			"direction"=>$data["direction"] ?? "ltr",
			"type"=>$data["type"] ?? "secondary",
		]);
	}

	static function deleteLanguageByCode($code){
		return self::where("code", $code)->delete();
	}

	static function setPrimaryLanguageTo($type){
		return self::where("type", "primary")->update([
			"type"=>$type
		]);
	}

	static function setFirstRecordToPrimary(){
		$record = self::first();
		if($record){
			$record->update([
				"type"=>"primary"
			]);
		}
	}

}

?>