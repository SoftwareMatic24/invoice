<?php

namespace App\Http\Controllers;

use App\Classes\Req;
use App\Classes\Util;
use App\Plugins\Pages\Helpers\Page as HelpersPage;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Theme as ModelsTheme;
use App\Plugins\Language\Models\Language;
use App\Plugins\Pages\Models\Page;
use App\Plugins\Setting\Model\ExternalIntegration;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class ThemeController extends Controller
{	
	// views

	function pageView(Request $request){
		$data = Route::current()->parameters();
		$page = [];
		$dynamicParams = [];
		
		$routesData = [
			"login"=>["googleOAuthSettings"],
			"register"=>["googleOAuthSettings", "roles"],
			"forgot-password"=>["googleOAuthSettings"],
			"reset-password"=>["googleOAuthSettings"],
			"2fa-verify"=>["googleOAuthSettings"],
		];

		
		forEach($data as $paramKey=>$param){
			if($paramKey === "page") $page = $param;
			else $dynamicParams[$paramKey] = $param;
		}
	
		$page = array_merge($page, $dynamicParams);
			
		
		$pageSlug = $page["slug"] ?? "";
		$paramKeys = request()->route()->parameterNames();
		
		$pageData = array_filter(Cache::get("publishedPages"), function($row) use($pageSlug){
			if($row["slug"] === $pageSlug) return $row;
		});

	
		$pageData = array_values($pageData);

		HelpersPage::setSlug($page['slug']);
		
		$featuredImage = $pageData[0]["featured_image"] ?? NULL;
		$featuredVideo = $pageData[0]["featured_video"] ?? NULL;
		$featuredVideoThumbnail = $pageData[0]["featured_video_thumbnail"] ?? NULL;

		$settings = Cache::get("settings");
		$socialMediaLinks = Cache::get("socialMediaLinks");
		
		foreach($paramKeys as $key) {
			$page[$key] = $data[$key] ?? NULL;
		}

		if(isset($routesData[$pageSlug])) {
			foreach($routesData[$pageSlug] as $func){
				if(method_exists($this, $func)) {
					$page[$func] = $this->$func();
				}
			}
		}
	
		$page["settings"] = $settings;
		
		$page["socialMediaLinks"] = $socialMediaLinks;
		
		$page["featuredImage"] = $featuredImage;
		$page["featuredVideo"] = $featuredVideo;
		$page["featuredVideoThumbnail"] = $featuredVideoThumbnail;
		
		
		$bearerToken = $_COOKIE["bt"] ?? NULL;
		$response = Req::getTokenAndUser($bearerToken);

		$user = $response["user"];

		if($user !== NULL){
			$safeUser = Req::safeUser($user);
			$request['loggedInSafeUser'] = $safeUser;
		}

		$langCode = $page["lang"] ?? app()->getLocale();
		

		$primaryLanguage = $page["primaryLanguage"];
		App::setLocale($langCode);

		$page["themeURL"] = function($postFix) use($langCode, $primaryLanguage) {
			return Util::themeURL($postFix, $langCode, $primaryLanguage["code"]);
		};
		
		return View($page["file"], $page);
	}

	function fallbackPageView(Request $request){
		
		
		if($request->ajax() || $request->isJson()) {
			return response()->json(["status"=>"fail", "msg"=>"Route not found."], 404);
			exit;
		}
		
		$page = [
			"slug"=>"404"
		];

		
		$pageData = array_filter(Cache::get("publishedPages"), function($row){
			if($row["slug"] === "404") return $row;
		});

		$pageData = array_values($pageData);
		$meta = json_decode($pageData[0]["meta"] ?? "[]", true);
		$content = json_decode($pageData[0]["content"] ?? "[]", true);

		$page["page_title"] = $pageData[0]["page_title"] ?? NULL;
		$page["content"] = $content;

		$components = Cache::get("components");
		$socialMediaLinks = Cache::get("socialMediaLinks");
		$theme = Cache::get("activeTheme");
		$settings = Cache::get("settings");

		$themeSlug = $theme["slug"];
		$pageData = Page::getPageBySlug($page["slug"]);
		$meta = json_decode($pageData["meta"] ?? "[]", true);

		if(isset($meta["tabTitle"]) && $meta["tabTitle"] !== "") $page["tabTitle"] = $meta["tabTitle"];
		if(isset($meta["metaDescription"]) && $meta["metaDescription"] !== "") $page["metaDescription"] = $meta["metaDescription"];
		if(isset($meta["metaAuthor"]) && $meta["metaAuthor"] !== "") $page["metaAuthor"] = $meta["metaAuthor"];

		$fileRelativePathWithoutName = "themes/$themeSlug/pages/404";
		$theme404Exists = file_exists(resource_path("views/$fileRelativePathWithoutName.blade.php"));
		
		$page["theme"] = $theme;
		$page["settings"] = $settings;
		$page["components"] = $components;
		$page["socialMediaLinks"] = $socialMediaLinks;

		$langCode = $page["lang"] ?? app()->getLocale();
		$primaryLanguage = Language::getLanguageByCode("en");

		$page["primaryLanguage"] = $primaryLanguage;

		App::setLocale($langCode);

		$page["themeURL"] = function($postFix) use($langCode, $primaryLanguage) {
			return Util::themeURL($postFix, $langCode, $primaryLanguage["code"]);
		};
		
		HelpersPage::setSlug($page['slug']);
		
		if($theme404Exists === true) return response()->view($fileRelativePathWithoutName, $page, 404);
		else return abort(404);
	}

	function maintenanceModeView(){
		$settings = Cache::get("settings");
		$theme = Cache::get("activeTheme");
		$themeSlug = $theme["slug"];

		$pageData = [
			"tabTitle"=>"Maintenance Mode",
			"settings"=>$settings
		];

		$fileRelativePathWithoutName = base_path("resources/views/themes/$themeSlug/pages/maintenance");
		$fileRelativePathWithoutName2 = "themes/$themeSlug/pages/maintenance";

		if(!file_exists($fileRelativePathWithoutName.".blade.php")) $fileRelativePathWithoutName2 = "maintenance";
	 	return response()->view($fileRelativePathWithoutName2, $pageData, 503);
	}

	// routes data

	function googleOAuthSettings(){
		$googleOAuthEI = ExternalIntegration::getExternalIntegrationBySlug("google-oauth");
		if($googleOAuthEI !== NULL) $googleOAuthEI = $googleOAuthEI->toArray();
		return $googleOAuthEI;
	}

	function roles(){
		return Role::getRoles();
	}

	// methods

	// TODO:remove
	static function contentView($content = []){

		$layout = "";
		foreach($content as $section){
			$layout .= "<div data-section='".$section["title"]."'>".$section["content"]."</div>";
		}

		$cleanLayout = str_replace("<p><br></p>", "", $layout);
		return $cleanLayout;
	}

	function activateTheme($themeId){

		Cache::forget("activeTheme");

		ModelsTheme::activateTheme($themeId);
		$theme = ModelsTheme::getActiveTheme();
		Session::put("theme", $theme);
		return ["status"=>"success", "msg"=>"Theme is activated."];
	}

	static function loadFile($themeSlug, $relativePath, $options = [])
	{
		$path = "resources/views/themes/$themeSlug/$relativePath";
		return ProjectController::loadFile($path, $options);
	}
	
	// apis

	function getThemes(){
		return ModelsTheme::getThemes();
	}

	 function getActiveTheme(){
		return ModelsTheme::getActiveTheme();
	 }

	 function activateThemeRequest($themeId){
		return $this->activateTheme($themeId);
	 }

}
