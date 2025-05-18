<?php

namespace App\Plugins\Tax\Models;

use Illuminate\Database\Eloquent\Model;

class TaxSetting extends Model {

	public $timestamps = false;
	protected $fillable = [
		"column_name",
		"column_value",
		"user_id"
	];

	// Query

	static function getSettings(){
		$formattedSettings = [];
		$settings =  self::get();

		foreach($settings as $row){
			if(!isset($formattedSettings[$row["user_id"]])) $formattedSettings[$row["user_id"]] = [];
			$formattedSettings[$row["user_id"]][$row["column_name"]] = $row["column_value"];
		}

		return $formattedSettings;
	}

	static function getSettingsByUserId($userId){
		return self::where("user_id", $userId)->get();
	}

	static function getSettingByUserIdColumnName($userId, $columnName){
		return self::where("user_id", $userId)->where("column_name", $columnName)->first();
	}

	static function saveSetting($userId, $columnName, $columnValue){
		$setting = self::getSettingByUserIdColumnName($userId, $columnName);
		if($setting === NULL) return self::addSetting($userId, $columnName, $columnValue);
		return self::updateSetting($userId, $columnName, $columnValue);
	}

	static function addSetting($userId, $columnName, $columnValue){
		return self::create([
			"column_name"=>$columnName,
			"column_value"=>$columnValue,
			"user_id"=>$userId
		]);
	}

	static function updateSetting($userId, $columnName, $columnValue){
		return self::where("user_id",$userId)->where("column_name",$columnName)->update([
			"column_value"=>$columnValue
		]);
	}


}

?>