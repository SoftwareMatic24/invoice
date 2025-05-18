<?php

use App\Plugins\Subscription\Controller\SubscriptionController;
use Illuminate\Support\Facades\Route;


Route::get("/packages/subscription-redirect", [SubscriptionController::class, "subscriptionRedirectRequest"]);

Route::group(["middleware" => ["authCheck", "onAllPortal"]], function () {
	Route::get("/packages/subscribe/{subscriptionPackageId}/{paymentMethodSlug}", [SubscriptionController::class, "subscribeRequest"]);
	Route::get("/packages/status", [SubscriptionController::class, "userPackageUpdateView"]);
});

/**
 * User Packages
 */

/**
 * Packages
 */

Route::group(["middleware" => ["authCheck:view-subscription-package", "onAllPortal"]], function () {
	Route::get("/manage", [SubscriptionController::class, "subscriptionPackagesView"]);
});

Route::group(["middleware" => ["authCheck:add-subscription-package", "onAllPortal"]], function () {
	Route::get("/save", [SubscriptionController::class, "saveSubscriptionPackageView"]);
});

Route::group(["middleware" => ["authCheck:update-subscription-package", "onAllPortal"]], function () {
	Route::get("/save/{subscriptionPackageId}", [SubscriptionController::class, "saveSubscriptionPackageView"]);
});

/**
 * Classifications
 */

Route::group(["middleware" => ["authCheck:manage-subscription-classifications", "onAllPortal"]], function () {
	Route::get("/classifications/save", [SubscriptionController::class, "saveClassificationView"]);
	Route::get("/classifications/save/{slug}", [SubscriptionController::class, "saveClassificationView"]);
	Route::get("/classifications", [SubscriptionController::class, "classificationsView"]);
});


/**
 * Subscribers
 */

Route::group(["middleware" => ["authCheck:view-subscription-subscribers", "onAllPortal"]], function () {
	Route::get("/subscribers", [SubscriptionController::class, "subscribersView"]);
});

Route::group(["middleware" => ["authCheck:manage-subscription-subscribers", "onAllPortal"]], function () {
	Route::get("/subscribers/save", [SubscriptionController::class, "saveSubscriberView"]);
	Route::get("/subscribers/save/{subscriberUserId}", [SubscriptionController::class, "saveSubscriberView"]);
});
