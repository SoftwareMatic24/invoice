<?php

use Illuminate\Support\Facades\DB;

return new Class {
	public function run(){

		$abilities = [
			[
				"ability"=>"view-notification-banner",
				"role"=>"admin"
			],
			[
				"ability"=>"add-notification-banner",
				"role"=>"admin"
			],
			[
				"ability"=>"update-notification-banner",
				"role"=>"admin"
			],
			[
				"ability"=>"delete-notification-banner",
				"role"=>"admin"
			]
		];

		foreach($abilities as $row){
			DB::table("abilities")->insert([
				"ability"=>$row["ability"],
				"role_title"=>$row["role"]
			]);
		}

	}
}

?>