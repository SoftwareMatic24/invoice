<?php

use App\Plugins\Language\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;


Route::group(["middleware"=>["authCheck:view-language", "onAllPortal"]], function(){
	Route::get("/manage", [LanguageController::class, "languagesView"]);
});

Route::group(["middleware"=>["authCheck:add-language", "onAllPortal"]], function(){
	Route::get("/save", [LanguageController::class, "saveLanguageView"]);
	Route::get("/translations", [LanguageController::class, "translationsView"]);
});

Route::group(["middleware"=>["authCheck:update-language", "onAllPortal"]], function(){
	Route::get("/save/{code}", [LanguageController::class, "saveLanguageView"]);
});

Route::group(["middleware"=>["authCheck:language-settings", "onAllPortal"]], function(){
	Route::get("/settings", [LanguageController::class, "settingsView"]);
});

?>