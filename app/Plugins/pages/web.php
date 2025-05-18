<?php

use App\Http\Controllers\ThemeController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;


$activeTheme = Cache::get("activeTheme");
$languages = Cache::get("languages");

$allURI = [];
$currentURL = url()->current();
$currentURLChunks = explode("/", $currentURL);

$urlAndPrimaryLanguage = getURLLanguageAndPrimaryLanguage($currentURLChunks, $languages ?? []);

$urlLanguage = $urlAndPrimaryLanguage["urlLanguage"];
$primaryLanguage = $urlAndPrimaryLanguage["primaryLanguage"];

try {
	$pages = Cache::get("publishedPages") ?? [];
} catch (Exception $e) {
	$pages = [];
}

foreach (Route::getRoutes() as $route) {
	$allURI[] = $route->uri();
}

foreach ($pages as $page) {
	$pageSlug = $page["slug"];
	$pageSlugURI = $pageSlug;
	$fileName = $pageSlug;

	if ($page["hard_url"] !== NULL) {
		$pageSlugURI = $page["hard_url"];
		$convertResponse = convertParamURL($pageSlugURI);
		if($convertResponse !== NULL) $pageSlugURI = $convertResponse["pageSlugURI"];
	}
	
	$convrtResponse = convertParamURL($pageSlug);
	
	if($convrtResponse !== NULL) {
		$fileName = $convrtResponse["fileName"];
		$pageSlugURI = $convrtResponse["pageSlugURI"];
		if (sizeof($convrtResponse["params2"]) > 0) $fileName = $fileName . "-" . implode("-", $convrtResponse["params2"]);
	}

	$filePath = "themes/" . $activeTheme["slug"] . "/pages/" . $fileName;
	if (in_array($pageSlugURI, $allURI)) continue;

	if (file_exists(base_path('/resources/views/'.$filePath.'.blade.php'))) $file = $filePath;
	else $file = "themes/" . $activeTheme["slug"] . "/pages/page";

	if ($urlLanguage !== false && $urlLanguage["code"] !== $primaryLanguage["code"]) $pageSlugURI = "{lang?}/" . $pageSlugURI;
	
	Route::get($pageSlugURI, [ThemeController::class, "pageView"])->defaults("page", [
		"slug" => $pageSlug,
		"file" => $file,
		"urlLanguage" => $urlLanguage,
		"primaryLanguage" => $primaryLanguage
	]);
	
}

// other
function convertParamURL($relativeURL)
{
	$output = NULL;
	$params2 = [];
	
	$slugChunks = explode("/:", $relativeURL);
	
	if (sizeof($slugChunks) > 1) {
		
		$fileName = $slugChunks[0];
		array_shift($slugChunks);

		$params = [];
		foreach ($slugChunks as $param) {
			$paramArr = explode("/", $param);

			foreach ($paramArr as $pIndex => $p) {
				if ($pIndex === 0) {
					$params[] = sprintf("%s%s%s", "{", $p, "}");
					$params2[] = "param";
				} else {
					$params[] = $p;
					$params2[] = $p;
				}
			}
		}

		$pageSlugURI = $fileName . "/" . join("/", $params);

		$output = [
			"fileName"=>$fileName,
			"pageSlugURI"=>$pageSlugURI,
			"params2"=>$params2
		];
	}
	
	return $output;
}

function getURLLanguageAndPrimaryLanguage($urlChunks, $languages)
{
	$match = false;
	$primaryLanguage = false;

	foreach ($urlChunks as $chunk) {
		foreach ($languages as $lang) {
			if ($chunk === $lang["code"]) $match = $lang;
			if ($lang["type"] === "primary") $primaryLanguage = $lang;
		}

		if ($match !== false && $primaryLanguage !== false) break;
	}
	return [
		"urlLanguage" => $match,
		"primaryLanguage" => $primaryLanguage
	];
}
