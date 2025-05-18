<?php

namespace App\Http\Controllers;

use App\Classes\Util;
use App\Models\Notification;
use App\Models\NotificationRead;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

	function notificationsView()
	{

		$pageData = [
			"tabTitle" => __("all notifications"),
			"pageName"=> __("notifications"),
			"pageSlug" => "notifications",
			"backURL" => Util::prefixedURL("/dashboard"),
		];

		return View("portal/notifications", $pageData);
	}


	// methods

	function notificationOpen($notificationId)
	{

		$userRole = request()["loggedInUser"]["role_title"];
		$userId = request()["loggedInUser"]["id"];

		$notification = Notification::getNotification($notificationId);

		
		if ($notification === NULL) return redirect(Util::prefixedURL("/dashboard"));

		if ($notification["user_notifications"] ?? false) {

			$match = false;

			foreach ($notification["user_notifications"] as $user) {
				if ($user["user_id"] === $userId) $match = true;
			}

	
			if ($match === false) {
				return redirect(Util::prefixedURL("/dashboard"));
				exit;
			}
		} else if ($notification["role_notifications"] ?? false) {

			
			$match = false;

			foreach ($notification["role_notifications"] as $role) {
				if ($role["role"] === $userRole) $match = true;
			}

		
			if ($match === false) {
				return redirect(Util::prefixedURL("/dashboard"));
				exit;
			}
		}

	
		$read = NotificationRead::getReadByUserIdNotificationId($userId, $notification["id"]);
		if ($read === NULL) NotificationRead::addRead($userId, $notification["id"]);

		if ($notification["link_type"] === "external") return redirect($notification["link"]);
		else if ($notification["link_type"] === "internal") return redirect(url($notification["link"]));
		else if ($notification["link_type"] === "none")	return redirect(url()->previous() ?? url(Util::prefixedURL("/dashboard")));
	}

	function getNotificationsByUserIdAndRole($userId, $userRole)
	{
		return Notification::getNotificationsByUserAndRole($userId, $userRole);
	}

	// requests

	function myNotificationsRequest(Request $request)
	{
		$userId = $request->user()->id;
		$userRole = $request->user()->role_title;

		return $this->getNotificationsByUserIdAndRole($userId, $userRole);
	}
}
