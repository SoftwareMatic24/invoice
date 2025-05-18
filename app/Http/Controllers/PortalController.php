<?php

namespace App\Http\Controllers;

use App\Classes\ExtProvider;
use App\Classes\Util;
use App\Models\Action;
use App\Models\Notification;
use App\Models\Plugin;
use App\Models\Role;
use App\Models\User;
use App\Services\ResetService;
use Illuminate\Http\Request;

class PortalController extends Controller
{

	// Views

	function dashboardView(Request $request)
	{
		$userId = $request["loggedInUser"]["id"];
		$pageData = [
			"tabTitle" => __("dashboard"),
			"pageName" => "",
			"pageSlug" => "dashboard",
			"forcePassword" => Action::getActionBySlugUidStatus("FORCE_UPDATE_PASSWORD", $userId, "pending")
		];
		return View("portal/dashboard", $pageData);
	}

	function profileView(Request $request)
	{
		$roles = Role::getRoles();
		$user = User::getUserById($request["loggedInUser"]->id);
		$user = $user->toArray();
		$details = [];

		foreach ($user["details"] ?? [] as $row) {
			$details[$row["column_name"]] = $row["column_value"];
		}

		$pname = env("PORTAL_STYLE_PLUGIN");

		$pageData = [
			"tabTitle" => __("profile"),
			"pageName" => __("profile"),
			"pageSlug" => "profile",
			"roles" => $roles,
			"details" => $details
		];

		$path = "Plugins/$pname";
		$filePath = app_path("$path/views/profile.blade.php");
		if (file_exists($filePath)) return PluginController::loadView(app_path("$path/app/controllers"), "profile.blade.php", $pageData);
		return View("portal/profile", $pageData);
	}

	function activityLogView(Request $request)
	{

		$pageData = [
			"tabTitle" => "Activity Log",
			"pageSlug" => "activity-log"
		];

		return View("portal/activity-log", $pageData);
	}


	// Methods

	function getIPDetail(Request $request)
	{
		$data  = $request->post();
		$ip = $data["ip"];
		return ExtProvider::ipDetails($ip);
	}

	// API

	function genericDataRequest(Request $request)
	{
		$userRole = $request->user()->role_title;
		$userId = $request->user()->id;

		$notifications = Notification::getNotificationsByUserAndRole($userId, $userRole, 3);

		$activePluginSlugs = Plugin::getActivePluginSlugs();
		$data = PluginController::loadPluginsGenericData($activePluginSlugs, $userRole);

		$data["notifications"] = $notifications;
		return $data;
	}
}
