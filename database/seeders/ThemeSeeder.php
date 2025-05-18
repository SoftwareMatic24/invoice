<?php

namespace Database\Seeders;

use App\Classes\DateTime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThemeSeeder extends Seeder
{
	
	public function run(): void
	{
		$fileContent = file_get_contents(base_path("_themes/themes.json"));
		$fileContent = json_decode($fileContent, true);
		$themes = array_map(function($theme){
			$theme["options"] = isset($theme["options"]) ? json_encode($theme["options"]) : NULL;
			$theme["create_datetime"] = DateTime::getDateTime();
			return $theme;
		},$fileContent);

		$themes = array_filter($themes, function($theme){
			if($theme["status"] === "active") return true;
			return false;
		});


		DB::table("themes")->insert($themes);
	}
}
