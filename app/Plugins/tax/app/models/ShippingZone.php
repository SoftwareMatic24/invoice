<?php

namespace App\Plugins\Tax\Models;

require_once __DIR__."/ShippingZoneCondition.php";

use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model {

	public $timestamps = false;
	protected $fillable = [
		"country",
		"state",
		"city",
		"postcode",
		"shipping_class_id"
	];

	// Relation

	function shippingZoneConditions(){
		return $this->hasMany(ShippingZoneCondition::class, "shipping_zone_id", "id");
	}

	// Query

	static function addShippingZones($shippingClassId, $zones){

		foreach($zones as $zone){

			$row = [
				"country"=>$zone["country"] ?? NULL,
				"state"=>$zone["state"] ?? NULL,
				"postcode"=>$zone["postcode"] ?? NULL,
				"city"=>$zone["city"] ?? NULL,
				"shipping_class_id"=>$shippingClassId
			];

			$shippingZone = self::create($row);

			ShippingZoneCondition::addConditions($shippingZone["id"], $zone["conditions"] ?? []);
		}

		
		return true;
	}

	static function deleteShippingZone($shippingClassId){
		return self::where("shipping_class_id", $shippingClassId)->delete();
	}

}

?>