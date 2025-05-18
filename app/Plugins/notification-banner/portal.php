<?php

use App\Plugins\NotificationBanner\Controller\NotificationBannerController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware"=>["authCheck:view-notification-banner", "onAllPortal"]], function(){
	Route::get("/", [NotificationBannerController::class, "notificationBannersView"]);
});

Route::group(["middleware"=>["authCheck:add-notification-banner", "onAllPortal"]], function(){
	Route::get("/save", [NotificationBannerController::class, "saveNotificationBannersView"]);
});

Route::group(["middleware"=>["authCheck:update-notification-banner", "onAllPortal"]], function(){
	Route::get("/save/{notificationBannerId}", [NotificationBannerController::class, "saveNotificationBannersView"]);
});

?>