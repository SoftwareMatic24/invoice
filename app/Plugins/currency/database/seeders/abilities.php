<?php

use App\Classes\DateTime;
use Illuminate\Support\Facades\DB;

return new class
{
	public function run()
	{
		$abilities = [
			"view-currency",
			"add-currency",
			"update-currency",
			"delete-currency"
		];

		foreach($abilities as $ability){
			DB::table("abilities")->insert([
				"ability"=>$ability,
				"role_title"=>"admin"
			]);
		}
	}
};
