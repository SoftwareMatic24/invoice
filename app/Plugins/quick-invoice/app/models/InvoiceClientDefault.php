<?php

namespace App\Plugins\QuickInvoice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InvoiceClientDefault extends Model {
	public $timestamps = false;
	protected $fillable = [
		"discount",
		"discount_type",
		"payment_method",
		"currency_code",
		"salutation",
		"note",
		"client_id"
	];
	protected $table = "client_defaults";

	static function addDefaults($clientId, $data){
		self::deleteDefaults($clientId);

		self::create([
			"discount"=>$data["discount"] ?? 0,
			"discount_type"=>$data["discountType"] ?? 0,
			"payment_method"=>$data["paymentMethod"] ?? NULL,
			"currency_code"=>$data["currency"] ?? NULL,
			"salutation"=>$data["salutation"] ?? NULL,
			"note"=>$data["note"] ?? NULL,
			"client_id"=>$clientId
		]);
	}

	static function deleteDefaults($clientId){
		return self::where("client_id", $clientId)->delete();
	}

}

?>