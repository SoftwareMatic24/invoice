<?php

namespace App\Plugins\Tax\Models;

require_once __DIR__."/ShippingZone.php";

use Illuminate\Database\Eloquent\Model;

class ShippingClass extends Model {

	public $timestamps = false;
	protected $fillable = [
		"title",
		"user_id"
	];

	// Relation

	function shippingZones(){
		return $this->hasMany(ShippingZone::class, "shipping_class_id", "id");
	}

	// Build Relation

	static function basicRelation(){

		$withShippingZones = function($shippingZone){
			$shippingZone->with("shippingZoneConditions");
		};

		return self::with(["shippingZones"=>$withShippingZones]);
	}

	// Query

	static function getUserShippingClasses($userId){
		$relation = self::basicRelation();
		return $relation->where("user_id", $userId)->get();
	}

	static function getUserShippingClass($userId, $shippingClassId){
		$relation = self::basicRelation();
		return $relation->where("user_id", $userId)->where("id", $shippingClassId)->first();
	}

	static function addUserShippingClass($userId, $data){
		$shippingClass = self::create([
			"title"=>$data["shippingClassName"],
			"user_id"=>$userId
		]);
		ShippingZone::addShippingZones($shippingClass["id"], $data["zones"] ?? []);
		return $shippingClass;
	}

	static function updateUserShippingClass($userId, $shippingClassId, $data){
		
		$record = self::getUserShippingClass($userId, $shippingClassId);
		if($record === NULL) return;

		self::where("user_id", $userId)->where("id", $shippingClassId)->update([
			"title"=>$data["shippingClassName"]
		]);

		ShippingZone::deleteShippingZone($shippingClassId);
		ShippingZone::addShippingZones($shippingClassId, $data["zones"] ?? []);
		return true;
	}

	static function deleteUserShippingClass($userId, $shippingClassId){
		return self::where("user_id", $userId)->where("id",$shippingClassId)->delete();
	}

}

?>