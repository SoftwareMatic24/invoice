<?php

require_once __DIR__ ."/app/controllers/PayPalController.php";

use App\Plugings\PayPal\Controller\PayPalController;
use Illuminate\Support\Facades\Route;

Route::get("/test", [PayPalController::class, "test"]);

?>