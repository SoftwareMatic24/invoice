<?php

namespace App\Plugins\Subscription\Model;

// require_once __DIR__ . "/SubscriptionPackage.php";
// require_once __DIR__ . "/../../../payment-method/app/models/Transaction.php";

use App\Classes\DateTime;
use App\Models\User;
use App\Plugins\PaymentMethods\Model\Transaction;
use Illuminate\Database\Eloquent\Model;

class SubscriptionSubscribers extends Model
{

	public $timestamps = false;
	protected $fillable = [
		"user_id",
		"subscription_package_id",
		"transaction_id",
		"disable",
		"expiry_datetime",
		"create_datetime"
	];

	// Relation

	function transaction()
	{
		return $this->belongsTo(Transaction::class, "transaction_id", "id");
	}

	function subscriptionPackage()
	{
		return $this->belongsTo(SubscriptionPackage::class, "subscription_package_id", "id");
	}

	function subscriber()
	{
		return $this->belongsTo(User::class, "user_id", "id");
	}

	// Build

	static function basicRelation()
	{
		return self::with("transaction")->with("subscriptionPackage.limits")->with("subscriber");
	}

	// Query: Get

	static function getSubscribers(){
		$relation = self::basicRelation();
		return $relation->orderBy("id","DESC")->get();
	}

	static function getSubscriber($userId){
		$relation = self::basicRelation();
		return $relation->where("user_id",$userId)->first();
	}


	// Query: Save

	static function saveSubscriber($packageId, $transactionId, $userId, $expiryDateTime = NULL, $disable = false){
		$subscriber = self::getSubscriber($userId);
		if($subscriber === NULL) return self::addSubscriber($packageId, $transactionId, $userId, $expiryDateTime, $disable);
		return self::updateSubscriber($userId, $packageId, $transactionId, $expiryDateTime, $disable);
	}

	static function addSubscriber($packageId, $transactionId, $userId, $expiryDateTime = NULL, $disable = false)
	{
		return self::create([
			"user_id" => $userId,
			"subscription_package_id" => $packageId,
			"transaction_id" => $transactionId,
			"disable"=>$disable,
			"expiry_datetime" => $expiryDateTime,
			"create_datetime" => DateTime::getDateTime()
		]);
	}

	static function updateSubscriber($userId, $packageId, $transactionId, $expiryDateTime = NULL, $disable = false)
	{
		return self::where("user_id", $userId)
			->update([
				"subscription_package_id" => $packageId,
				"transaction_id" => $transactionId,
				"disable"=>$disable,
				"expiry_datetime" => $expiryDateTime,
				"create_datetime"=>DateTime::getDateTime()
			]);
	}
}
