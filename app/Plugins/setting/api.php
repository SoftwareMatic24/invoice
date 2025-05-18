<?php

use App\Plugins\Setting\Controller\ExternalIntegrationController;
use App\Plugins\Setting\Controller\SettingController;
use App\Plugins\Setting\Controller\SitemapController;
use App\Plugins\Setting\Controller\TwoFactorAuthController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware"=>["auth:sanctum", "ability:general-settings", "onAllPortal"]],function(){
	Route::get("/all", [SettingController::class, "getSettings"]);
	Route::put("/update", [SettingController::class, "updateSettingsRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:manage-smtp", "onAllPortal"]],function(){
	Route::put("/smtp/update", [SettingController::class, "updateSMTPRequest"]);
	Route::post("/smtp/test", [SettingController::class, "testSMTPRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:manage-global-scripts", "onAllPortal"]],function(){
	Route::put("/global-scripts/update", [SettingController::class, "updateGlobalScriptsRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:manage-sitemap", "onAllPortal"]],function(){
	Route::get("/sitemap/one", [SitemapController::class, "sitemapRequest"]);
	Route::put("/sitemap/save", [SitemapController::class, "saveSitemapRequest"]);
	Route::put("/sitemap/update/excluded-urls", [SitemapController::class, "updateExcludedURLsRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:manage-external-integrations", "onAllPortal"]],function(){
	Route::put("/external-integrations/save/{slug}", [ExternalIntegrationController::class, "saveExternalIntegrationRequest"]);
	Route::get("/external-integrations/all", [ExternalIntegrationController::class, "externalIntegrations"]);
	Route::get("/external-integrations/slug/one/{slug}", [ExternalIntegrationController::class, "externalIntegration"]);
});

Route::post("/2fa/code/verify", [TwoFactorAuthController::class, "verifyCodeRequest"]);

Route::group(["middleware"=>["auth:sanctum", "ability:manage-2fa", "onAllPortal"]],function(){
	Route::get("/2fa/one", [TwoFactorAuthController::class, "getUser2FaRequest"]);
	Route::post("/2fa/save", [TwoFactorAuthController::class, "save2FaRequest"]);
});


?>