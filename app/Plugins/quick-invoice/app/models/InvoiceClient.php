<?php

namespace App\Plugins\QuickInvoice\Models;

use App\Classes\DateTime;
use App\Models\User;
use App\Plugins\QuickInvoice\Models\InvoiceClientDefault as ModelsInvoiceClientDefault;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvoiceClientDefault;

class InvoiceClient extends Model {

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
		"registration_number",
		"registration_number_2",
		"tax_number",
		"added_by",
		"create_datetime",
		"update_datetime"
	];
	protected $table = "clients";
	
	// Relation

	function addedBy(){
		return $this->belongsTo(User::class, "added_by", "id");
	}

	function default(){
		return $this->hasOne(ModelsInvoiceClientDefault::class, "client_id", "id");
	}

	// Build

	static function basicRelation(){
		return self::with("default");
	}

	// Query: User Client

	static function userClient($userId, $clientId){
		$relation = self::basicRelation();
		return $relation->where("added_by", $userId)->where("id", $clientId)->first();
	}

	static function userClients($userId){
		$relation = self::basicRelation();
		return $relation->where("added_by", $userId)->orderBy("id", "DESC")->get();
	}

	// Query: Delete User Client

	static function deleteUserClient($userId, $clientId){
		return self::where("added_by", $userId)->where("id", $clientId)->delete();
	}

	// Query: Save Client

	static function saveClient($clientId, $addedBy = NULL, $data){
		$defaults = [
			[
				"discountType"=>$data["default"]["discountType"] ?? "percentage",
				"discount"=>$data["default"]["discount"] ?? 0,
				"payment_method"=>$data["default"]["paymentMethod"] ?? NULL,
				"currency_code"=>$data["default"]["currency"] ?? NULL,
				"salutation"=>$data["default"]["salutation"] ?? NULL,
				"note"=>$data["default"]["note"] ?? NULL
			]
		];

		DB::beginTransaction();
		try {
			if($clientId === NULL){
				$client = self::addClient($addedBy, $data);
				$client->default()->createMany($defaults);
				$clientId = $client->id;
			}
			else {
				self::updateClient($clientId, $data);
				ModelsInvoiceClientDefault::addDefaults($clientId, $data["default"] ?? []);
			}
			DB::commit();
			return $clientId;
		}
		catch(Exception $e){
			print('<pre>'.print_r($e->getMessage(),true).'</pre>');
			DB::rollBack();
			return false;
		}
	}

	static function addClient($addedBy = NULL, $data){
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
			"registration_number"=>$data["registrationNumber"] ?? NULL,
			"registration_number_2"=>$data["registrationNumber2"] ?? NULL,
			"tax_number"=>$data["taxNumber"] ?? NULL,
			"added_by"=>$addedBy,
			"create_datetime"=>DateTime::getDateTime()
		]);
	}

	static function updateClient($clientId, $data){
		return self::where("id", $clientId)->update([
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
			"registration_number"=>$data["registrationNumber"] ?? NULL,
			"registration_number_2"=>$data["registrationNumber2"] ?? NULL,
			"tax_number"=>$data["taxNumber"] ?? NULL,
			"update_datetime"=>DateTime::getDateTime()
		]);
	}

	
}

?>