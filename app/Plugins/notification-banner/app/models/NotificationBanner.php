<?php

namespace App\Plugins\NotificationBanner\Models;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Model;

class NotificationBanner extends Model {

	public $timestamps = false;
	protected $fillable = [
		"text",
		"status",
		"type",
		"style",
		"create_datetime"
	];


	// Query

	static function getNotificationBanners($status = "all", $type = 'all'){
		$relation = self::orderBy("id", "DESC");
		if($status !== "all") $relation->where("status", $status);
		if($type !== 'all') $relation->where('type', $type);
		return $relation->get();
	}

	static function getNotificationBanner($id){
		return self::where("id", $id)->orderBy("id", "DESC")->first();
	}

	static function addNotificationBanner($data){

		$style = [];
		if($data["bgColor"] ?? false) $style["bgColor"] = $data["bgColor"];
		if($data["color"] ?? false) $style["color"] = $data["color"];
		if(sizeof($style) <= 0) $style = NULL;

		return self::create([
			"text"=>$data["text"] ?? NULL,
			"type"=>$data["type"] ?? "portal",
			"status"=>$data["status"] ?? "active",
			"style"=>json_encode($style) ?? NULL,
			"create_datetime"=>DateTime::getDateTime()
		]);
	}

	static function updateNotificaionBanner($id, $data){
		
		$style = [];
		if($data["bgColor"] ?? false) $style["bgColor"] = $data["bgColor"];
		if($data["color"] ?? false) $style["color"] = $data["color"];
		if(sizeof($style) <= 0) $style = NULL;

		return self::where("id", $id)->update([
			"text"=>$data["text"] ?? NULL,
			"type"=>$data["type"] ?? "portal",
			"status"=>$data["status"] ?? "active",
			"style"=>json_encode($style) ?? NULL,
			"create_datetime"=>DateTime::getDateTime()
		]);
	}

	static function deleteNotificationBanner($id){
		return self::where("id", $id)->delete();
	}

}

?>