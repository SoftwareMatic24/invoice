<?php

namespace App\Models;

use App\Classes\DateTime as MyDateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
	use HasFactory;

	public $timestamps = false;

	protected $fillable = [
		"slug",
		"uid",
		"status",
		"data",
		"create_datetime"
	];


	/**
	 * ===== Query
	 */

	static function getActionBySlugUidStatus($slug,$uid,$status){
		return self::where("slug",$slug)->where("uid",$uid)->where("status",$status)->get();
	}

	static function getActionByUid($uid)
	{
		$action = self::where("uid", $uid)->first();
		if($action === null) return null;
		$action["data"] = json_decode($action["data"], true);
		return $action;
	}

	static function addAction($data)
	{
		$slug = $data["slug"];
		$uid = $data["uid"];
		$status = $data["status"] ?? "pending";
		$data = $data["data"] ?? NULL;

		return self::create([
			"slug" => $slug,
			"uid" => $uid,
			"status" => $status,
			"data" => $data !== NULL ? json_encode($data) : NULL,
			"create_datetime" => MyDateTime::getDateTime()
		]);
	}

	static function updateStatusByUid($uid, $status){
		self::where("uid", $uid)->update([ "status"=>$status ]);
		return ["status"=> "success", "msg"=>"Status updated"];
	}

	static function updateActionStatusBySlugUidStatus($slug,$uid,$status,$newStatus){
		return self::where("slug",$slug)->where("uid",$uid)->where("status",$status)->update(["status"=>$newStatus]);
	}
}
