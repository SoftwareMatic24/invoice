<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
	use HasFactory;

	public $timestamps = false;
	protected $fillable = [
		"column_name",
		"column_value",
		"user_id"
	];

	// Query: Get

	static function getUserSettings($userId){
		return self::where("user_id", $userId)->get()->KeyBy("column_name");
	}

	static function getUserSetting($columnName, $userId){
		return self::where("column_name", $columnName)->where("user_id", $userId)->first();
	}

	static function getSettingByColumnNameAndValue($columnName, $columnValue){
		return self::where("column_name", $columnName)
		->where("column_value",$columnValue)->get()->keyBy("user_id");
	}

	// Query: Save

	static function saveUserSetting($columnName, $columnValue, $userId){
		$setting = self::getUserSetting($columnName, $userId);
		if($setting === NULL) $setting = self::addUserSetting($columnName, $columnValue, $userId);
		self::updateUserSetting($columnName, $columnValue, $userId);
		return $setting;
	}

	static function addUserSetting($columnName, $columnValue, $userId){
		return self::create([
			"column_name"=>$columnName,
			"column_value"=>$columnValue,
			"user_id"=>$userId
		]);
	}

	static function updateUserSetting($columnName, $columnValue, $userId){
		return self::where("column_name", $columnName)
		->where("user_id", $userId)
		->update(["column_value"=>$columnValue]);
	}

}
