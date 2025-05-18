<?php

use App\Classes\DateTime;
use App\Models\User;
use Illuminate\Support\Facades\DB;

return new class {
	public function run(){}
	public function bridge()
	{	
		$packageLimitFunc = function () {
			$limits = [
				[
					"plugin_slug" => "quick-invoice",
					"label" => "No. of Invoices",
					"limit_slug" => "quick-invoice-invoice"
				],
				[
					"plugin_slug" => "quick-invoice",
					"label" => "No. of Proposals",
					"limit_slug" => "quick-invoice-proposal"
				],
				[
					"plugin_slug" => "quick-invoice",
					"label" => "No. of Expense Sheets",
					"limit_slug" => "quick-invoice-expense-sheet"
				],
				[
					"plugin_slug" => "quick-invoice",
					"label" => "No. of Businesses",
					"limit_slug" => "quick-invoice-business"
				]
			];

			DB::table("subscription_package_plugin_limits")->insert($limits);
		};

		$packagesFunc = function(){
			$packages = [
				[
					"title"=>"Free",
					"description"=>"forever",
					"price"=>0,
					"status"=>"active",
					"user_id"=>1,
					"classification_id"=>1,
					"create_datetime"=>DateTime::getDateTime(),
					"details"=>[
						[
							"name"=>"1 proposal and invoice / m",
							"included"=>true
						],
						[
							"name"=>"1 expense sheet / m",
							"included"=>true
						],
						[
							"name"=>"1 business / m",
							"included"=>true
						]
					],
					"limits"=>[
						[
							"slug"=>"quick-invoice-invoice",
							"limit"=>1
						],
						[
							"slug"=>"quick-invoice-proposal",
							"limit"=>1
						],
						[
							"slug"=>"quick-invoice-expense-sheet",
							"limit"=>1
						],
						[
							"slug"=>"quick-invoice-business",
							"limit"=>1
						]
					]
				],
				[
					"title"=>"Basic",
					"description"=>"per month",
					"price"=>4.99,
					"status"=>"active",
					"user_id"=>1,
					"classification_id"=>1,
					"create_datetime"=>DateTime::getDateTime(),
					"details"=>[
						[
							"name"=>"20 proposals and invoices / m",
							"included"=>true
						],
						[
							"name"=>"20 expense sheets / m",
							"included"=>true
						],
						[
							"name"=>"20 businesses / m",
							"included"=>true
						]
					],
					"limits"=>[
						[
							"slug"=>"quick-invoice-invoice",
							"limit"=>20
						],
						[
							"slug"=>"quick-invoice-proposal",
							"limit"=>20
						],
						[
							"slug"=>"quick-invoice-expense-sheet",
							"limit"=>20
						],
						[
							"slug"=>"quick-invoice-business",
							"limit"=>20
						]
					]
				],
				[
					"title"=>"Standard",
					"description"=>"per month",
					"price"=>9.99,
					"status"=>"active",
					"user_id"=>1,
					"classification_id"=>1,
					"create_datetime"=>DateTime::getDateTime(),
					"details"=>[
						[
							"name"=>"100 proposals and invoices / m",
							"included"=>true
						],
						[
							"name"=>"100 expense sheets / m",
							"included"=>true
						],
						[
							"name"=>"100 businesses / m",
							"included"=>true
						]
					],
					"limits"=>[
						[
							"slug"=>"quick-invoice-invoice",
							"limit"=>100
						],
						[
							"slug"=>"quick-invoice-proposal",
							"limit"=>100
						],
						[
							"slug"=>"quick-invoice-expense-sheet",
							"limit"=>100
						],
						[
							"slug"=>"quick-invoice-business",
							"limit"=>100
						]
					]
				],
				[
					"title"=>"Enterprise",
					"description"=>"per month",
					"price"=>19.99,
					"status"=>"active",
					"user_id"=>1,
					"classification_id"=>1,
					"create_datetime"=>DateTime::getDateTime(),
					"details"=>[
						[
							"name"=>"Unlimited proposals and invoices / m",
							"included"=>true
						],
						[
							"name"=>"Unlimited expense sheets / m",
							"included"=>true
						],
						[
							"name"=>"Unlimited businesses / m",
							"included"=>true
						]
					],
					"limits"=>[
						[
							"slug"=>"quick-invoice-invoice",
							"limit"=>NULL
						],
						[
							"slug"=>"quick-invoice-proposal",
							"limit"=>NULL
						],
						[
							"slug"=>"quick-invoice-expense-sheet",
							"limit"=>NULL
						],
						[
							"slug"=>"quick-invoice-business",
							"limit"=>NULL
						]
					]
				]
			];

			foreach($packages as $package){
				$packageDetails = $package["details"];
				$packageLimits = $package["limits"];

				unset($package["details"]);
				unset($package["limits"]);

				$packageId = DB::table("subscription_packages")->insertGetId($package);

				$packageDetails = array_map(function($row) use($packageId){
					$row["subscription_package_id"] = $packageId;
					return $row;
				}, $packageDetails);

				$packageLimits = array_map(function($row) use($packageId){
					$row["subscription_package_id"] = $packageId;
					return $row;
				}, $packageLimits);
				
				DB::table("subscription_package_details")->insert($packageDetails);
				DB::table("subscription_package_limits")->insert($packageLimits);
			}
		};

		$subscriberFunc = function(){
			$users = User::getUsersByRole("user")->toArray();
			$dataToInsert = [];
			$subscriber = [
				"subscription_package_id"=>1,
				"user_id"=>NULL,
				"create_datetime"=>DateTime::getDateTime()
			];

			foreach($users as $user){
				$subscriber["user_id"] = $user["id"];
				$dataToInsert[] = $subscriber;
			}

			DB::table("subscription_subscribers")->insert($dataToInsert);
		};

		return [
			[
				"dirs" => ["Plugins/subscription/database/seeders"],
				"seeds" => [$packageLimitFunc, $packagesFunc, $subscriberFunc]
			]
		];
	}

}

?>

<!-- subscription_package_plugin_limits -->