<?php

use Illuminate\Support\Facades\DB;

return new class {

	public function run(){

		$abilities = [
			"view-contact-message",
			"delete-contact-message"
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