<?php

namespace App\Plugins\EmailTemplate\Model;

use Illuminate\Database\Eloquent\Model;

class EmailSignature extends Model {

	public $timestamps = false;
	protected $fillable = [
		"title",
		"slug",
		"content"
	];

	// Query

	static function getSignatures(){
		return self::orderBy("id", "DESC")->get();
	}

	static function getSignature($signatureId){
		return self::where("id", $signatureId)->first();
	}

	static function saveSignature($signatureId, $data){
		$signature = self::getRow($signatureId);

		if($signature === NULL) $signature = self::addSignature($data);
		else self::updateSignature($signatureId, $data);

		return $signature["id"];
	}

	static function addSignature($data){
		return self::create([
			"title"=>$data["title"],
			"slug"=>$data["slug"],
			"content"=>$data["content"] ?? NULL
		]);
	}

	static function updateSignature($signatureId, $data){
		return self::where("id", $signatureId)->update([
			"title"=>$data["title"],
			"slug"=>$data["slug"],
			"content"=>$data["content"] ?? NULL
		]);
	}

	// Rows

	static function getRow($signatureId){
		return self::where("id", $signatureId)->first();
	}

	static function getRowBySlug($slug){
		return self::where("slug", $slug)->first();
	}

	static function getRows(){
		return self::orderBy("id", "DESC")->get();
	}

	static function deleteRow($signatureId){
		return self::where("id", $signatureId)->delete();
	}


}

?>