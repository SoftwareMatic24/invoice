<?php

use App\Classes\DateTime;
use Illuminate\Support\Facades\DB;

return new class
{
	public function run()
	{
		$lang = [
			[
				"name"=>"English",
				"code"=>"en",
				"status"=>"active",
				"type"=>"primary",
				"direction"=>"ltr",
				"create_datetime"=>DateTime::getDateTime()
			],
			[
				"name"=>"German",
				"code"=>"de",
				"status"=>"active",
				"type"=>"secondary",
				"direction"=>"ltr",
				"create_datetime"=>DateTime::getDateTime()
			]
		];

		DB::table("languages")->insert($lang);
	}

	public function bridge(){

		$settingsFunc = function(){

			$settings = [
				[
					"column_name"=>"portal-lang",
					"column_value"=>"en"
				]
			];

			DB::table("settings")->insert($settings);
		};

		return [
			[
				"dirs"=>["Plugins/setting/database/seeders"],
				"seeds"=>[$settingsFunc]
			]
		];

	}

};
