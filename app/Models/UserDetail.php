<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
	use HasFactory;

	public $timestamps = false;
	protected $fillable = [
		"column_name",
		"column_value",
		"user_id"
	];

	// Query

	static function getDetails($userId){
		return self::where("user_id", $userId)->pluck("column_value","column_name");
	}

	static function getDetail($userId, $columnName){
		return self::where("user_id",$userId)->where("column_name",$columnName)->first();
	}

	static function getDetailByNameAndValue($columnName, $columnValue){
		return self::where("column_value",$columnValue)->where("column_name",$columnName)->get();
	}

	static function addDetails($userId, $data){

		$dataToInsert = [];
		foreach($data as $key=>$value){
			$dataToInsert[] = [
				"column_name"=>$key,
				"column_value"=>$value,
				"user_id"=>$userId
			];
		}

		return self::insert($dataToInsert);
	}

	static function updateDetails($userId, $data){
		self::deleteDetails($userId);
		return self::addDetails($userId, $data);
	}

	static function deleteDetails($userId){
		return self::where("user_id",$userId)->delete();
	}

	static function updateDetail($userId, $columnName, $value){
		$row = self::getDetail($userId, $columnName);

		if($row === NULL){
			return self::create([
				"column_name"=>$columnName,
				"column_value"=>$value,
				"user_id"=>$userId
			]);
		}
		else {
			return self::where("user_id", $userId)->where("column_name", $columnName)->update([
				"column_value"=>$value,
			]);
		}
	}

}
