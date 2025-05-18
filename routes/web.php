<?php

use App\Classes\Util;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\PrivateStorageController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ResetController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;


// Cache
Route::get("/cache/clear", function () {
	App::make("clearCachedData");
	echo "Cache cleared!";
});

// Auth User
Route::group(["middleware" => ["authCheck", "onAllPortal"]], function () {
	Route::get(Util::prefixedRelativeURL("/dashboard"), [PortalController::class, "dashboardView"]);
	Route::get(Util::prefixedRelativeURL("/profile"), [PortalController::class, "profileView"]);
	Route::get(Util::prefixedRelativeURL("/activity"), [PortalController::class, "activityLogView"]);
	// Route::get(Util::prefixedRelativeURL("/re-auth"), [UserController::class, "reAuth"]);

	Route::get(Util::prefixedRelativeURL("/notifications"), [NotificationController::class, "notificationsView"]);
	Route::get(Util::prefixedRelativeURL("/notifications/open/{notificationId}"), [NotificationController::class, "notificationOpen"]);
});

// Auth Admin
Route::group(["middleware" => ["authCheck:admin", "onAllPortal"]], function () {
	Route::get(Util::prefixedRelativeURL("/reset"), [ResetController::class, "resetView"]);
});


// Private Storage
//Route::get("/private-storage", [PrivateStorageController::class, "storageRequest"]);


// Project
ProjectController::init("web");
