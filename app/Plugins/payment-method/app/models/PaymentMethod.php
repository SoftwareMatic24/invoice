<?php

namespace App\Plugins\PaymentMethods\Model;


use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model {

	public $timestamps = false;
	
	protected $fillable = [
		"title",
		"slug",
		"image",
		"create_datetime",
		"update_datetime"
	];


	// Query

	static function paymentMethods(){
		return self::get();
	}
	

}

?>