<?php

namespace App\Plugins\Setting\Controller;

use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Plugins\Setting\Model\ExternalIntegration;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use HTTP;

class ExternalIntegrationController extends Controller
{

	function externalIntegrationsView()
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => __('external integrations'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('external integrations'),
			"pageSlug" => "external-integrations",
			"pluginConfig" => $config
		];

		return PluginController::loadView(__DIR__, "external-integrations.blade.php", $pageData);
	}

	function googleOAuthView()
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => __('setup google oauth'),
			"backURL" => Util::prefixedURL("/setting/external-integrations"),
			"pageName" => __('setup google oauth'),
			"pageSlug" => "external-integrations",
			"pluginConfig" => $config
		];

		return PluginController::loadView(__DIR__, "google-oauth.blade.php", $pageData);
	}

	function twoFactorAuthView()
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => __('2fa'),
			"backURL" => Util::prefixedURL("/dashboard"),
			"pageName" => __('2fa'),
			"pageSlug" => "2fa",
			"pluginConfig" => $config
		];

		return PluginController::loadView(__DIR__, "2fa.blade.php", $pageData);
	}

	/**
	 * Get
	 */

	function externalIntegrations()
	{
		return ExternalIntegration::getExternalIntegrations();
	}

	function externalIntegration($slug)
	{
		return ExternalIntegration::getExternalIntegrationBySlug($slug);
	}

	/**
	 * Save
	 */

	function saveExternalIntegration($slug, $data)
	{
		ExternalIntegration::saveInternalIntegration($slug, $data);
		$text = Str::slug($slug, " ");
		$text = ucwords($text);
		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	// Request

	function saveExternalIntegrationRequest(Request $request, $slug)
	{
		$data = $request->post();
		$response = $this->saveExternalIntegration($slug, $data);
		return HTTP::inStringResponse($response);
	}
}
