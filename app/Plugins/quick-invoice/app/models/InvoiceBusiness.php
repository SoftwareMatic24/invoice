<?php

namespace App\Plugins\QuickInvoice\Models;

use App\Classes\DateTime;
use App\Plugins\MediaCenter\Models\Media;
use Illuminate\Database\Eloquent\Model;

class InvoiceBusiness extends Model {
	public $timestamps = false;
	protected $fillable = [
		"name",
		"email",
		"country",
		"city",
		"province_state",
		"street",
		"street_2",
		"postcode",
		"telephone",
		"phone",
		"fax",
		"website",
		"business_id",
		"tax_id",
		"trade_register",
		"logo_id",
		"signature_id",
		"added_by",
		"create_datetime",
		"update_datetime"
	];
	protected $table = "businesses";

	// Relation 

	function logo(){
		return $this->belongsTo(Media::class, "logo_id", "id");
	}

	function signature(){
		return $this->belongsTo(Media::class, "signature_id", "id");
	}

	// Build

	static function basicRelation(){
		return self::with("logo")->with("signature");
	}

	// Query: Business

	static function getBusinesses(){
		return self::orderBy("id", "DESC")->get();
	}

	// Query: User Business

	static function userBusiness($userId, $businessId){
		$relation = self::basicRelation();
		return $relation->where("added_by", $userId)->where("id", $businessId)->first();
	}

	static function userBusinesses($userId){
		return self::where("added_by", $userId)->orderBy("id", "DESC")->get();
	}
	

	// Query: Save User Business
	
	static function saveUserBusiness($businessId, $addedBy, $data){
		if($businessId === NULL) {
			$business = self::addUserBusiness($addedBy, $data);
			$businessId = $business->id ?? NULL;
		}
		else {
			$isUpdated = self::updateUserBusiness($businessId, $addedBy, $data);
			if($isUpdated === false) return NULL;
		}
		return $businessId;
	}

	static function addUserBusiness($addedBy, $data){
		return self::create([
			"name"=>$data["name"],
			"email"=>$data["email"] ?? NULL,
			"country"=>$data["country"],
			"city"=>$data["city"] ?? NULL,
			"street"=>$data["street"] ?? NULL,
			"street_2"=>$data["street2"] ?? NULL,
			"postcode"=>$data["postcode"] ?? NULL,
			"province_state"=>$data["province"] ?? NULL,
			"telephone"=>$data["telephone"] ?? NULL,
			"phone"=>$data["phone"] ?? NULL,
			"fax"=>$data["fax"] ?? NULL,
			"website"=>$data["website"] ?? NULL,
			"business_id"=>$data["businessId"] ?? NULL,
			"tax_id"=>$data["taxId"] ?? NULL,
			"trade_register"=>$data["tradeRegister"] ?? NULL,
			"logo_id"=>$data["logoMediaId"] ?? NULL,
			"signature_id"=>$data["signatureMediaId"] ?? NULL,
			"added_by"=>$addedBy,
			"create_datetime"=>DateTime::getDateTime()
		]);
	}

	static function updateUserBusiness($businessId, $addedBy, $data){
		return self::where("id",$businessId)->where("added_by", $addedBy)->update([
			"name"=>$data["name"],
			"email"=>$data["email"] ?? NULL,
			"country"=>$data["country"],
			"city"=>$data["city"] ?? NULL,
			"street"=>$data["street"] ?? NULL,
			"street_2"=>$data["street2"] ?? NULL,
			"postcode"=>$data["postcode"] ?? NULL,
			"province_state"=>$data["province"] ?? NULL,
			"telephone"=>$data["telephone"] ?? NULL,
			"phone"=>$data["phone"] ?? NULL,
			"fax"=>$data["fax"] ?? NULL,
			"website"=>$data["website"] ?? NULL,
			"business_id"=>$data["businessId"] ?? NULL,
			"tax_id"=>$data["taxId"] ?? NULL,
			"trade_register"=>$data["tradeRegister"] ?? NULL,
			"logo_id"=>$data["logoMediaId"] ?? NULL,
			"signature_id"=>$data["signatureMediaId"] ?? NULL,
			"update_datetime"=>DateTime::getDateTime()
		]);
	}


	// Query: Delete User Business

	static function deleteUserBusiness($businessId, $addedBy){
		return self::where("id", $businessId)->where("added_by", $addedBy)->delete();
	}

}


?>