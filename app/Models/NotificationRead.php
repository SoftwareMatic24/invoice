<?php

namespace App\Models;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationRead extends Model
{
	use HasFactory;

	public $timestamps = false;
	protected $fillable = [
		"user_id",
		"notification_id",
		"create_datetime"
	];


	/**
	 * ===== Query
	 */

	static function getReadByUserIdNotificationId($userId, $notificationId){
		return self::where("user_id", $userId)->where("notification_id", $notificationId)->first();
	}

	static function addRead($userId, $notificationId){
		return self::create([
			"user_id"=>$userId,
			"notification_id"=>$notificationId,
			"create_datetime"=>DateTime::getDateTime()
		]);
	}

}
