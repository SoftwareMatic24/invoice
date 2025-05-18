<?php

use App\Classes\DateTime;
use App\Models\User;
use Illuminate\Support\Facades\DB;

return new class {
	public function run(){

		$templates = [
			[
				"name"=>"Classic",
				"slug"=>"classic",
				"primary_color"=>"#42464f",
				"secondary_color"=>"#0096c7",
				"create_datetime"=>DateTime::getDateTime()
			],
			[
				"name"=>"Professional",
				"slug"=>"professional",
				"primary_color"=>"#0096c7",
				"secondary_color"=>"#42464f",
				"create_datetime"=>DateTime::getDateTime()
			],
			[
				"name"=>"Smart",
				"slug"=>"smart",
				"primary_color"=>"#0096c7",
				"secondary_color"=>"#42464f",
				"create_datetime"=>DateTime::getDateTime()
			]
		];

		$userDocumentTemplates = [];
		
		$users = User::getUsersByRole('user')->toArray();
		
		foreach($users as $user){
			$userDocumentTemplates[] = [
				'primary_color'=>$templates[0]['primary_color'],
				'secondary_color'=>$templates[0]['secondary_color'],
				'user_id'=>$user['id'],
				'document_template_slug'=>$templates[0]['slug']
			];
		}

		DB::table('document_templates')->insert($templates);
		DB::table('user_document_templates')->insert($userDocumentTemplates);

	}
}

?>