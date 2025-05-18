<?php

use App\Classes\DateTime;
use Illuminate\Support\Facades\DB;

return new class
{
	public function run()
	{
		$currency = [
			[
				"currency" => "USD",
				"symbol" => "$",
				"type" => "primary",
				"rate" => 1,
				"create_datetime" => DateTime::getDateTime()
			],
			[
				"currency" => "EUR",
				"symbol" => "€",
				"type" => "secondary",
				"rate" => 0.90,
				"create_datetime" => DateTime::getDateTime()
			]
		];

		DB::table("currencies")->insert($currency);
	}
};
