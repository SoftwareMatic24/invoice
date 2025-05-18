<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

return new class extends Seeder {
	public function run(){

		$branding = function () {
			$seeds = [
				[
					"column_name" => "brand-name",
					"column_value" => "Invoice Maker"
				],
				[
					"column_name" => "brand-logo",
					"column_value"=>"temp/quick-invoice/logo.png"
				],
				[
					"column_name" => "brand-logo-light",
					"column_value"=>"temp/quick-invoice/logo-light.png"
				],
				[
					"column_name" => "brand-fav-icon",
					"column_value"=>"temp/quick-invoice/fav.png"
				],
				[
					"column_name"=>"account-page-image",
					"column_value"=>NULL
				]
			];

			$seeds2 = [
				[
					"column_name"=>"sidebarHeaderColor",
					"column_value"=>"#563981"
				],
				[
					"column_name"=>"sidebarColor",
					"column_value"=>"#563981"
				],
				[
					"column_name"=>"sidebarDropdownColor",
					"column_value"=>"#4d3373"
				],
				[
					"column_name"=>"sidebarTextColor",
					"column_value"=>"#ffffff"
				],
				[
					"column_name"=>"primaryButtonColor",
					"column_value"=>"#563981"
				],
				[
					"column_name"=>"primaryButtonTextColor",
					"column_value"=>"#ffffff"
				],
				[
					"column_name"=>"primaryButtonHoverColor",
					"column_value"=>"#4d3373"
				],
				[
					"column_name"=>"pageBackgroundColor",
					"column_value"=>"#F8F8F8"
				]
			];

			foreach ($seeds as $row) {
				DB::table("branding")->where("column_name", $row["column_name"])->update([
					"column_value" => $row["column_value"]
				]);
			}

			foreach($seeds2 as $row){
				DB::table("default_branding")->where("column_name", $row["column_name"])->update([
					"column_value" => $row["column_value"]
				]);
			}

			DB::table('branding')->insert($seeds2);
		};

		$branding();
	}

}

?>