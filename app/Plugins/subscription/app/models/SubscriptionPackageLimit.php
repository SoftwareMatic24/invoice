<?php

namespace App\Plugins\Subscription\Model;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPackageLimit extends Model {

	public $timestamps = false;
	protected $fillable = [
		"slug",
		"limit",
		"subscription_package_id"
	];


	// Query

	static function saveLimits($subscriptionPackageId = NULL, $data){
		self::deleteLimits($subscriptionPackageId);
		$dataToInsert = [];

		foreach($data as $row){
			$dataToInsert[] = [
				"slug"=>$row["name"],
				"limit"=>$row["value"],
				"subscription_package_id"=>$subscriptionPackageId
			];
		}

		return self::insert($dataToInsert);
	}

	static function deleteLimits($subscriptionPackageId){
		return self::where("subscription_package_id", $subscriptionPackageId)->delete();
	}

}

?>