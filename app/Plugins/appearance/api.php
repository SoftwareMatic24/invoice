<?php

use App\Plugins\Appearance\Controller\AppearanceController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware"=>["auth:sanctum", "ability:manage-branding", "onAllPortal"]], function(){
	Route::put("/branding/update", [AppearanceController::class, "updateBrandingRequest"]);
	Route::put("/account-branding/update", [AppearanceController::class, "updateAccountBrandingRequest"]);
});

// Themes

Route::group(["middleware"=>["auth:sanctum", "ability:view-theme", "onAllPortal"]], function(){
	Route::get("/themes/{themeSlug}/one", [AppearanceController::class, "theme"]);
	Route::put("/themes/customize/{themeSlug}/update", [AppearanceController::class, "updateCustomizedThemeRequest"]);
	Route::get("/themes/customize/{themeSlug}/reset-colors", [AppearanceController::class, "resetThemeColors"]);
});

?>