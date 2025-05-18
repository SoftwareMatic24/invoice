<?php

use App\Classes\DateTime;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new Class {

	public function run(){

		$paymentMethods = [
			[
				"title"=>"Cash on Delivery",
				"slug"=>"cod",
				"image"=>"cod.png",
				"create_datetime"=>DateTime::getDateTime()
			],
			[
				"title"=>"PayPal",
				"slug"=>"paypal",
				"image"=>"paypal.png",
				"create_datetime"=>DateTime::getDateTime()
			],
			[
				"title"=>"Stripe",
				"slug"=>"stripe",
				"image"=>"stripe.png",
				"create_datetime"=>DateTime::getDateTime()
			],
			[
				"title"=>"Bank Transfer",
				"slug"=>"bank-transfer",
				"image"=>"bank.png",
				"create_datetime"=>DateTime::getDateTime()
			],
		];

		$paymentMethodIdentifiers = [
			[
				"slug"=>"default",
				"description"=>NULL
			]
		];



		
		DB::table("payment_methods")->insert($paymentMethods);
		DB::table("payment_method_identifiers")->insert($paymentMethodIdentifiers);
		
	}
}

?>