<?php

namespace App\Plugins\PaymentMethods\Model;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class UserPaymentMethod extends Model {

	public $timestamps = false;
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
		"user_id",
		"create_datetime",
		"update_datetime"
	];
	protected $casts = [
		"private_key"=>"encrypted"
	];

	function paymentMethod(){
		return $this->belongsTo(PaymentMethod::class, 'payment_method_slug', 'slug');
	}

	// Query: Get Payment Method

	static function getPaymentMethod($entryId){
		return self::where("id", $entryId)->first();
	}

	static function getUserPaymentMethodByUserIdPaymentMethodSlug($userId, $paymentMethodSlug){
		return self::where("payment_method_slug", $paymentMethodSlug)->where("user_id", $userId)->get();
	}

	static function getUserPaymentMethidByIdentifierAndSlug($userId, $identifier, $paymentMethodSlug){
		return self::where("payment_method_slug", $paymentMethodSlug)->where("user_id", $userId)->where("payment_method_identifier", $identifier)->first();
	}

	// Query: Save Payment Method

	static function saveUserPaymentMethod($userId, $entryId, $data){
		if($entryId === NULL){
			$paymentMethod = self::addUserPaymentMethod($userId, $data);
			$entryId = $paymentMethod->id ?? NULL;
		}
		else {
			$isUpdated = self::updateUserPaymentMethod($userId, $data);
			if($isUpdated == 0) return NULL;
		}

		return $entryId;
	}

	static function addUserPaymentMethod($userId, $data){
		return self::create([
			"name"=>$data["name"] ?? NULL,
			"email"=>$data["email"] ?? NULL,
			"status"=>$data["status"] ?? "inactive",
			"payment_method_identifier"=>$data["identifier"],
			"public_key"=>$data["publicKey"] ?? NULL,
			"private_key"=>$data["privateKey"] ?? NULL,
			"other"=> isset($data["other"]) ? json_encode($data["other"]) : NULL,
			"payment_method_slug"=>$data["paymentMethodSlug"],
			"note"=>$data["note"] ?? NULL,
			"user_id"=>$userId,
			"create_datetime"=>DateTime::getDateTime()
		]);
	}

	static function updateUserPaymentMethod($userId, $data){
		return self::where("user_id",$userId)->where("payment_method_slug",$data["paymentMethodSlug"])->update([
			"name"=>$data["name"] ?? NULL,
			"email"=>$data["email"] ?? NULL,
			"status"=>$data["status"] ?? "inactive",
			"payment_method_identifier"=>$data["identifier"],
			"public_key"=>$data["publicKey"] ?? NULL,
			"private_key"=>empty($data["privateKey"]) ? NULL : Crypt::encryptString($data["privateKey"]),
			"other"=> isset($data["other"]) ? json_encode($data["other"]) : NULL,
			"payment_method_slug"=>$data["paymentMethodSlug"],
			"note"=>$data["note"] ?? NULL,
			"update_datetime"=>DateTime::getDateTime()
		]);
	}

	// Query: Delete Payment Method

	static function deletePaymentMethod($entryId){
		return self::where("id", $entryId)->delete();
	}

};

?>