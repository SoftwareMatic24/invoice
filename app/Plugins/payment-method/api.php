<?php

use App\Plugins\PaymentMethods\Controller\PaymentMethodController;
use App\Plugins\PaymentMethods\Model\PaymentMethod;
use Illuminate\Support\Facades\Route;

Route::get("/all", [PaymentMethod::class, "paymentMethods"]);
Route::get("/system/safe/one/{identifier}/{paymentMethodSlug}", [PaymentMethodController::class, "safeSystemPaymentMethod"]);

Route::group(["middleware"=>["auth:sanctum", "ability:system-payment-method", "onAllPortal"]], function(){
	Route::post("/system/save", [PaymentMethodController::class, "saveSystemPaymentMethodRequest"]);
	Route::put("/system/save/{entryId}", [PaymentMethodController::class, "saveSystemPaymentMethodRequest"]);
	Route::get("/system/all/{paymentMethodSlug}", [PaymentMethodController::class, "systemPaymentMethodsRequest"]);
	Route::delete("/system/delete/{entryId}", [PaymentMethodController::class, "deleteSystemPaymentMethodEntryRequest"]);
});


Route::group(["middleware"=>["auth:sanctum", "ability:user-payment-method", "onAllPortal"]], function(){
	Route::post("/user/save", [PaymentMethodController::class, "saveUserPaymentMethodRequest"]);
	Route::put("/user/save/{entryId}", [PaymentMethodController::class, "saveUserPaymentMethodRequest"]);
	Route::get("/user/all/{paymentMethodSlug}", [PaymentMethodController::class, "userPaymentMethodsRequest"]);
	Route::delete("/user/delete/{entryId}", [PaymentMethodController::class, "deleteUserPaymentMethodEntryRequest"]);
});

?>