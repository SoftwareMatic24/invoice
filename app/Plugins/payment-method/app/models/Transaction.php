<?php

namespace App\Plugins\PaymentMethods\Model;

use App\Classes\DateTime;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;


class Transaction extends Model
{

	public $timestamps = false;
	protected $fillable = [
		"uid",
		"customer_name",
		"customer_email",
		"product_name",
		"product_amount",
		"product_quantity",
		"currency",
		"payment_method",
		"type",
		"status",
		"other",
		"user_id",
		"create_datetime"
	];


	// Relation

	function user()
	{
		return $this->belongsTo(User::class, "user_id", "id");
	}


	// Build

	static function basicRelation()
	{
		return self::with(["user" => function ($user) {
			$user->select("id", "first_name", "last_name", "email");
		}]);
	}

	/**
	 * Query: Get
	 */

	static function transactionByUid(string $uid){
		return self::basicRelation()->where('uid', $uid)->first();
	}

	static function trnsactionsByStatus(string $status)
	{
		return self::basicRelation()->where('status', $status)->get();
	}

	static function allTransactions()
	{
		return self::orderBy("id", "desc")->get();
	}

	static function allTransactionsInUserIds($userIds)
	{
		return self::orderBy("id", "desc")->whereIn("user_id", $userIds)->get();
	}

	static function addTransaction($data)
	{
		return self::create([
			"uid" => $data["uid"],
			"customer_name" => $data["customerName"] ?? NULL,
			"customer_email" => $data["customerEmail"] ?? NULL,
			"product_name" => $data["productName"] ?? NULL,
			"product_amount" => $data["productAmount"] ?? NULL,
			"product_quantity" => $data["productQuantity"] ?? NULL,
			"currency" => $data["currency"] ?? NULL,
			"payment_method" => $data["paymentMethod"] ?? NULL,
			"type" => $data["type"] ?? NULL,
			"status" => $data["status"] ?? "pending",
			"other" => $data["other"] ?? NULL,
			"user_id" => $data["user_id"] ?? NULL,
			"create_datetime" => DateTime::getDateTime()
		]);
	}

	static function addBulkTransactions($dataToInsert)
	{
		return self::insert($dataToInsert);
	}
}
