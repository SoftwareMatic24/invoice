<?php

namespace App\Plugins\Tax\Models;

use Illuminate\Database\Eloquent\Model;

class TaxClassRate extends Model {

	public $timestamps = false;
	protected $fillable = [
		"country",
		"state",
		"city",
		"postcode",
		"rate",
		"tax_name",
		"tax_class_id"
	];

	// Query

	static function addTaxClassRates($taxClassId, $rates){

		$dataToInsert = [];

		foreach($rates as $rate){
			$dataToInsert[] = [
				"country"=> $rate["country"] ?? NULL,
				"state"=>$rate["state"] ?? NULL,
				"city"=>$rate["city"] ?? NULL,
				"postcode"=>$rate["postcode"] ?? NULL,
				"rate"=>$rate["rate"],
				"tax_name"=>$rate["tax"],
				"tax_class_id"=>$taxClassId
			];
		}
		
		return self::insert($dataToInsert);
	}

	static function updateTaxClassRates($taxClassId, $rates){
		self::deleteTaxClassRates($taxClassId);
		return self::addTaxClassRates($taxClassId, $rates);
	}

	static function deleteTaxClassRates($taxClassId){
		return self::where("tax_class_id",$taxClassId)->delete();
	}

}

?>