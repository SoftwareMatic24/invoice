<?php

use App\Classes\DateTime;
use Illuminate\Support\Facades\DB;

return new class {
	public function run(){}
	public function bridge()
	{

		$settings = function () {
			$seeds = [
				[
					"column_name" => "brand-name",
					"column_value" => "InvoiceMatic"
				],
				[
					"column_name" => "brand-logo",
					"column_value" => "temp/quick-invoice/invoice-matic-logo.png"
				],
				[
					"column_name" => "brand-logo-light",
					"column_value" => "temp/quick-invoice/invoice-matic-logo-light.png"
				],
				[
					"column_name" => "brand-portal-logo",
					"column_value" => "temp/quick-invoice/invoice-matic-logo-light.png"
				],
				[
					"column_name" => "brand-fav-icon",
					"column_value" => "temp/quick-invoice/icon.png"
				]
			];

			foreach ($seeds as $row) {
				DB::table("settings")->where("column_name", $row["column_name"])->update([
					"column_value" => $row["column_value"]
				]);
			}

		};

		return [
			[
				"dirs" => ["Plugins/setting/database/seeders"],
				"seeds" => [$settings]
			]
		];
	}

}

?>