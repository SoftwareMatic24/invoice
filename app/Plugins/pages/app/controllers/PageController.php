<?php

namespace App\Plugins\Pages\Controllers;

use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Plugins\Pages\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use HTTP;

class PageController extends Controller
{

	// Views

	function savePageView($pageId = NULL)
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$page = !empty($pageId) ? Page::getPage($pageId) : NULL;

		$pageData = [
			"tabTitle" => $pageId === NULl ? ucwords(__("new page")) : ucwords(__("edit page")),
			"backURL" => Util::prefixedURL($config["slug"] . "/manage"),
			"pageName" => $pageId === NULl ? ucwords(__("new page")) : ucwords(__("edit page")),
			"pageSlug" => "pages-save",
			"pluginConfig" => $config,
			"pageId" => $pageId,
			"page" => $page
		];

		return PluginController::loadView(__DIR__, "save-page.blade.php", $pageData);
	}

	function managePagesView(Request $request)
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" =>  ucwords(__("manage pages")),
			"backURL" => Util::prefixedURL("/dashboard"),
			"pageName" => ucwords(__("manage pages")),
			"pageSlug" => "pages-manage",
			"pluginConfig" => $config
		];

		return PluginController::loadView(__DIR__, "manage-pages.blade.php", $pageData);
	}

	/**
	 * Page: Get
	 */

	function page($pageId)
	{
		return Page::getPage($pageId);
	}

	function pages()
	{
		return Page::getPages();
	}

	function getPagesByStatus($status)
	{
		return Page::getPagesByStatus($status);
	}

	// Requests

	function pageRequest($pageId)
	{
		return $this->page($pageId);
	}

	function pagesRequest()
	{
		return $this->pages();
	}

	/**
	 *  Page: Save
	 */

	function savePage($data)
	{

		if (trim($data["slug"]) === "") $data["slug"] = Str::slug($data["title"]);

		$validationRules = [
			"title" => "required|max:255",
			"pageTitle" => "required|max:255",
			"slug" => "required|unique:pages,slug|regex:/^[a-zA-Z0-9\-:\/]+$/",
			"status" => "required"
		];

		if ($data["pageId"] ?? false) {
			$validationRules["slug"] = "required|regex:/^[a-zA-Z0-9\-:\/]+$/";
		}

		$validator = Validator::make(
			$data,
			$validationRules,
			[
				'title.required' => __('title-field-required'),
				'pageTitle' => __('page-title-field-required'),
				'slug.unique' => __('slug-field-exists'),
				"slug.regex" => __("slug-field-invalid")
			]
		);

		if ($validator->fails()) return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);

		Cache::forget("publishedPages");

		$sections = $data["sections"] ?? NULL;
		$meta = $data["meta"] ?? NULL;

		$finalData = [
			"title" => $data["title"] ?? NULL,
			"pageTitle" => $data["pageTitle"],
			"description" => $data["description"],
			"slug" => $data["slug"],
			"status" => $data["status"],
			"featuredImageURL" => $data["featuredImageURL"],
			"featuredVideoURL" => $data["featuredVideoURL"],
			"featuredVideoThumbnailURL" => $data["featuredVideoThumbnailURL"],
			"meta" => $meta,
			"sections" => $sections,
			"languageCode" => $data["languageCode"] ?? null
		];

		if ($data["pageId"] ?? false) {

			$oldPage = Page::getPageBySlug($data["slug"]);

			if ($oldPage !== null && $oldPage["id"] != $data["pageId"]) {
				return HTTP::inBoolArray(false, __('action-required'), __("slug-field-exists"));
			}

			$oldPage2 = Page::getPage($data["pageId"]);
			if ($oldPage2["persistence"] === "permanent" && $data["slug"] !== $oldPage2["slug"]) {
				return HTTP::inBoolArray(false, __('request-failed'), __('default-page-slug-update-notification-description'));
			}

			Page::updatePage($data["pageId"], $finalData);
		} else Page::addPage($finalData);

		return HTTP::inBoolArray(true, __('update-notification-heading'), __('update-notification-description'));
	}

	// Requsts

	function savePageRequest(Request $request)
	{
		$data = $request->post();
		$response = $this->savePage($data);
		return HTTP::inStringResponse($response);
	}


	/**
	 * Page: Delete
	 */


	function deletePage($pageId)
	{

		$oldPage = Page::getPage($pageId);

		if (empty($oldPage)) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		} else if ($oldPage["persistence"] === "permanent") {
			return HTTP::inBoolArray(false, __('request-failed'), __('permanent-page-delete-notification-description'));
		}

		Cache::forget("publishedPages");
		Page::deletePage($pageId);

		return HTTP::inBoolArray(true, __('delete-notification-heading'), __('delete-notification-description'));
	}

	// Request

	function deletePageRequest($pageId)
	{
		$response = $this->deletePage($pageId);
		return HTTP::inStringResponse($response);
	}
}
