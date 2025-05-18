<?php

namespace Database\Seeders;

use App\Classes\DateTime;
use App\Http\Controllers\AbilityController;
use App\Http\Controllers\RoleController;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{

		$dateTime = DateTime::getDateTime();

		$roleController = new RoleController();
		$roles = $roleController->allRoles();

		foreach($roles as $role){
			DB::table("roles")->insert([
				"title" => $role
			]);
		}

		
		$abilityController = new AbilityController();
		$abilities = $abilityController->abilities();

		foreach ($abilities as $ability => $roles) {

			foreach ($roles as $role) {
				DB::table("abilities")->insert(
					[
						"ability" => $ability,
						"role_title" => $role
					]
				);
			}
		}

		DB::table("users")->insert([
			"first_name" => "Admin",
			"email" => "admin@gmail.com",
			"password" => bcrypt("admin"), //admin
			"status" => "active",
			"image" => NULL,
			"role_title" => "admin",
			"dob"=>"1996-10-18",
			"create_datetime" => $dateTime
		]);

		DB::table("users")->insert([
			"first_name" => "User",
			"email" => "user@gmail.com",
			"password" => bcrypt("user"), //admin
			"status" => "active",
			"image" => NULL,
			"role_title" => "user",
			"create_datetime" => $dateTime
		]);
	}
}
