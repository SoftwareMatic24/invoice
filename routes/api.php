<?php

use App\Http\Controllers\LockController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PluginController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\ThemeController;
use Illuminate\Support\Facades\Route;

// Auth User
Route::group(["middleware" => ["auth:sanctum", "onAllPortal"]], function () {
	Route::get("/portal/generic", [PortalController::class, "genericDataRequest"]);
	Route::post("/portal/ip-detail", [PortalController::class, "getIPDetail"]);

	Route::get("/notifications/my", [NotificationController::class, "myNotificationsRequest"]);
	Route::get("/portal/active-plugins/slugs", [PluginController::class, "getActivePluginSlugs"]);
});


// Lock
Route::get("/lock/status/slug/{lockSlug}", [LockController::class, "lockStatus"]);

// Theme
Route::group(["middleware" => ["auth:sanctum", "ability:view-theme"]], function () {
	Route::get("/themes/all", [ThemeController::class, "getThemes"]);
});

Route::group(["middleware" => ["auth:sanctum", "ability:install-activate-deactivate-theme"]], function () {
	Route::get("/themes/activate/{themeId}", [ThemeController::class, "activateThemeRequest"]);
});

// Reset

Route::group(["middleware" => ["auth:sanctum", "ability:admin"]], function () {
	Route::post("/resets/reset/all", [ResetController::class, "resetAllRequest"]);
	Route::post("/resets/reset", [ResetController::class, "resetRequest"]);
	Route::get("/resets/active/all", [ResetController::class, "activeResetsRequest"]);
	Route::put("/resets/settings/update/one", [ResetController::class, "updateSettingRequest"]);
});

// Project
ProjectController::init("api");
