<?php

use App\Plugins\Currency\Controller\CurrencyController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware"=>["authCheck:view-currency", "onAllPortal"]], function(){
	Route::get("/manage", [CurrencyController::class, "manageCurrencyView"]);
});

Route::group(["middleware"=>["authCheck:add-currency", "onAllPortal"]], function(){
	Route::get("/save", [CurrencyController::class, "saveCurrencyView"]);
});

Route::group(["middleware"=>["authCheck:update-currency", "onAllPortal"]], function(){
	Route::get("/save/{currencyId}", [CurrencyController::class, "saveCurrencyView"]);
});

?>