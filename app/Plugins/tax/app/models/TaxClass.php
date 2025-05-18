<?php

namespace App\Plugins\Tax\Models;

require_once __DIR__."/TaxClassRate.php";

use Illuminate\Database\Eloquent\Model;

class TaxClass extends Model {

	public $timestamps = false;
	protected $fillable = [
		"title",
		"user_id"
	];

	// Relation

	function rates(){
		return $this->hasMany(TaxClassRate::class, "tax_class_id", "id");
	}

	// Build

	static function basicRelation(){
		return self::with("rates");
	}

	// Query

	static function getTaxClassesByUserId($userId){
		$relation = self::basicRelation();
		return $relation->where("user_id",$userId)->get();
	}

	static function getTaxClassByIdUserId($taxClassId, $userId){
		$relation = self::basicRelation();
		return $relation->where("id", $taxClassId)->where("user_id",$userId)->first();
	}

	static function saveTaxClass($userId, $taxClassId = NULL, $data){
		if($taxClassId === NULL) return self::addTaxClass($userId, $data);
		return self::updateTaxClass($userId, $taxClassId, $data);
	}

	static function addTaxClass($userId, $data){
		$taxClass = self::create([
			"title"=>$data["taxClassName"],
			"user_id"=>$userId
		]);

		TaxClassRate::addTaxClassRates($taxClass["id"], $data["rates"]);

		return $taxClass;
	}

	static function updateTaxClass($userId, $taxClassId = NULL, $data){

		$record = self::getTaxClassByIdUserId($taxClassId, $userId);
		if($record === NULL) return; 

		$taxClass = self::where("user_id", $userId)->where("id", $taxClassId)->update([
			"title"=>$data["taxClassName"]
		]);

		TaxClassRate::updateTaxClassRates($taxClassId, $data["rates"]);

		return $taxClass;
	}

	static function deleteUserTaxClass($userId, $taxClassId){
		return self::where("user_id", $userId)->where("id", $taxClassId)->delete();
	}

}

?>