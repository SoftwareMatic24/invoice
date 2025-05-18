<?php

use App\Http\Controllers\RoleController;
use App\Models\Abilities;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

return new class extends Seeder
{

	public function run()
	{
		$roleController = new RoleController();
		$roles = $roleController->getRoles()->toArray();

		foreach ($roles as $role) {
			DB::table("abilities")->insert([
				"ability" => "user-payment-method",
				"role_title" => $role["title"]
			]);
		}

		DB::table("abilities")->insert([
			[
				"ability" => "system-payment-method",
				"role_title" => "admin"
			],
			[
				'ability' => 'manage-billing',
				'role_title' => 'admin'
			],
			[
				'ability' => 'manage-billing-transactions',
				'role_title' => 'admin'
			]
		]);
	}
};
