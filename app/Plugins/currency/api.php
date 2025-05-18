<?php

use App\Plugins\Currency\Controller\CurrencyController;
use Illuminate\Support\Facades\Route;

Route::get("/switch/{currencyId}", [CurrencyController::class, "switchCurrency"]);

Route::get("/all", [CurrencyController::class, "currencies"]);
Route::get("/one/{currencyId}", [CurrencyController::class, "currency"]);

Route::group(["middleware"=>["auth:sanctum", "ability:add-currency", "onAllPortal"]],function(){
	Route::post("/save", [CurrencyController::class, "saveCurrencyRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:update-currency", "onAllPortal"]],function(){
	Route::put("/save/{currencyId}", [CurrencyController::class, "saveCurrencyRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-currency", "onAllPortal"]],function(){
	Route::delete("/delete/{currencyId}", [CurrencyController::class, "deleteCurrencyRequest"]);
});

?>