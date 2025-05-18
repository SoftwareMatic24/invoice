<?php

namespace App\Plugins\Setting\Model;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model {

	public $timestamps = false;
	protected $fillable = [
		"column_name",
		"column_value"
	];


	/**
	 * ===== Query
	 */

	static function getSettings(){
		return self::get()->keyBy("column_name");
	}

	static function getSetting($columnName){
		try {
			return self::where("column_name", $columnName)->first();
		}
		catch(Exception $e) {
			return NULL;
		}
	}

	static function saveSetting($columnName, $columnValue){

		Cache::forget("settings");

		$oldSetting = self::getSetting($columnName);
		if($oldSetting === NULL) return self::addSetting($columnName, $columnValue);
		else return self::updateSetting($columnName, $columnValue);
	}

	static function addSetting($columnName, $columnValue){
		return self::create([
			"column_name"=>$columnName,
			"column_value"=>$columnValue
		]);
	}

	static function updateSetting($columnName, $columnValue){
		return self::where("column_name", $columnName)->update([
			"column_value"=>$columnValue
		]);
	}

}
