<?php

use App\Plugins\Appearance\Controller\AppearanceController;
use App\Plugins\Menu\Controller\MenuController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware"=>["authCheck:view-menu", "onAllPortal"]], function(){
	Route::get("/menu", [MenuController::class, "menusView"]);
});

Route::group(["middleware"=>["authCheck:manage-branding", "onAllPortal"]], function(){
	Route::get("/branding", [AppearanceController::class, "brandingView"]);
	Route::get("/account", [AppearanceController::class, "accountBrandingView"]);
});

// Themes

Route::group(["middleware"=>["authCheck:view-theme", "onAllPortal"]], function(){
	Route::get("/themes", [AppearanceController::class, "themesView"]);
});

Route::group(["middleware"=>["authCheck:customize-theme", "onAllPortal"]], function(){
	Route::get("/themes/customize/{themeSlug}", [AppearanceController::class, "customizeTheme"]);
});

?>