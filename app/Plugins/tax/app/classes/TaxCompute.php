<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TaxCompute {
	
	static function calculateExclusiveTax($price, $taxPercentage){
		$price = floatval($price);
		$taxPercentage = floatval($taxPercentage);
		if($price < 0 || $taxPercentage < 0) return 0; 

		return ($price * $taxPercentage) / 100;
	}

	static function calculateInclusiveTax($price, $taxPercentage){
		$price = floatval($price);
		$taxPercentage = floatval($taxPercentage);
		if ($price < 0 || $taxPercentage < 0) return 0;
		return ($price * $taxPercentage) / (100 + $taxPercentage);
	}

	static function tax($price, $rates, $filters = [], $inclusiveTax = false){
		if($rates instanceof Collection) $rates = $rates->toArray();

		$output = [];
		$applicableRates = [];
		
		$calculateTax = $inclusiveTax ? "calculateInclusiveTax" : "calculateExclusiveTax";
		
		if(sizeof($filters) === 0) {
			$applicableRates = array_filter($rates, function($rate){
				if($rate["country"] === NULL && $rate["city"] === NULL && $rate["postcode"] === NULL) return $rate;
			});
		}
		else {
			$applicableRates = array_filter($rates, function($rate) use($filters) {
				$match = true;
				foreach($filters as $key=>$value){
					if(isset($rate[$key]) && (strtolower($rate[$key])) !== strtolower($value) && ($rate[$key]) !== NULL ) $match = false;
				}
		
				return $match;
			});
		}
		
		$output = array_map(function($rate) use($price, $calculateTax) {
			$taxAmount = self::$calculateTax($price, $rate["rate"]);
			return ["tax"=>$taxAmount, "name"=>$rate["tax_name"]];
		}, $applicableRates);

		return $output;
	}
}

?>