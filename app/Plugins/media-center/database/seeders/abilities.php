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
		
			$hasAbility = Abilities::where("ability", "media-center")->where("role_title", $role)->first();
			if($hasAbility === NULL) {
				DB::table("abilities")->insert([
					"ability"=>"media-center",
					"role_title"=>$role
				]);
			}
		}		

	}

};

?>