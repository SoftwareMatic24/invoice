<?php

use Illuminate\Support\Facades\DB;

return new class
{
	public function run()
	{
		$abilities = [
			[
				"ability"=>"view-language",
				"role_title"=>"admin"
			],
			[
				"ability"=>"add-language",
				"role_title"=>"admin"
			],
			[
				"ability"=>"update-language",
				"role_title"=>"admin"
			],
			[
				"ability"=>"delete-language",
				"role_title"=>"admin"
			],
			[
				"ability"=>"language-settings",
				"role_title"=>"admin"
			]
		];

		DB::table("abilities")->insert($abilities);
	}
};
