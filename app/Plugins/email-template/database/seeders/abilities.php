<?php

use Illuminate\Support\Facades\DB;

return new Class {

	public function run(){
		$abilities = [
			"view-email-template",
			"update-email-template"
			
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