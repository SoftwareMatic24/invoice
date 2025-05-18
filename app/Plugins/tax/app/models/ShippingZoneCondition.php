<?php

namespace App\Plugins\Tax\Models;


use Illuminate\Database\Eloquent\Model;

class ShippingZoneCondition extends Model {

	public $timestamps = false;
	protected $fillable = [
		"from",
		"to",
		"cost",
		"shipping_zone_id"
	];

	// Query

	static function addConditions($shippingZoneId, $conditions){
		$dataToInsert = [];

		foreach($conditions as $condition){
			$dataToInsert[] = [
				"from"=>$condition["from"],
				"to"=>$condition["to"] ?? NULL,
				"cost"=>$condition["cost"],
				"shipping_zone_id"=>$shippingZoneId
			];
		}

		return self::insert($dataToInsert);
	}
	
}

?>