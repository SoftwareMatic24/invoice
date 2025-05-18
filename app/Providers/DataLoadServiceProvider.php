<?php

namespace App\Providers;

use App\Models\Plugin;
use App\Models\StorageRoleAccess;
use App\Models\Theme;
use App\Plugins\Appearance\Models\Branding;
use App\Plugins\Components\Model\Component;
use App\Plugins\Currency\Model\Currency;
use App\Plugins\Language\Models\Language;
use App\Plugins\Pages\Models\Page;
use App\Plugins\Setting\Model\Setting;
use App\Plugins\SocialMedia\Model\SocialMediaLinks;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Exception;

require_once __DIR__."/../Plugins/media-center/app/models/Media.php";
require_once __DIR__."/../Plugins/language/app/models/Language.php";
require_once __DIR__."/../Plugins/currency/app/models/Currency.php";
require_once __DIR__."/../Plugins/pages/app/models/Page.php";
require_once __DIR__."/../Plugins/pages/app/models/Pagei18n.php";

class DataLoadServiceProvider extends ServiceProvider
{

	public function register(): void {}

	public function boot(): void
	{
		try {
			$this->run();
		} catch (Exception $e) {
			echo $e->getMessage();
			exit;
		}
	}

	function run()
	{

		try {
			$this->cacheActivePlugins();
			$this->cacheActiveThemes();
			$this->cachePublishedPages();
			$this->cacheSettings();
			$this->cacheAppearance();
			$this->cacheStorageRoleAccess();
			$this->cacheSocialMediaLinks();
			$this->cacheComponents();
			$this->cacheLanguages();
			$this->cacheCurrency();
			$this->cacheSMTP();

			$primaryLanguage = Cache::get('primaryLanguage');
			App::setLocale($primaryLanguage['code']);

		} catch (Exception $e) {
			
		}

		$this->setAppLanguageFromURL();
	
		$this->app->bind("clearCachedData", function () {
			$this->clearCache();
		});
	}

	function cacheActivePlugins()
	{
		if (!Cache::has("activePlugins")) {
			$activePlugins = Plugin::getActivePlugins();
			Cache::put("activePlugins", $activePlugins->toArray(), now()->addDay());
		}
	}

	function cacheActiveThemes()
	{
		if (!Cache::has("activeTheme")) {
			$activeTheme = Theme::getActiveTheme();
			if ($activeTheme !== NULL) Cache::put("activeTheme", $activeTheme->toArray(), now()->addDay());
		}
	}

	function cacheStorageRoleAccess()
	{
		if (!Cache::has("storageRoleAccess")) {
			$access = StorageRoleAccess::getRows()->toArray();
			Cache::put("storageRoleAccess", $access);
		}
	}

	function cacheSettings()
	{
	
		if ($this->isPluginActive('setting') && !Cache::has("settings")) {
			require_once __DIR__ . "/../Plugins/setting/app/models/Setting.php";
			$settings = Setting::getSettings()->toArray();
			Cache::put("settings", $settings, now()->addDay());
		}
	}

	function cacheAppearance()
	{
		if ($this->isPluginActive('appearance') && !Cache::has('branding')) {
			require_once __DIR__ . "/../Plugins/appearance/app/models/Branding.php";
			$branding = Branding::getBranding()->toArray();
			Cache::put('branding', $branding, now()->addDay());
		}
	}

	function cachePublishedPages()
	{
		if ($this->isPluginActive('pages') && !Cache::has("publishedPages")) {
			require_once __DIR__ . "/../Plugins/pages/app/models/Page.php";
			$publishedPages = Page::getPagesByStatus("publish")->toArray();
			Cache::put("publishedPages", $publishedPages, now()->addDay());
		}
	}

	function cacheSocialMediaLinks()
	{
		if ($this->isPluginActive('social-media') && !Cache::has("socialMediaLinks")) {
			require_once __DIR__ . "/../Plugins/social-media/app/models/SocialMediaLinks.php";
			$socialLinks = SocialMediaLinks::getSocialMediaLinks()->toArray();
			Cache::put("socialMediaLinks", $socialLinks, now()->addDay());
		}
	}

