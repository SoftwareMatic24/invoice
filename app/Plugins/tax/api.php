<?php

require_once __DIR__."/app/controllers/TaxController.php";

use App\Plugins\Tax\Controllers\Tax\TaxController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware"=>["auth:sanctum", "ability:view-tax"]], function(){
	Route::get("/user/class/all", [TaxController::class, "userTaxClassesRequest"]);
	Route::get("/user/class/one/{taxClassId}", [TaxController::class, "userTaxClassRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:add-tax"]], function(){
	Route::post("/user/class/save", [TaxController::class, "saveTaxClassRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:update-tax"]], function(){
	Route::put("/user/class/save/{taxClassId}", [TaxController::class, "saveTaxClassRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-tax"]], function(){
	Route::delete("/user/class/delete/{taxClassId}", [TaxController::class, "deleteUserTaxClassRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:view-shipping"]], function(){
	Route::get("/user/shipping-class/all", [TaxController::class, "userShippingClassesRequest"]);
	Route::get("/user/shipping-class/one/{shippingClassId}", [TaxController::class, "userShippingClassRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:add-shipping"]], function(){
	Route::post("/user/shipping-class/save", [TaxController::class, "saveUserShippingClassRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:update-shipping"]], function(){
	Route::put("/user/shipping-class/save/{shippingClassId}", [TaxController::class, "saveUserShippingClassRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-shipping"]], function(){
	Route::delete("/user/shipping-class/delete/{shippingClassId}", [TaxController::class, "deleteUserShippingClassRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:tax-setting"]], function(){
	Route::get("/user/settings/all", [TaxController::class, "userSettingsRequest"]);
	Route::put("/user/settings/update", [TaxController::class, "updateUserSettingsRequest"]);
});


?>