<?php

use App\Plugins\Subscription\Controller\SubscriptionController;
use Illuminate\Support\Facades\Route;


Route::get("/packages/active/all", [SubscriptionController::class, "activePackages"]);

Route::group(["middleware"=>["auth:sanctum", "onAllPortal"]], function(){
	Route::get("/user/one", [SubscriptionController::class, "userSubscriptionRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:view-subscription-package", "onAllPortal"]], function(){
	Route::get("/packages/all", [SubscriptionController::class, "packages"]);
	Route::get("/packages/one/{subscriptionPackageId}", [SubscriptionController::class, "package"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:add-subscription-package", "onAllPortal"]], function(){
	Route::post("/packages/save", [SubscriptionController::class, "savePackageRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:update-subscription-package", "onAllPortal"]], function(){
	Route::put("/packages/save/{subscriptionPackageId}", [SubscriptionController::class, "savePackageRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-subscription-package", "onAllPortal"]], function(){
	Route::delete("/packages/delete/{subscriptionPackageId}", [SubscriptionController::class, "deletePackageRequest"]);
});

/**
 * Classifications
 */

 Route::group(["middleware"=>["auth:sanctum", "ability:manage-subscription-classifications", "onAllPortal"]], function(){
	Route::get("/classifications/all", [SubscriptionController::class, "classifications"]);
	Route::post("/classifications/save", [SubscriptionController::class, "saveClassificationRequest"]);
	Route::put("/classifications/save/{slug}", [SubscriptionController::class, "saveClassificationRequest"]);
	Route::delete("/classifications/delete/slug/{slug}", [SubscriptionController::class, "deleteClassificationBySlugRequest"]);
});

 /**
  * Subscribers
  */

Route::group(["middleware"=>["auth:sanctum", "ability:view-subscription-subscribers", "onAllPortal"]], function(){
	Route::get("/packages/subscribers/all", [SubscriptionController::class, "subscribers"]);
	Route::get("/packages/subscribers/one/user-id/{userId}", [SubscriptionController::class, "subscriber"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:manage-subscription-subscribers", "onAllPortal"]], function(){
	Route::post("/packages/subscribers/save", [SubscriptionController::class, "saveSubscriberRequest"]);
});

?>