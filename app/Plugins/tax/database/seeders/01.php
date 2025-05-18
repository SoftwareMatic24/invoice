<?php

use App\Plugins\Ecommerce\Classes\EcommerceConfig;
use Illuminate\Support\Facades\DB;

return new class {

	public function run(){
		
		$taxSettings = [
			[
				"column_name"=>"tax",
				"column_value"=>true,
				"user_id"=>3
			],
			[
				"column_name"=>"price-inclusive-tax",
				"column_value"=>false,
				"user_id"=>3
			],
			[
				"column_name"=>"calculate-tax-on-address",
				"column_value"=>"shipping",
				"user_id"=>3
			],
			[
				"column_name"=>"product-pirce-display-tax",
				"column_value"=>true,
				"user_id"=>3
			],
			[
				"column_name"=>"shipping",
				"column_value"=>true,
				"user_id"=>3
			],
			[
				"column_name"=>"shipping-countries",
				"column_value"=>NULL,
				"user_id"=>3
			],
		];

		if(!EcommerceConfig::$isMultivendor){
			$taxSettings = array_map(function($row){
				$row["user_id"] = 1;
				return $row;
			}, $taxSettings);
		}

		DB::table("tax_settings")->insert($taxSettings);
	}

}

?>