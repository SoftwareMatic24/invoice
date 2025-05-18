<?php

namespace App\Plugins\Currency\Helpers;

use App\Plugins\Currency\Model\Currency as ModelCurrency;
use Illuminate\Support\Facades\Cache;

class Currency {

	static function currency($id){
		$currency = ModelCurrency::getCurrency($id);
		return !empty($currency) ? $currency->toArray() : NULL; 
	}

	static function currencies(){
		return ModelCurrency::getCurrencies()->toArray();
	}

	static function cacheCurrencies(){
		return Cache::get('currencies');
	}

	static function cacheCurrencyCurrency(){
		$currencyId = $_COOKIE['currency'] ?? NULL;
		$currencies	=  self::cacheCurrencies();
		$match = NULL;

		foreach($currencies as $c){
			if(empty($currencyId) && $c['type'] === 'primary') $match = $c;
			else if($currencyId == $c['id']) $match = $c;
		}

		return $match;
	}

	static function cachePrimaryCurrency(){
		$currencies	=  self::cacheCurrencies();
		$match = NULL;

		foreach($currencies as $c){
			if($c['type'] === 'primary') $match = $c;
		}

		return $match;
	}
	

}


?>