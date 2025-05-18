<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleNotification extends Model
{
	use HasFactory;
	public $timestamps = false;
	protected $fillable = [
		"notification_id",
		"role"
	];


	/**
	 * ===== Query
	 */

	 static function add($notificationId, $roles)
	 {
		$dataToAdd = [];
		foreach($roles as $role){
			$dataToAdd[] = [
				"notification_id"=>$notificationId,
				"role"=>$role
			];
		}
		return self::insert($dataToAdd);
	 }

}
