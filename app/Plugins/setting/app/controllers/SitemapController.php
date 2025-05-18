<?php

namespace App\Plugins\Setting\Controller;

use App\Classes\DateTime as MyDateTime;
use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Http\Controllers\ThemeController;
use App\Plugins\Pages\Models\Page;
use App\Plugins\Posts\Model\Post;
use App\Plugins\Posts\Model\PostCategory;
use App\Plugins\Posts\Model\PostClassification;
use App\Plugins\Setting\Model\Sitemap;
use Illuminate\Http\Request;
use DateTime;
use HTTP;

class SitemapController extends Controller
{

	function sitemapView()
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => __('sitemap'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('sitemap'),
			"pageSlug" => "sitemap",
			"pluginConfig" => $config
		];

		return PluginController::loadView(__DIR__, "sitemap.blade.php", $pageData);
	}

	/**
	 * Sitemap: Get
	 */

	function getSitemap()
	{
		return Sitemap::getSitemap();
	}

	function generateSitemap($excludeURLs = [])
	{

		$urls = [];
		$finalURLs = [];
		// Pages
		$pages = Page::getPagesByStatus("publish");
		foreach ($pages as $pageIndex => $page) {

			$pageSlug = $page["slug"];
			if ($page["hard_url"] !== NULL) $pageSlug = $page["hard_url"];
			if ($page["hard_url"] === "#") continue;

			$priority = "0.8";
			if ($pageIndex === 0) $priority = "1.0";
			else if (strpos($pageSlug, config("app.portal_prefix")) !== false && config("app.protal_prefix") !== "" && config("app.portal_prefix") !== "/") $priority = 0.5;

			$lastModified = $page["update_datetime"] ?? $page["create_datetime"];
			$datetime = DateTime::createFromFormat(MyDateTime::$dateTimeFormat, $lastModified);
			$lastModified = $datetime->format('Y-m-d\TH:i:sO');

			$urls[] = [
				"loc" => url($pageSlug),
				"priority" => $priority,
				"lastmod" => $lastModified
			];
		}

		// Posts
		$posts = Post::getPosts("publish");
		foreach ($posts as $post) {

			$priority = "0.8";

			$lastModified = $post["update_datetime"] ?? $page["create_datetime"];
			$datetime = DateTime::createFromFormat(MyDateTime::$dateTimeFormat, $lastModified);
			$lastModified = $datetime->format('Y-m-d\TH:i:sO');

			$urls[] = [
				"loc" => url($post["classification"]["slug"] . "/" . $post["slug"]),
				"priority" => $priority,
				"lastmod" => $lastModified
			];
		}

		// Post Classifications
		$classifications = PostClassification::getClassifications();
		foreach ($classifications as $classification) {

			$priority = "0.7";

			$lastModified = MyDateTime::getDateTime();
			$datetime = DateTime::createFromFormat(MyDateTime::$dateTimeFormat, $lastModified);
			$lastModified = $datetime->format('Y-m-d\TH:i:sO');

			$urls[] = [
				"loc" => url($classification["slug"]),
				"priority" => $priority,
				"lastmod" => $lastModified
			];
		}

		// Post Categories
		$categories = PostCategory::getCategories();
		foreach ($categories as $category) {

			$priority = "0.7";

			$lastModified = MyDateTime::getDateTime();
			$datetime = DateTime::createFromFormat(MyDateTime::$dateTimeFormat, $lastModified);
			$lastModified = $datetime->format('Y-m-d\TH:i:sO');

			$urls[] = [
				"loc" => url($category["slug"]),
				"priority" => $priority,
				"lastmod" => $lastModified
			];
		}

		// Exclusion
		foreach ($urls as $url) {
			if (in_array($url["loc"], $excludeURLs) !== true) $finalURLs[] = $url;
		}

		$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

		foreach ($finalURLs as $url) {
			$xml .= '  <url>' . "\n";
			$xml .= '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . "\n";
			$xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . "\n";
			$xml .= '    <priority>' . $url['priority'] . '</priority>' . "\n";
			$xml .= '  </url>' . "\n";
		}

		$xml .= '</urlset>' . "\n";

		return response($xml)->header('Content-Type', 'application/xml');
	}

	/**
	 * Sitemap: Save
	 */

	function saveSitemap($status)
	{
		Sitemap::updateSitemapStatus($status);
		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	function updateExcludedURLs($data)
	{
		Sitemap::updateSitemapExcludedURLs($data);
		return HTTP::inBoolArray(true, __('update-notification-heading'), __('update-notification-description'));
	}

	// Request

	function sitemapRequest()
	{
		return $this->getSitemap();
	}

	function saveSitemapRequest(Request $request)
	{
		$data = $request->post();
		$response = $this->saveSitemap($data["status"]);
		return HTTP::inStringResponse($response);
	}

	function updateExcludedURLsRequest(Request $request)
	{
		$data = $request->post();
		$response = $this->updateExcludedURLs($data);
		return HTTP::inStringResponse($response);
	}

	function generateSitemapRequest(Request $request)
	{
		$sitemap = Sitemap::getSitemap();
		if ($sitemap->status !== "active") return (new ThemeController)->fallbackPageView($request);

		$urls = [];
		$excludeURLs = $sitemap["excluded_urls"];
		if ($excludeURLs !== NULL && $excludeURLs !== "") {
			$excludeURLs = explode("\n", $excludeURLs);
			foreach ($excludeURLs as $url) {
				if ($url !== NULL && $url !== "") $urls[] = trim($url);
			}
		}

		return $this->generateSitemap($urls);
	}
}
