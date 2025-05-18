<?php

namespace App\Models;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
	use HasFactory;
	public $timestamps = false;
	protected $fillable = [
		"title",
		"link",
		"link_type",
		"meta",
		"create_datetime"
	];


	/**
	 * ===== Relation
	 */

	function userNotifications()
	{
		return $this->hasMany(UserNotification::class, "notification_id", "id");
	}

	function roleNotifications()
	{
		return $this->hasMany(RoleNotification::class, "notification_id", "id");
	}

	function read()
	{
		return $this->hasMany(NotificationRead::class, "notification_id", "id");
	}

	/**
	 * ===== Build Relation
	 */



	/**
	 * ===== Query
	 */



	static function addNotification($data)
	{
		$notification = self::create([
			"title" => $data["title"],
			"link" => $data["link"] ?? NULL,
			"link_type" => $data["linkType"] ?? "none",
			"meta" => json_encode($data["meta"] ?? []),
			"create_datetime" => DateTime::getDateTime()
		]);

		if ($data["notificationFor"] === "users") {
			UserNotification::add($notification->id, $data["userIds"]);
		} else if ($data["notificationFor"] === "roles") {
			RoleNotification::add($notification->id, $data["roles"]);
		}

		return $notification;
	}

	static function getNotificationsByUserAndRole($userId, $roleTitle, $limit = NULL)
	{

		$withUserNotifications = function($query) use ($userId) {
			$query->where("user_id", $userId);
		};

		$withRoleNotifications = function($query) use ($roleTitle) {
			$query->where("role", $roleTitle);
		};

		$withRead = function($query) use($userId) {
			$query->where("user_id", $userId);
		};

		$userNotificationRelation = self::with(["userNotifications" => $withUserNotifications])->has("userNotifications")->with(["read"=>$withRead]);
		$userNotifications = $userNotificationRelation->orderBy("id", "DESC")->limit($limit)->get();

		
		$roleNotificationRelation = self::with(["roleNotifications"=>$withRoleNotifications])->has("roleNotifications")->with(["read"=>$withRead]);
		$roleNotifications = $roleNotificationRelation->orderBy("id", "DESC")->limit($limit)->get();

		$userNotifications = array_filter($userNotifications->toArray(), function($notification){
			if(count($notification["user_notifications"]) > 0) return $notification;
		});

		$roleNotifications = array_filter($roleNotifications->toArray(), function($notification){
			if(count($notification["role_notifications"]) > 0) return $notification;
		});

	
		return [
			"userNotifications"=>array_values($userNotifications),
			"roleNotifications"=>array_values($roleNotifications)
		];
	}

	static function getNotification($notificationId)
	{

		$withUserNotifications = function($query) {};
		$withRoleNotifications = function($query) {};

		$userNotificationRelation = self::with(["userNotifications" => $withUserNotifications])->has("userNotifications");
		$userNotifications = $userNotificationRelation->where("id", $notificationId)->first();

		
		$roleNotificationRelation = self::with(["roleNotifications"=>$withRoleNotifications])->has("roleNotifications");
		$roleNotifications = $roleNotificationRelation->where("id", $notificationId)->first();

		$notification = NULL;

		if($userNotifications !== NULL) $notification = $userNotifications->toArray();
		else if($roleNotifications !== NULL) $notification = $roleNotifications->toArray();
		
		return $notification;
	}

}
