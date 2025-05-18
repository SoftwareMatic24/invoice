<?php

use Illuminate\Support\Facades\DB;

return new class {
	public function run(){

		$abilities = [
			"view-subscription-package",
			"add-subscription-package",
			"update-subscription-package",
			"delete-subscription-package",
			"view-subscription-subscribers",
			"manage-subscription-subscribers",
			"manage-subscription-classifications"
		];

		$userAbilities = [
			[
				"ability"=>"view-user-subscriptions",
				"role_title"=>"user"
			]
		];

		foreach($abilities as $ability) {
			DB::table("abilities")->insert([
				"ability"=>$ability,
				"role_title"=>"admin"
			]);
		}
		
		DB::table('abilities')->insert($userAbilities);

	}
}

?>