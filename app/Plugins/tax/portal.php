<?php

require_once __DIR__."/app/controllers/TaxController.php";

use App\Plugins\Tax\Controllers\Tax\TaxController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware"=>["authCheck:view-tax"]], function(){
	Route::get("/tax", [TaxController::class, "taxesView"]);
});

Route::group(["middleware"=>["authCheck:add-tax"]], function(){
	Route::get("/save", [TaxController::class, "saveTaxView"]);
});

Route::group(["middleware"=>["authCheck:update-tax"]], function(){
	Route::get("/save/{taxClassId}", [TaxController::class, "saveTaxView"]);
});

Route::group(["middleware"=>["authCheck:view-shipping"]], function(){
	Route::get("/shipping", [TaxController::class, "shippingsView"]);
});

Route::group(["middleware"=>["authCheck:add-shipping"]], function(){
	Route::get("/shipping/save", [TaxController::class, "saveShippingClassView"]);
});

Route::group(["middleware"=>["authCheck:update-shipping"]], function(){
	Route::get("/shipping/save/{shippingClassId}", [TaxController::class, "saveShippingClassView"]);
});


Route::group(["middleware"=>["authCheck:tax-setting"]], function(){
	Route::get("/settings", [TaxController::class, "settingsView"]);
});


?>