	function cacheComponents()
	{
		if ($this->isPluginActive('components') && !Cache::has("components")) {

			require_once __DIR__ . "/../Plugins/components/app/models/Component.php";
			require_once __DIR__ . "/../Plugins/components/app/models/ComponentGroup.php";
			require_once __DIR__ . "/../Plugins/components/app/models/ComponentDataSection.php";
			require_once __DIR__ . "/../Plugins/components/app/models/ComponentGroupSchema.php";
			require_once __DIR__ . "/../Plugins/components/app/models/ComponentDataSectionData.php";
			require_once __DIR__ . "/../Plugins/components/app/models/ComponentDataSectioni18n.php";
			require_once __DIR__ . "/../Plugins/components/app/models/ComponentDataSectionDatai18n.php";

			$components = Component::getComponentsFormatByKeys()->toArray();
			Cache::put("components", $components, now()->addDay());
		}
	}

	function cacheLanguages()
	{
		if ($this->isPluginActive('language') && !Cache::has("languages")) {
			require_once __DIR__ . "/../Plugins/language/app/models/Language.php";

			$languages = Language::getLanguages("active", ["columns" => ["name", "code", "type", "direction"]])->toArray();

			$primaryLanguage = array_filter($languages, function ($language) {
				if ($language["type"] === "primary") return true;
			});

			$primaryLanguage = reset($primaryLanguage);
			Cache::put("primaryLanguage", $primaryLanguage, now()->addDay());
			Cache::put("languages", $languages, now()->addDay());
		}
	}

	function cacheCurrency(){

		if ($this->isPluginActive('currency') && !Cache::has("currencies")) {
			
			require_once __DIR__ . "/../Plugins/currency/app/models/Currency.php";
			
			$currencies = Currency::getCurrencies()->toArray();

			$primaryCurrency = array_filter($currencies, function ($currency) {
				if ($currency["type"] === "primary") return true;
			});

			$primaryCurrency = reset($primaryCurrency);

			Cache::put("primaryCurrency", $primaryCurrency, now()->addDay());
			Cache::put("currencies", $currencies, now()->addDay());
		}
	}

	function cacheSMTP(){
		$smtpHost = Cache::get("settings")["smtp-host"]["column_value"] ?? "";
		$smtpPort = Cache::get("settings")["smtp-port"]["column_value"] ?? "";
		$smtpEmail = Cache::get("settings")["smtp-email"]["column_value"] ?? "";
		$smtpPassword = Cache::get("settings")["smtp-password"]["column_value"] ?? "";
		$smtpFromName = Cache::get("settings")["smtp-from-name"]["column_value"] ?? "";
		$smtpEncryption = Cache::get("settings")["smtp-encryption"]["column_value"] ?? "";
		$smtpDomain = Cache::get("settings")["smtp-domain"]["column_value"] ?? "";

		Config::set("mail.mailers.smtp.host", $smtpHost);
		Config::set("mail.mailers.smtp.port", $smtpPort);
		Config::set("mail.mailers.smtp.encryption", $smtpEncryption);
		Config::set("mail.mailers.smtp.username", $smtpEmail);
		Config::set("mail.mailers.smtp.password", $smtpPassword);
		Config::set("mail.mailers.smtp.local_domain", $smtpDomain);
		Config::set("mail.from.address", $smtpEmail);
		Config::set("mail.from.name", $smtpFromName);
	}

	function clearCache(){
		Cache::forget("activePlugins");
		Cache::forget("activeTheme");
		Cache::forget("publishedPages");
		Cache::forget("settings");
		Cache::forget('branding');
		Cache::forget("storageRoleAccess");
		Cache::forget("socialMediaLinks");
		Cache::forget("components");
		Cache::forget("languages");
		Cache::forget("primaryLanguage");
		Cache::forget("primaryCurrency");
		Cache::forget("currencies");
	}


	function isPluginActive($slug)
	{
		$activePlugins = Cache::get("activePlugins");
		$activePluginSlugs = array_column($activePlugins, "slug");
		return in_array($slug, $activePluginSlugs);
	}

	function setAppLanguageFromURL()
	{
		
		$languages = Cache::get('languages');
		$languagesCodes = array_column($languages ?? [], 'code');

		$requestURI = request()->getUri();
		$requestURIChunks = explode('/', $requestURI);

		if(in_array('switch', $requestURIChunks) && in_array('language', $requestURIChunks)) return;

		$code = NULL;
		foreach ($languagesCodes as $langCode) {
			if (in_array($langCode, $requestURIChunks)) {
				$code = $langCode;
				break;
			}
		}

		if (empty($code) && !empty(request()->cookie('language')) && request()->ajax()) {
			$code = request()->cookie('language');
		}

		if (!empty($code)) {
			App::setLocale($code);
			setcookie('language', $code, false, '/');
		} else setcookie('language', '', false, '/');
	}
}
