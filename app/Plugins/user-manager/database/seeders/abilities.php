<?php

use Illuminate\Support\Facades\DB;

return new class
{

	public function run()
	{
		$abilities = [
			"view-accounts",
			"add-account",
			"update-account",
			"delete-account",
			"view-roles",
			"add-role",
			"update-role",
			"delete-role",
			"manage-abilities"
		];

		foreach ($abilities as $ability) {
			DB::table("abilities")->insert([
				"ability" => $ability,
				"role_title" => "admin"
			]);
		}
	}
};
