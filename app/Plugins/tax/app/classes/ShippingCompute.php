<?php

use Illuminate\Database\Eloquent\Collection;

class ShippingCompute {
	private static function applicableZones($shippingZones, $filters = []){
		
		if($shippingZones instanceof Collection) $shippingZones = $shippingZones->toArray();
		$zones = [];

		
		if(sizeof($filters) === 0) {
			$zones = array_filter($shippingZones, function($zone){
				if($zone["country"] === NULL && $zone["city"] === NULL && $zone["postcode"] === NULL) return $zone;
			});
		}
		else {
			$zones = array_filter($shippingZones, function($zone) use($filters) {
				
				$match = true;
				foreach($filters as $key=>$value){
					if(isset($zone[$key]) && (strtolower($zone[$key])) !== strtolower($value) && ($zone[$key]) !== NULL ) $match = false;
				}
				return $match;
			});
		}

		if(sizeof($zones) === 0) return $zones;

		$maxPoints = [];
		$max = NULL;
		$maxIndex = NULL;

		foreach($zones as $index=>$zone){
			$maxPoints[$index] = 0;
			if(!empty($zone["country"])) $maxPoints[$index]++;
			if(!empty($zone["state"])) $maxPoints[$index]++;
			if(!empty($zone["city"])) $maxPoints[$index]++;
			if(!empty($zone["postcode"])) $maxPoints[$index]++;
		}

		foreach($maxPoints as $index=>$points){
			if($max === NULL || $max < $points){
				$max = $points;
				$maxIndex = $index;
			}
		}

		return [$zones[$maxIndex]];
	}

	private static function applicableConditions($price, $zones){
		$match = [];
		$price = floatval($price);

		foreach($zones as $zone){
			$conditions = $zone["shipping_zone_conditions"] ?? [];
			
			foreach($conditions as $condition){
				$from = $condition["from"] ?? 0;
				$from = floatval($from);
				$to = $condition["to"] ?? NULL;

				if($to !== NULL && $price >= $from && $price <= floatval($to)) $match[] = $condition;
				else if($to === NULL && $price >= $from) $match[] = $condition; 
			}
		}

		return $match;
	}

	private static function conditionsCost($conditions){
		$cost = 0;

		foreach($conditions as $condition){
			$cost += floatval($condition["cost"]);
		}

		return $cost;
	}

	static function shipping($price, $shippingZones, $filters = []){
		$zones = self::applicableZones($shippingZones, $filters);
		$conditions = self::applicableConditions($price, $zones);
		$cost = self::conditionsCost($conditions);

		return $cost;
	}
}

?>