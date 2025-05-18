<?php

namespace App\Plugins\PaymentMethods\Model;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SystemPaymentMethod extends Model
{
	public $timestamps = false;
	protected $casts = [
		"private_key"=>"encrypted"
	];
	protected $fillable = [
		"name",
		"email",
		"payment_method_identifier",
		"status",
		"public_key",
		"private_key",
		"other",
		"payment_method_slug",
		"note",
		"create_datetime",
		"update_datetime"
	];


	// Query: Get Payment Method

	static function getPaymentMethod($entryId)
	{
		return self::where("id", $entryId)->first();
	}

	static function getSystemPaymentMethodByPaymentMethodSlug($paymentMethodSlug)
	{
		return self::where("payment_method_slug", $paymentMethodSlug)->get();
	}

	static function getPaymentMethodByIdentifierAndSlug($identifier, $paymentMethodSlug)
	{
		return self::where("payment_method_identifier", $identifier)->where("payment_method_slug", $paymentMethodSlug)->first();
	}

	// Query: Save Payment Method

	static function savePaymentMethod($entryId, $data)
	{
		if ($entryId === NULL) {
			$paymentMethod = self::addPaymentMethod($data);
			$entryId = $paymentMethod->id ?? NULL;
		} else {
			$isUpdated = self::updatePaymentMethod($entryId, $data);
			if ($isUpdated == 0) return NULL;
		}
		return $entryId;
	}

	static function addPaymentMethod($data)
	{
		return self::create([
			"name" => $data["name"] ?? NULL,
			"email" => $data["email"] ?? NULL,
			"status" => $data["status"] ?? "inactive",
			"payment_method_identifier" => $data["identifier"],
			"public_key" => $data["publicKey"] ?? NULL,
			"private_key" => $data["privateKey"] ?? NULL,
			"other" => isset($data["other"]) ? json_encode($data["other"]) : NULL,
			"payment_method_slug" => $data["paymentMethodSlug"],
			"note" => $data["note"] ?? NULL,
			"create_datetime" => DateTime::getDateTime()
		]);
	}

	static function updatePaymentMethod($entryId, $data)
	{
		return self::where("payment_method_slug", $data["paymentMethodSlug"])->where("id", $entryId)->update([
			"name" => $data["name"] ?? NULL,
			"email" => $data["email"] ?? NULL,
			"status" => $data["status"] ?? "inactive",
			"payment_method_identifier" => $data["identifier"],
			"public_key" => $data["publicKey"] ?? NULL,
			"private_key" => empty($data["privateKey"]) ? NULL : Crypt::encryptString($data["privateKey"]),
			"other" => isset($data["other"]) ? json_encode($data["other"]) : NULL,
			"payment_method_slug" => $data["paymentMethodSlug"],
			"note" => $data["note"] ?? NULL,
			"update_datetime" => DateTime::getDateTime()
		]);
	}

	// Query: Delete Payment Method

	static function deletePaymentMethod($entryId)
	{
		return self::where("id", $entryId)->delete();
	}
}
