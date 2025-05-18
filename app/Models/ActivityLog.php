<?php

namespace App\Models;

require_once __DIR__."/ActivityLogDetails.php";

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
	use HasFactory;

	public $timestamps = false;

	protected $fillable = [
		"title",
		"slug",
		"ip",
		"user_id",
		"create_datetime"
	];

	protected $hidden = [
		"ip"
	];

	// Relation

	function detail(){
		return $this->hasMany(ActivityLogDetails::class, "activity_log_id", "id");
	}

	function user(){
		return $this->belongsTo(User::class, "user_id", "id");
	}

	static function basicRelation(){
		return self::with("detail")->with(["user"=>function($user){
			return $user->select("id", "first_name", "last_name", "email", "role_title");
		}]);
	}

	// Query: Save

	static function addActivityLog($title, $slug, $user_id = NULL, $data = []){

		$ip = request()->ip();
		
		$log = self::create([
			"title"=>$title,
			"slug"=>$slug,
			"ip"=>$ip,
			"user_id"=>$user_id,
			"create_datetime"=>DateTime::getDateTime()
		]);

		ActivityLogDetails::addDetail($log->id, $data);

		return $log;
	}

	// Query: Get

	static function getLogsBySlug($slug){
		return self::basicRelation()->where("slug",$slug)->orderBy("id", "DESC")->get();
	}

	static function getLogsLikeSlug($slug){
		return self::basicRelation()->where("slug","LIKE", "%$slug%")->orderBy("id", "DESC")->get();
	}


}
