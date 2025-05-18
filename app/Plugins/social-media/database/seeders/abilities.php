<?php

use Illuminate\Support\Facades\DB;

return new class {
	public function run(){

		$abilities = [
			"view-social-media",
			"view-social-media-links",
			"add-social-media-links",
			"update-social-media-links",
			"delete-social-media-links"
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