<?php


use App\Plugins\Language\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

Route::get("/world-wide", [LanguageController::class, "worldWideLanguages"]);
Route::get("/all", [LanguageController::class, "languages"]);
Route::get("/one/{code}", [LanguageController::class, "languageByCode"]);

Route::group(["middleware"=>["auth:sanctum","ability:view-language", "onAllPortal"]], function(){
	Route::get("/plugin-slugs", [LanguageController::class, "pluginSlugsHavingLanguage"]);
	Route::post("/translations", [LanguageController::class, "translationsRequest"]);
	Route::put("/translations/save", [LanguageController::class, "saveTranslationsRequest"]);
});

Route::group(["middleware"=>["auth:sanctum","ability:add-language", "onAllPortal"]], function(){
	Route::post("/save", [LanguageController::class, "saveLanguageRequest"]);
});

Route::group(["middleware"=>["auth:sanctum","ability:update-language", "onAllPortal"]], function(){
	Route::put("/save/{code}", [LanguageController::class, "saveLanguageRequest"]);
});

Route::group(["middleware"=>["auth:sanctum","ability:delete-language", "onAllPortal"]], function(){
	Route::delete("/delete/{code}", [LanguageController::class, "deleteLangugeRequest"]);
});

Route::group(["middleware"=>["auth:sanctum","ability:language-settings", "onAllPortal"]], function(){
	Route::put("/settings", [LanguageController::class, "saveLanguageSettingsRequest"]);
});



?>