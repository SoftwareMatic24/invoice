<?php

use App\Plugins\Elearning\Controllers\TransactionController;
use App\Plugins\PaymentMethods\Controller\PaymentMethodController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware"=>["authCheck:manage-billing-transactions", "onAllPortal"]], function(){
	Route::get("/billing/transactions", [TransactionController::class, "transactionsView"]);
});

Route::group(["middleware"=>["authCheck:user-payment-method", "onAllPortal"]], function(){
	Route::get("/methods/{type}/{paymentMethodSlug}", [PaymentMethodController::class, "paymentMethodEntriesView"]);
	Route::get("/methods/{type}/{paymentMethodSlug}/save", [PaymentMethodController::class, "savePaymentMethodEntryView"]);
	Route::get("/methods/{type}/{paymentMethodSlug}/save/{entryId}", [PaymentMethodController::class, "savePaymentMethodEntryView"]);
});

?>