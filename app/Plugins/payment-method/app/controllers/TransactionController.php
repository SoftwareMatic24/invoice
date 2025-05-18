<?php

namespace App\Plugins\Elearning\Controllers;

use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;

class TransactionController extends Controller
{

	function transactionsView()
	{
		$config = PluginController::getPluginConfig(__DIR__);
	
		$pageData = [
			"tabTitle" => __('transactions'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('transactions'),
			"pageSlug" => "transactions",
			"pluginConfig" => $config,
		];

		return PluginController::loadView(__DIR__, "manage-transactions.blade.php", $pageData);
	}
}
