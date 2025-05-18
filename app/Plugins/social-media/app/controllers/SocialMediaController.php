<?php

namespace App\Plugins\SocialMedia\Controller;

require_once __DIR__ . "/../models/SocialMediaLinks.php";

use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Plugins\SocialMedia\Model\SocialMediaLinks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SocialMediaController extends Controller
{


	/**
	 * ===== Views
	 */

	function socialLinksView()
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => "Social Media Links",
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => "Social Media Links",
			"pageSlug" => "social-links",
			"pluginConfig" => $config
		];

		return PluginController::loadView(__DIR__, "social-media-links.blade.php", $pageData);
	}

	/**
	 * ===== Method
	 */

	function allSocialMediaLinks()
	{
		return SocialMediaLinks::getSocialMediaLinks();
	}

	/**
	 * ===== API
	 */

	function saveSocialMediaLinksRequest(Request $request)
	{

		Cache::forget("socialMediaLinks");

		$data = $request->post();
		SocialMediaLinks::saveSocialMediaLinks($data);
		return ["status" => "success", "msg" => "Social media links are saved."];
	}
}
