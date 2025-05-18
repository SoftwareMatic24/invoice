<?php


use App\Plugins\NotificationBanner\Controller\NotificationBannerController;
use Illuminate\Support\Facades\Route;

Route::get("/all", [NotificationBannerController::class, "getNotificationBanners"]);

Route::group(["middleware"=>["auth:sanctum", "ability:view-notification-banner", "onAllPortal"]], function(){
	Route::get("/one/{notificationBannerId}", [NotificationBannerController::class, "getNotificationBanner"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:add-notification-banner", "onAllPortal"]], function(){
	Route::post("/save", [NotificationBannerController::class, "saveNotificationBannerRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:update-notification-banner", "onAllPortal"]], function(){
	Route::put("/save/{notificationBannerId}", [NotificationBannerController::class, "saveNotificationBannerRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-notification-banner", "onAllPortal"]], function(){
	Route::delete("/delete/{notificationBannerId}", [NotificationBannerController::class, "deleteNotificationBanner"]);
});

?>