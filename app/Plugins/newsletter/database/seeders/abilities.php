<?php

use Illuminate\Support\Facades\DB;

return new class {

	public function run(){

		$abilities = [
			"view-newsletter",
			"add-newsletter",
			"update-newsletter",
			"delete-newsletter"
		];

		foreach($abilities as $ability){

			DB::table("abilities")->insert([
				"ability"=>$ability,
				"role_title"=>"admin"
			]);
		}

	}

}

?>