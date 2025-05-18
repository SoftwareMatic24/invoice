<?php

use App\Http\Controllers\RoleController;
use App\Models\Abilities;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

return new class extends Seeder {

	public function run(){

		$roleController = new RoleController();
		$roles = $roleController->allRoles();

		foreach($roles as $role) {
		
			$hasAbility = Abilities::where("ability", "settings")->where("role_title", $role)->first();

			if($hasAbility === NULL) {
				DB::table("abilities")->insert([
					"ability"=>"settings",
					"role_title"=>$role
				]);
			}

			DB::table("abilities")->insert([
				"ability"=>"manage-2fa",
				"role_title"=>$role
			]);
		}
		

		DB::table("abilities")->insert([
			[
				"ability"=>"general-settings",
				"role_title"=>"admin"
			],
			[
				"ability"=>"manage-smtp",
				"role_title"=>"admin"
			],
			[
				"ability"=>"manage-global-scripts",
				"role_title"=>"admin"
			],
			[
				"ability"=>"manage-sitemap",
				"role_title"=>"admin"
			],
			[
				"ability"=>"manage-external-integrations",
				"role_title"=>"admin"
			]
		]);

	}

};

?>