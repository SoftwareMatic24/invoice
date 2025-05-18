<?php

use App\Plugins\PaymentMethods\Controller\PaymentMethodController;
use App\Plugins\Setting\Controller\ExternalIntegrationController;
use App\Plugins\Setting\Controller\SettingController;
use App\Plugins\Setting\Controller\SitemapController;
use Illuminate\Support\Facades\Route;


Route::group(["middleware"=>["authCheck:user-payment-method", "onAllPortal"]],function(){
	Route::get("/payment-methods", [PaymentMethodController::class, "paymentMethodsView"]);
});

Route::group(["middleware"=>["authCheck:general-settings", "onAllPortal"]],function(){
	Route::get("/general", [SettingController::class, "generalSettingsView"]);
});

Route::group(["middleware"=>["authCheck:manage-smtp", "onAllPortal"]],function(){
	Route::get("/smtp", [SettingController::class, "smtpView"]);
});

Route::group(["middleware"=>["authCheck:manage-global-scripts", "onAllPortal"]],function(){
	Route::get("/global-scripts", [SettingController::class, "globalScriptsView"]);
});

Route::group(["middleware"=>["authCheck:manage-sitemap", "onAllPortal"]],function(){
	Route::get("/sitemap", [SitemapController::class, "sitemapView"]);
});


Route::group(["middleware"=>["authCheck:manage-external-integrations", "onAllPortal"]],function(){
	Route::get("/external-integrations", [ExternalIntegrationController::class, "externalIntegrationsView"]);
	Route::get("/external-integrations/google-oauth", [ExternalIntegrationController::class, "googleOAuthView"]);
});

Route::group(["middleware"=>["authCheck:manage-2fa", "onAllPortal"]],function(){
	Route::get("/2fa", [ExternalIntegrationController::class, "twoFactorAuthView"]);
});

?>