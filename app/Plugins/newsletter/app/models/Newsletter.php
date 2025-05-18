<?php

namespace App\Plugins\Newsletter\Model;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Newsletter extends Model {

	public $timestamps = false;
	protected $fillable = [
		"uid",
		"name",
		"email",
		"status",
		"create_datetime",
		"unsubscribe_datetime"
	];

	/**
	 * ===== Query
	 */

	static function getAllNewsletter(){
		return self::orderBy("id", "DESC")->get();
	}

	static function getNewsletter($newsletterId){
		return self::where("id", $newsletterId)->first();
	}

	static function getNewsletterByEmail($email){
		return self::where("email", $email)->first();
	}

	static function addNewsletter($data){

		$uniqueId = Str::uuid($data["email"]);
		
		$dataToAdd = [
			"uid"=>$uniqueId,
			"name"=>$data["name"] ?? "",
			"email"=>$data["email"] ?? "",
			"status"=>$data["status"] ?? "subscribed",
			"create_datetime"=>DateTime::getDateTime()
		];

		if(($data["status"] ?? NULL) === "unsubscribed") $dataToAdd["unsubscribe_datetime"] = DateTime::getDateTime();
		return self::create($dataToAdd);
	}

	static function updateNewsletter($newsletterId, $data){
		$updateData = [
			"name"=>$data["name"] ?? "",
			"email"=>$data["email"] ?? "",
			"status"=>$data["status"] ?? "subscribed"
		];

		if($data["status"] === "unsubscribed") $updateData["unsubscribe_datetime"] = DateTime::getDateTime();
		else $updateData["unsubscribe_datetime"] = NULL;

		self::where("id", $newsletterId)->update($updateData);
		
		return self::getNewsletter($newsletterId);
	}
	
	static function updateStatus($newsletterId, $status){

		$dataToUpdate = [
			"status"=>$status
		];

		if($status === "unsubscribed") $dataToUpdate["unsubscribe_datetime"] = DateTime::getDateTime();
		else $dataToUpdate["unsubscribe_datetime"] = NULL;

		return self::where("id", $newsletterId)->update($dataToUpdate);
	}

	static function deleteNewsletter($newsletterId){
		return self::where("id", $newsletterId)->delete();
	}

}
