<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
	use HasFactory;

	public $timestamps = false;
	protected $fillable = [
		"notification_id",
		"user_id"
	];


	/**
	 * ===== Query
	 */

	static function add($notificationId, $userIds)
	{
		$dataToAdd = [];
		foreach($userIds as $id){
			$dataToAdd[] = [
				"notification_id"=>$notificationId,
				"user_id"=>$id
			];
		}
		return self::insert($dataToAdd);
	}
}
