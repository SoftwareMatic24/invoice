<?php

namespace App\Plugins\Subscription\Model;


use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPackage extends Model
{

	public $timestamps = false;
	protected $fillable = [
		"title",
		"description",
		"price",
		"status",
		"user_id",
		'classification_id',
		"create_datetime",
		"update_datetime"
	];


	// Relation

	function classification()
	{
		return $this->belongsTo(SubscriptionPackageClassification::class, 'classification_id', 'id');
	}

	function details()
	{
		return $this->hasMany(SubscriptionPackageDetail::class, "subscription_package_id", "id");
	}

	function limits()
	{
		return $this->hasMany(SubscriptionPackageLimit::class, "subscription_package_id", "id");
	}

	// Build

	static function basicRelation()
	{
		return self::with("details")
			->with("limits")
			->with('classification');
	}

	// Query

	static function getPackages()
	{
		$relation = self::basicRelation();
		return $relation->get();
	}

	static function getPackagesByStatus($status)
	{
		$relation = self::basicRelation();
		return $relation->where("status", $status)->get();
	}

	static function getPackage($packageId)
	{
		$relation = self::basicRelation();
		return $relation->where("id", $packageId)->first();
	}

	static function addPackage($userId, $data)
	{
		$package =  self::create([
			"title" => $data["title"],
			"description" => $data["description"] ?? NULL,
			"price" => $data["price"],
			"status" => $data["status"] ?? "active",
			"user_id" => $userId,
			"classification_id" => $data['classificationId'],
			"create_datetime" => DateTime::getDateTime()
		]);

		SubscriptionPackageDetail::saveDetails($package["id"], $data["items"] ?? []);
		SubscriptionPackageLimit::saveLimits($package["id"], $data["limits"] ?? []);

		return $package;
	}

	static function updatePackage($packageId, $data)
	{
		self::where("id", $packageId)->update([
			"title" => $data["title"],
			"description" => $data["description"] ?? NULL,
			"price" => $data["price"],
			"status" => $data["status"] ?? "active",
			"classification_id" => $data['classificationId'],
			"update_datetime" => DateTime::getDateTime()
		]);

		SubscriptionPackageDetail::saveDetails($packageId, $data["items"] ?? []);
		SubscriptionPackageLimit::saveLimits($packageId, $data["limits"] ?? []);

		return $packageId;
	}

	static function deletePackage($packageId)
	{
		return self::where("id", $packageId)->delete();
	}
}
