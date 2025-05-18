<?php

namespace App\Plugins\NotificationBanner\Controller;

use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Plugins\NotificationBanner\Models\NotificationBanner;
use HTTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class NotificationBannerController extends Controller
{

	// views

	function notificationBannersView()
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => __('notification banners'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('notification banners'),
			"pageSlug" => "notification-banner",
			"pluginConfig" => $config
		];

		return PluginController::loadView(__DIR__, "notification-banners.blade.php", $pageData);
	}

	function saveNotificationBannersView($notificationBannerId = NULL)
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => $notificationBannerId === NULL ? "Add Notification Banner" : "Edit Notification Banner",
			"backURL" => Util::prefixedURL($config["slug"] . "/"),
			"pageName" => $notificationBannerId === NULL ? "Add Notification Banner" : "Edit Notification Banner",
			"pageSlug" => "notification-banner",
			"pluginConfig" => $config,
			"notificationBannerId" => $notificationBannerId
		];

		return PluginController::loadView(__DIR__, "save-notification-banner.blade.php", $pageData);
	}


	/**
	 * Notification Banner: Get
	 */

	function getNotificationBanners()
	{
		return NotificationBanner::getNotificationBanners();
	}

	function getNotificationBanner($notificationBannerId)
	{
		return NotificationBanner::getNotificationBanner($notificationBannerId);
	}

	/**
	 * Notification Banner: Save
	 */

	function saveNotificationBanner($notificationBannerId = NULL, $data)
	{

		$validator = Validator::make($data, [
			'text' => 'required',
			'status' => 'required|in:active,inactive',
			'type' => 'required|in:portal,web'

		], [
			'text.required' => __('text-field-required')
		]);

		if ($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}

		if ($notificationBannerId === NULL) {
			$notificationBanner = NotificationBanner::addNotificationBanner($data);
			$notificationBannerId = $notificationBanner["id"];
		} else NotificationBanner::updateNotificaionBanner($notificationBannerId, $data);

		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	// Util

	function saveNotificationBannerRequest(Request $request, $notificationBannerId = NULL)
	{
		$data = $request->post();
		$response = $this->saveNotificationBanner($notificationBannerId, $data);
		return HTTP::inStringResponse($response);
	}

	/**
	 * Notification Banner: Delete
	 */

	function deleteNotificationBanner($notificationBannerId)
	{
		NotificationBanner::deleteNotificationBanner($notificationBannerId);
		return HTTP::inStringResponse(HTTP::inBoolArray(true, __('delete-notification-heading'), __('delete-notification-description')));
	}
}
