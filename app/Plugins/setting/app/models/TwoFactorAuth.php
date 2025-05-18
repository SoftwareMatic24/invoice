<?php

namespace App\Plugins\Setting\Model;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Model;

class TwoFactorAuth extends Model {

	public $timestamps = false;
	protected $fillable = [
		"status",
		"code",
		"user_id",
		"code_create_datetime",
		"create_datetime"
	];


	// Query

	static function get2FaForUser($userId){
		return self::where("user_id",$userId)->first();
	}

	static function addCode($userId, $code){

		$twoFa = self::get2FaForUser($userId);
		if($twoFa === NULL) return false;

		return self::where("user_id",$userId)->update([
			"code"=>$code,
			"code_create_datetime"=>DateTime::getDateTime()
		]);
	}

	static function resetCodeForUser($userId){
		return self::where("user_id", $userId)->update([
			"code"=>NULL
		]);
	}

	static function change2FaStatus($userId, $status){
		$entry = self::get2FaForUser($userId);

		if($entry === NULL) {
			return self::create([
				"code"=>NULL,
				"user_id"=>$userId,
				"status"=>$status,
				"create_datetime"=>DateTime::getDateTime()
			]);
		}
		else {
			return self::where("user_id",$userId)->update([
				"status"=>$status,
			]);
		}
	}
	

}

?>