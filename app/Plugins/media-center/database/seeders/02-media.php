<?php

use App\Classes\DateTime;
use Illuminate\Support\Facades\DB;

return new class {

	public function run(){
		$media = [
			[
				"url"=>"avatars/avatar-1.png",
				"type"=>"image/png",
				"folder_id"=>2,
				"options"=>json_encode([
					"size"=>"28.9 KB"
				]),
				"user_id"=>1,
				"create_datetime"=>DateTime::getDateTime()
			],
			[
				"url"=>"avatars/avatar-2.png",
				"type"=>"image/png",
				"folder_id"=>2,
				"options"=>json_encode([
					"size"=>"32.4 KB"
				]),
				"user_id"=>1,
				"create_datetime"=>DateTime::getDateTime()
			],
			[
				"url"=>"avatars/avatar-3.png",
				"type"=>"image/png",
				"folder_id"=>2,
				"options"=>json_encode([
					"size"=>"31.7 KB"
				]),
				"user_id"=>1,
				"create_datetime"=>DateTime::getDateTime()
			],
			[
				"url"=>"avatars/avatar-4.png",
				"type"=>"image/png",
				"folder_id"=>2,
				"options"=>json_encode([
					"size"=>"28.0 KB"
				]),
				"user_id"=>1,
				"create_datetime"=>DateTime::getDateTime()
			],
		];
		DB::table("media")->insert($media);
	}

};
