<?php

namespace App\Models;

use App\Classes\DateTime;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lock extends Model
{
	use HasFactory;

	public $timestamps = false;
	protected $fillable = [
		"slug",
		"expiry_datetime",
		"create_datetime"
	];

	// Query

	static function getLock($slug){
		return self::where("slug", $slug)->first();
	}

	static function acquire($slug, $expiryDateTime = NULL){
		try {
			self::create(["slug"=>$slug, "expiry_datetime"=>$expiryDateTime, "create_datetime"=>DateTime::getDateTime()]);
			return true;
		}
		catch(Exception $e){
			return false;
		}
	}

	static function release($slug){
		return self::where("slug", $slug)->delete();
	}

	static function lockStatus($slug){
		$lock = self::where("slug", $slug)->first();
		if($lock === NULL) return false;
		return true;
	}

	static function isExpired($slug){
		$lock = self::getLock($slug);
		if($lock === NULL) return true;
		else if($lock["expiry_datetime"] !== NULL){
			$now = DateTime::getDateTime();
			$expiryDateTime = $lock["expiry_datetime"];
			$expired = DateTime::dateTimeLessThan($expiryDateTime, $now);
			if($expired === true) self::release($slug);
			return $expired;
		}
		
		return false;
	}


}
