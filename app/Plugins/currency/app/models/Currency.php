<?php

namespace App\Plugins\Currency\Model;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model {
	public $timestamps = false;
	protected $fillable = [
		"currency",
		"symbol",
		"type",
		"rate",
		"create_datetime",
		"update_datetime"
	];


	// Query

	static function getCurrencies(){
		return self::orderByRaw("CASE WHEN type = 'primary' THEN 1 WHEN type = 'secondary' THEN 2 ELSE 3 END")->get();
	}

	static function getCurrency($currencyId){
		return self::where("id", $currencyId)->first();
	}

	static function getCurrencyByCurrency($currency){
		return self::where("currency", $currency)->first();
	}

	static function getPrimaryCurrency(){
		return self::where("type", "primary")->first();
	}

	static function addCurrency($data){

		if($data["type"] === "primary") self::updatePrimaryCurrencyType("secondary");

		return self::create([
			"currency"=>$data["currency"],
			"symbol"=>$data["symbol"],
			"type"=>$data["type"],
			"rate"=>$data["rate"],
			"create_datetime"=>DateTime::getDateTime(),
		]);
	}

	static function updateCurrency($currencyId, $data){

		if($data["type"] === "primary") self::updatePrimaryCurrencyType("secondary");

		return self::where("id", $currencyId)->update([
			"currency"=>$data["currency"],
			"symbol"=>$data["symbol"],
			"type"=>$data["type"],
			"rate"=>$data["rate"],
			"update_datetime"=>DateTime::getDateTime(),
		]);
	}

	static function deleteCurrency($currencyId){
		return self::where("id", $currencyId)->delete();
	}

	static function updatePrimaryCurrencyType($type){
		return self::where("type", "primary")->update([
			"type"=>$type
		]);
	}

}

?>