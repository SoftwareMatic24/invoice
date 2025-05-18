<?php

namespace App\Plugins\Stripe\Model;

use Illuminate\Database\Eloquent\Model;

class Stripe extends Model {
	
	public $timestamps = false;
	protected $fillable = [
		"email",
		"status",
		"public_key",
		"private_key"
	];
	protected $table = "stripe";

	// Query

	static function getActiveStripe(){
		return self::where("status","active")->first();
	}

}

?>