<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLogDetails extends Model
{
	use HasFactory;

	public $timestamps = false;
	protected $fillable = [
		"column_name",
		"colun_value",
		"activity_log_id"
	];


	/**
	 * ===== Query
	 */

	static function addDetail($activityLogId, $data = []){

		$dataToAdd = [];

	
		foreach($data as $key=>$value){
			$dataToAdd[] = [
				"column_name"=>$key,
				"column_value"=>$value,
				"activity_log_id"=>$activityLogId
			];
		}
		
		self::insert($dataToAdd);

		return true;
	}

}
