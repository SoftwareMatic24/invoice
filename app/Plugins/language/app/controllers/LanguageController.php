<?php

namespace App\Plugins\Language\Controllers;


use App\Classes\Constants;
use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Plugins\Language\Models\Language;
use App\Plugins\Setting\Model\Setting;
use HTTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{

	// views

	function languagesView()
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => __('languages'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('languages'),
			"pageSlug" => "language-manage",
			"pluginConfig" => $config,
		];

		return PluginController::loadView(__DIR__, "manage-languages.blade.php", $pageData);
	}

	function saveLanguageView($code = NULL)
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => empty($code) ? __('new language') : __('update language'),
			"backURL" => Util::prefixedURL($config["slug"] . "/manage"),
			"pageName" => empty($code) ? __('new language') : __('update language'),
			"pageSlug" => "language-save",
			"pluginConfig" => $config,
			"languages" => Constants::$languages,
			"languageCode" => $code
		];

		return PluginController::loadView(__DIR__, "save-language.blade.php", $pageData);
	}

	function translationsView()
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$activeLanguages = Language::getLanguages("active");

		$pageData = [
			"tabTitle" => __('translations'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('translations'),
			"pageSlug" => "language-translation",
			"pluginConfig" => $config,
			"languages" => json_encode(Constants::$languages),
			"activeLanguages" => json_encode($activeLanguages)
		];

		return PluginController::loadView(__DIR__, "translations.blade.php", $pageData);
	}

	function settingsView()
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$activeLanguages = Language::getLanguages("active");
		$settings = Cache::get("settings");
		$portalLang = $settings["portal-lang"]["column_value"] ?? "en";

		$pageData = [
			"tabTitle" => __('language settings'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('language settings'),
			"pageSlug" => "language-settings",
			"pluginConfig" => $config,
			"languages" => $activeLanguages,
			"portalLang" => $portalLang
		];

		return PluginController::loadView(__DIR__, "language-settings.blade.php", $pageData);
	}


	/**
	 * Language: Get
	 */

	function languages()
	{
		return Language::getLanguages();
	}

	function languageByCode($code)
	{
		return Language::getLanguageByCode($code);
	}

	/**
	 * Language: Save
	 */

	function saveLanguage($code = NULL, $data)
	{

		$validation = Validator::make($data, [
			"name" => "required",
			"code" => "required|max:10",
			"type" => "required",
			"direction" => "required"
		]);

		if ($validation->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validation->errors()->all()[0]);
		}

		if (($data["type"] ?? "secondary") === "primary" && ($data["status"] ?? "inactive") != "active") {
			return HTTP::inBoolArray(false, __('request-failed'), __('primary-language-must-be-active'));
		}

		$oldLanguage = Language::getLanguageByCode($data["code"]);

		if ($code === NULL) {

			if ($oldLanguage !== NULL) {
				return HTTP::inBoolArray(false, __('request-failed'), __('language-already-in-use'));
			}
			
			$language = Language::addLanguage($data);
			$code = $language["code"];

		} else {
			
			if ($oldLanguage !== NULL && $data["code"] != $code) {
				return HTTP::inBoolArray(false, __('request-failed'), __('language-already-in-use'));
			}

			if ($oldLanguage !== NULL && $oldLanguage["type"] === "primary" && $data["type"] === "secondary") {
				$languages = Language::getLanguages();

				if (sizeof($languages) === 1) {
					return HTTP::inBoolArray(false, __('request-failed'), __('at-least-one-primary-language'));
				}
				
				Language::setFirstRecordToPrimary();
			}
			Language::updateLanguageByCode($code, $data);

			$code = $data["code"];
		}

		App::make("clearCachedData");
		
		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	
	}

	function saveLanguageSettings($data)
	{
		$languageCode = $data["language"] ?? "en";
		Setting::saveSetting("portal-lang", $languageCode);
		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	function worldWideLanguages()
	{
		return Constants::$languages;
	}

	// Request

	function saveLanguageRequest(Request $request, $code = NULL)
	{
		$data = $request->post();
		$response = $this->saveLanguage($code, $data);
		return HTTP::inStringResponse($response);
	}

	function saveLanguageSettingsRequest(Request $request)
	{
		$data = $request->post();
		$response = $this->saveLanguageSettings($data);
		return HTTP::inStringResponse($response);
	}


	/**
	 * Language: Delete
	 */

	function deleteLanguage($code)
	{
		$language = Language::getLanguageByCode($code);

		if (empty($language)) {
			return HTTP::inBoolArray(false, __('not-found-notification-heading'), __('not-found-notification-description'));
		}

		if ($language["type"] === "primary") {
			return HTTP::inBoolArray(false, __('request-failed'), __('primary-language-not-deleteable-notification-description'));
		}

		Language::deleteLanguageByCode($code);
		App::make("clearCachedData");

		return HTTP::inBoolArray(true, __('delete-notification-heading'), __('delete-notification-description'));
	}

	// Request

	function deleteLangugeRequest($code){
		$response = $this->deleteLanguage($code);
		return HTTP::inStringResponse($response);
	}


	/**
	 * Translations: Get
	 */

	function translations($options = [])
	{

		$type = $options["type"] ?? false;
		$name = $options["name"] ?? false;
		$output = [];

		if ($type === "theme") {
			$activeTheme = Cache::get("activeTheme");
			$themeSlug = $activeTheme["slug"];
			$themeLangPath = resource_path("/views/themes/$themeSlug/lang");
			$themeLangFiles = File::files($themeLangPath);
			$themeLang = [];

			foreach ($themeLangFiles as $file) {
				$fileName = pathinfo($file, PATHINFO_FILENAME);
				$extension = pathinfo($file, PATHINFO_EXTENSION);
				if ($extension !== "json") continue;
				$themeLang[$fileName] = file_get_contents($file);
			}

			$output["theme"] = $themeLang;
		} else if ($type === "plugin") {
			$path = app_path("Plugins/$name/lang");
			$files = File::files($path);
			$lang = [];

			foreach ($files as $file) {
				$fileName = pathinfo($file, PATHINFO_FILENAME);
				$extension = pathinfo($file, PATHINFO_EXTENSION);
				if ($extension !== "json") continue;
				$lang[$fileName] = file_get_contents($file);
			}

			$output[$name] = $lang;
		} else if ($type === "main") {
			$path = lang_path();
			$files = File::files($path);
			$lang = [];

			foreach ($files as $file) {
				$fileName = pathinfo($file, PATHINFO_FILENAME);
				$extension = pathinfo($file, PATHINFO_EXTENSION);
				if ($extension !== "json" || strpos($fileName, "-original") !== false) continue;
				$lang[$fileName] = file_get_contents($file);
			}

			$output["main"] = $lang;
		} else if ($type === "system") {
			$path = lang_path();
			$files = File::files($path);
			$lang = [];

			foreach ($files as $file) {
				$fileName = pathinfo($file, PATHINFO_FILENAME);
				$extension = pathinfo($file, PATHINFO_EXTENSION);
				if ($extension !== "json" || strpos($fileName, "-original") === false) continue;
				$lang[$fileName] = file_get_contents($file);
			}

			$output["system"] = $lang;
		}

		return $output;
	}

	function pluginSlugsHavingLanguage()
	{

		$requiredPluginSlugs = [];
		$plugins = Cache::get("activePlugins");

		foreach ($plugins as $plugin) {
			$slug = $plugin["slug"];
			$langPath = app_path("/Plugins/$slug/lang");
			if (is_dir($langPath)) {
				$files = File::files($langPath);
				if (sizeof($files) > 0) $requiredPluginSlugs[] = $slug;
			}
		}

		return $requiredPluginSlugs;
	}

	// Request

	function translationsRequest(Request $request)
	{
		$data = $request->post();
		return $this->translations($data);
	}

	/**
	 * Translations: Save
	 */

	function saveTranslations($data)
	{

		$contentToSave = [];

		foreach ($data as $languageCode => $arr) {
			$fileContent = [];
			$filePath = lang_path($languageCode . ".json");

			if (file_exists($filePath)) {
				$fileContent = file_get_contents($filePath);
				$fileContent = json_decode($fileContent, true);
			}

			foreach ($arr as $row) {
				$key = $row["label"];
				$value = $row["value"];
				$fileContent[$key] = $value ?? "";
			}

			$contentToSave[$languageCode] = $fileContent;
		}

		foreach ($contentToSave as $languageCode => $content) {
			$filePath = lang_path($languageCode . ".json");
			File::put($filePath, json_encode($content));
		}

		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	// Request

	function saveTranslationsRequest(Request $request)
	{
		$data = $request->post();
		$response = $this->saveTranslations($data);
		return HTTP::inStringResponse($response);
	}


	/**
	 * Language: Switch
	 */

	function switchLanguage($code, $host, $referer)
	{
		
		if (language()['code'] === $code) $code = '';

		$url = $this->generateLanguageSwitchURL($code, $host, $referer);
		return redirect($url);
	}

	function generateLanguageSwitchURL($code, $host, $referer)
	{
		return $this->isBasicLanguageSwitchSupported($referer, $host)
			? $this->basicLanguageSwitchURL($code)
			: $this->advanceLanguageSwitchURL($code, $referer);
	}

	function basicLanguageSwitchURL($code)
	{
		return url("/$code");
	}

	function advanceLanguageSwitchURL($code, $referer)
	{
		$appURL = env('APP_URL');
		$subFolder = env('APP_SUBFOLDER');
		$baseURL = urlJoin($appURL, $subFolder);
		
		$uri = $this->uriForLanguageSwitch($baseURL, $referer);
		$cleanURI = $this->cleanURIForLanguageSwitch($uri);
		
		return urlJoin($baseURL, $subFolder, $code, $cleanURI);
	}

	// Util

	function uriForLanguageSwitch($baseURL, $referer)
	{
		return str_contains($referer, $baseURL)
			? str_replace($baseURL, '', $referer)
			: '';
	}

	function cleanURIForLanguageSwitch($uri)
	{
		$codes = array_column(languages(), 'code');
		$chunks = explode('/', $uri);

		$filteredChunks = array_filter($chunks, function ($chunk) use ($codes) {
			return !in_array($chunk, $codes) && !empty($chunk);
		});

		return implode('/', $filteredChunks);
	}


	// Checks

	function isBasicLanguageSwitchSupported($referer, $host)
	{
		return empty($referer) || empty($host) || !str_contains($referer, $host);
	}

	// Request

	function switchLanguageRequest($code)
	{
		$referer = request()->headers->get('referer');
		$host = request()->headers->get('host');

		return $this->switchLanguage($code, $host, $referer);
	}
}
