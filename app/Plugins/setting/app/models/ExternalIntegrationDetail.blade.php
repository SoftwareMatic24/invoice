<?php

namespace App\Plugins\Setting\Model;

use Illuminate\Database\Eloquent\Model;

class ExternalIntegrationDetail extends Model {

	public $timestamps = false;
	protected $fillable = [
		"column_name",
		"column_value",
		"external_integration_slug"
	];


	// Query

	static function saveDetails($slug, $data){
		self::deleteDetails($slug);
		$dataToInsert = [];

		foreach($data as $key=>$value){
			$dataToInsert[] = [
				"column_name"=>$key,
				"column_value"=>$value,
				"external_integration_slug"=>$slug
			];
		}

		return self::insert($dataToInsert);
	}

	static function deleteDetails($slug){
		return self::where("external_integration_slug", $slug)->delete();
	}
	

}

?>