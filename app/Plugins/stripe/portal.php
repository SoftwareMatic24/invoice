<?php

use App\Plugins\Stripe\Controller\StripeController;
use Illuminate\Support\Facades\Route;

require_once __DIR__ ."/app/controllers/StripeController.php";


Route::get("/test", [StripeController::class, "test"]);

?>