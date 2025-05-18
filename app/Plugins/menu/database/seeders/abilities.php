<?php

use Illuminate\Support\Facades\DB;

return new class {
	public function run(){

		$abilities = [
			"view-menu",
			"add-menu",
			"update-menu",
			"delete-menu"
		];

		foreach($abilities as $ability) {
			DB::table("abilities")->insert([
				"ability"=>$ability,
				"role_title"=>"admin"
			]);
		}

	}
}

?>