<?php

use App\Plugins\Currency\Controller\CurrencyController;
use Illuminate\Support\Facades\Route;

Route::get("/currency/switch/{currencyId}/{redirect}", [CurrencyController::class, "switchCurrency"]);


?>