<?php

use Illuminate\Support\Facades\DB;

return new class {
	public function run()
	{
		$branding = [
			[
				"column_name" => "brand-name",
				"column_value" => NULL,
			],
			[
				"column_name" => "brand-about",
				"column_value" => NULL,
			],
			[
				"column_name" => "brand-logo",
				"column_value" => NULL
			],
			[
				"column_name" => "brand-logo-light",
				"column_value" => NULL
			],
			[
				"column_name" => "brand-fav-icon",
				"column_value" => NULL
			],
			[
				"column_name" => "brand-portal-logo",
				"column_value" => NULL
			],
			[
				"column_name" => "account-page-description",
				"column_value" => "Join us for a seamless online experience. Access your account effortlessly. Stay secure and enjoy a hassle-free journey."
			],
			[
				"column_name" => "account-page-image",
				"column_value" => NULL
			],
		];
		DB::table("branding")->insert($branding);
	}
};
