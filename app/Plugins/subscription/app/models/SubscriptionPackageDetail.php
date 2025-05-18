<?php

namespace App\Plugins\Subscription\Model;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPackageDetail extends Model {

	public $timestamps = false;
	protected $fillable = [
		"name",
		"included",
		"subscription_package_id"
	];

	// Query

	static function saveDetails($subscriptionPackageId, $data){
		self::deleteDetails($subscriptionPackageId);
		$dataToInsert = [];

		foreach($data as $row){
			$dataToInsert[] = [
				"name"=>$row["name"],
				"included"=>$row["included"],
				"subscription_package_id"=>$subscriptionPackageId
			];
		}

		return self::insert($dataToInsert);
	}

	static function deleteDetails($subscriptionPackageId){
		return self::where("subscription_package_id", $subscriptionPackageId)->delete();
	}
}

?>