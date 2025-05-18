<?php

namespace App\Plugins\Appearance\Controller;

use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Models\Theme;
use App\Plugins\Appearance\Helpers\Appearance;
use App\Plugins\Appearance\Models\Branding;
use HTTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;


class AppearanceController extends Controller
{

	function brandingView()
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$branding = Appearance::branding();

		$pageData = [
			"tabTitle" => __('branding'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('branding'),
			"pageSlug" => "branding",
			"pluginConfig" => $config,
			"branding" => $branding
		];

		return PluginController::loadView(__DIR__, "branding.blade.php", $pageData);
	}

	function accountBrandingView()
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$branding = Branding::getBranding()->toArray();

		$pageData = [
			"tabTitle" => __('login-register-branding'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('login-register-branding'),
			"pageSlug" => "account",
			"pluginConfig" => $config,
			"branding"=>$branding
		];

		return PluginController::loadView(__DIR__, "account-branding.blade.php", $pageData);
	}

	function themesView()
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$pageData = [
			"tabTitle" => "Themes",
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => "Themes",
			"pageSlug" => "themes",
			"pluginConfig" => $config,
		];

		return PluginController::loadView(__DIR__, "themes.blade.php", $pageData);
	}

	function customizeTheme($themeSlug)
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$pageData = [
			"tabTitle" => "Customize Theme",
			"backURL" => Util::prefixedURL($config["slug"] . "/themes"),
			"pageName" => "Customize Theme",
			"pageSlug" => "themes",
			"pluginConfig" => $config,
			"themeSlug" => $themeSlug
		];

		return PluginController::loadView(__DIR__, "customize-theme.blade.php", $pageData);
	}

	/**
	 * Apperance: Get
	 */

	function generatePortalStyle()
	{

		$branding = Cache::get("branding");

		$style = "<style>";
		$style .= ":root {";

		foreach ($branding as $attr => $value) {

			if (strpos(strtolower($attr), "color") !== false) {
				$style .= "--$attr: $value;";
			} else if (strpos(strtolower($attr), "buttonborderradius") !== false) {
				$value = $value . "px";
				$style .= "--$attr: $value;";
			} else if (strpos(strtolower($attr), "tablefiltersradius") !== false) {
				$value = $value . "px";
				$style .= "--$attr: $value;";
			}
		}

		$style .= "}";
		$style .= "</style>";

		return $style;
	}

	function generateThemeStyle($options)
	{

		if (empty($options)) return "";
		else if (!is_array($options)) $options = json_decode($options, true);

		$colorsStyle = "";

		$colors = $options["colors"] ?? [];

		foreach ($colors as $colorName => $color) {
			$colorsStyle .= "$colorName:$color; ";
		}

		return "<style> :root {{$colorsStyle}} </style>";
	}


	/**
	 * Appearance: Save
	 */

	function updateBranding($data)
	{
		Cache::forget("settings");

		$data["logoURL"] = $data["logoURL"] ?? NULL;
		$data["favIconURL"] = $data["favIconURL"] ?? NULL;
		$data["portalLogoURL"] = $data["portalLogoURL"] ?? NULL;

		$validator = Validator::make($data, []);

		if ($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}

		$logoSize = $data["logoSize"] ?? 0;
		$logoLightSize = $data["logoLightSize"] ?? 0;

		Branding::saveBranding("brand-name", $data["name"] ?? NULL);
		Branding::saveBranding("brand-about", $data["about"] ?? "");
		Branding::saveBranding("brand-logo", $data["logoURL"]);
		Branding::saveBranding("brand-logo-light", $data["logoLightURL"] ?? NULL);
		Branding::saveBranding("brand-fav-icon", $data["favIconURL"]);
		Branding::saveBranding("brand-logo-size", $logoSize);
		Branding::saveBranding("brand-logo-light-size", $logoLightSize);
		Branding::saveBranding("brand-portal-logo", $data["portalLogoURL"]);
		Branding::saveBranding("buttonBorderRadius", $data["buttonBorderRadius"] ?? 6);
		Branding::saveBranding("tableFiltersRadius", $data["tableFiltersRadius"] ?? 6);

		foreach ($data as $key => $value) {
			if (strpos(strtolower($key), "color") !== false) Branding::saveBranding($key, $value);
		}

		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	function updateAccountBranding($data)
	{

		$description = $data["description"] ?? "";
		$bgImageURL = $data["bgImageURL"] ?? NULL;
		$opacity = $data["opacity"] ?? 1;
		$overlay = $data["overlay"];

		Branding::saveBranding("account-page-description", $description);
		Branding::saveBranding("account-page-image", $bgImageURL);
		Branding::saveBranding("account-page-image-opacity", $opacity);
		Branding::saveBranding("account-page-image-overlay", $overlay);

		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	// Request

	function updateBrandingRequest(Request $request)
	{
		$data = $request->post();
		$response = $this->updateBranding($data);
		return HTTP::inStringResponse($response);
	}

	function updateAccountBrandingRequest(Request $request)
	{
		$data = $request->post();
		$response = $this->updateAccountBranding($data);
		return HTTP::inStringResponse($response);
	}


	/**
	 * Theme: Get
	 */

	function theme($themeSlug)
	{
		return Theme::getRowBySlug($themeSlug);
	}


	/**
	 * Theme: Save
	 */

	function resetThemeColors($themeSlug)
	{
		$theme = $this->theme($themeSlug);
		if (empty($theme)) {
			return HTTP::inStringResponse(HTTP::inBoolArray(false, __('request-failed')), __('theme not found'));
		}

		$options = $theme["options"];
		if ($options !== NULL) $options = json_decode($options, true);

		if ($options["defaultColors"] ?? false) $options["colors"] = $options["defaultColors"];

		Theme::updateOptions($themeSlug, $options);
		Cache::forget("activeTheme");

		return HTTP::inStringResponse(HTTP::inBoolArray(true, __('theme-update-notification-heading'), __('theme-update-notification-description')));
	}

	function updateCustomizedTheme($themeSlug, $data)
	{
		$theme = $this->theme($themeSlug);
		if (empty($theme)){
			return HTTP::inBoolArray(false, __('request-failed'), __('theme not found'));
		}
		$theme = $theme->toArray();

		$options = $theme["options"];
		if ($options !== NULL) $options = json_decode($options, true);
		if ($data["colors"] ?? false) $options["colors"] = $data["colors"];

		Theme::updateOptions($themeSlug, $options);
		Cache::forget("activeTheme");

		return HTTP::inBoolArray(true, __('theme-update-notification-heading'), __('theme-update-notification-description'));
	}

	// Util

	function updateCustomizedThemeRequest(Request $request, $themeSlug)
	{
		$data = $request->post();
		$response = $this->updateCustomizedTheme($themeSlug, $data);
		return HTTP::inStringResponse($response);
	}
};
