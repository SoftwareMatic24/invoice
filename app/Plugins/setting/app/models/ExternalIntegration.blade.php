<?php

namespace App\Plugins\Setting\Model;

require_once __DIR__ . "/ExternalIntegrationDetail.blade.php";

use Illuminate\Database\Eloquent\Model;
use App\Plugins\Setting\Model\ExternalIntegrationDetail;

class ExternalIntegration extends Model {

	public $timestamps = false;
	protected $fillable = [
		"title",
		"slug",
		"description",
		"status"
	];

	// Relation

	function detail(){
		return $this->hasMany(ExternalIntegrationDetail::class, "external_integration_slug", "slug");
	}

	// Build

	static function basicRelation(){
		return self::with("detail");
	}


	// Query

	static function getExternalIntegrations(){
		$relation = self::basicRelation();
		return $relation->orderBy("id","DESC")->get();
	}

	static function getExternalIntegrationBySlug($slug){
		$relation = self::basicRelation();
		return $relation->where("slug", $slug)->first();
	}

	static function saveInternalIntegration($slug, $data){
		self::where("slug", $slug)->update([
			"status"=>$data["status"]
		]);

		return ExternalIntegrationDetail::saveDetails($slug, $data["details"] ?? []);
	}

}

?>