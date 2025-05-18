<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CronJob extends Model
{
	use HasFactory;

	public $timestamps = false;
	protected $fillable = [
		"title",
		"slug",
		"status",
		"run_every_seconds",
		"last_run_date_time"
	];

	// Query: Get

	static function getCronJob($slug){
		return self::where("slug", $slug)->first();
	}

	static function getCronJobs(){
		return self::get()->keyBy("slug");
	}

	// Query: Update

	static function updateStatus($slug, $status){
		return self::where("slug", $slug)->update([
			"status"=>$status
		]);
	}

	static function updateLastRunDateTime($slug, $dateTime){
		return self::where("slug", $slug)->update([
			"last_run_date_time"=>$dateTime
		]);
	}


}
