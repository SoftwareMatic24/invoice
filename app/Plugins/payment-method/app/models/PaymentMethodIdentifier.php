<?php

namespace App\Plugins\PaymentMethods\Model;

use Illuminate\Database\Eloquent\Model;

class PaymentMethodIdentifier extends Model {
	public $timestamps = false;
	protected $fillable = [
		"slug",
		"description"
	];
	protected $table = "payment_method_identifiers";

	// Query: Get

	static function getIdentifiers(){
		return self::get();
	}

}

?>