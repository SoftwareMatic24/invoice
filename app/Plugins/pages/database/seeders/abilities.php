<?php

use Illuminate\Support\Facades\DB;

return new class {

	public function run(){

		$abilities = [
			"view-page",
			"add-page",
			"update-page",
			"delete-page"
		];

		foreach($abilities as $ability){
			DB::table("abilities")->insert([
				"ability"=>$ability,
				"role_title"=>"admin"
			]);
		}

	}

};

?>