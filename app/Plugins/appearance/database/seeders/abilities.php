<?php

use Illuminate\Support\Facades\DB;

return new class {
	public function run(){

		$abilities = [
			[
				"ability"=>"appearance",
				"role_title"=>"admin"
			],
			[
				"ability"=>"manage-branding",
				"role_title"=>"admin"
			],
			[
				"ability"=>"view-theme",
				"role_title"=>"admin"
			],
			[
				"ability"=>"install-activate-deactivate-theme",
				"role_title"=>"admin"
			],
			[
				"ability"=>"customize-theme",
				"role_title"=>"admin"
			],
			[
				"ability"=>"delete-theme",
				"role_title"=>"admin"
			]
		];

		DB::table("abilities")->insert($abilities);
	}
}

?>
