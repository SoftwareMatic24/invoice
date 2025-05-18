<?php

use Illuminate\Support\Facades\DB;

return new class {

	public function run(){
		$folders = [
			[
				"title"=>"Unorganized",
				"user_id"=>1
			],
			[
				"title"=>"Avatars",
				"user_id"=>1
			]
		];
		DB::table("media_center_folders")->insert($folders);
	}

};
