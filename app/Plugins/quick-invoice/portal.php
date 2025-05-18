<?php

use App\Plugins\QuickInvoice\Controllers\InvoiceBusinessController;
use App\Plugins\QuickInvoice\Controllers\InvoiceClientController;
use App\Plugins\QuickInvoice\Controllers\InvoiceDocumentController;
use App\Plugins\QuickInvoice\Controllers\InvoiceExpenseController;
use App\Plugins\QuickInvoice\Controllers\InvoiceProductController;
use Illuminate\Support\Facades\Route;

// Clients

Route::group(["middleware"=>["authCheck:add-invoice-client", "onAllPortal"]], function(){
	Route::get("/clients/save", [InvoiceClientController::class, "saveClientView"]);
});

Route::group(["middleware"=>["authCheck:update-invoice-client", "onAllPortal"]], function(){
	Route::get("/clients/save/{clientId}", [InvoiceClientController::class, "saveClientView"]);
});

Route::group(["middleware"=>["authCheck:view-user-invoice-client", "onAllPortal"]], function(){
	Route::get("/clients/manage", [InvoiceClientController::class, "manageClientsView"]);
});

// Products

Route::group(["middleware"=>["authCheck:view-user-invoice-product", "onAllPortal"]], function(){
	Route::get("/products/manage/{productType}", [InvoiceProductController::class, "productsView"]);	
});

Route::group(["middleware"=>["authCheck:add-user-invoice-product", "onAllPortal"]], function(){
	Route::get("/products/{productType}/save", [InvoiceProductController::class, "saveProductView"]);	
});

Route::group(["middleware"=>["authCheck:update-user-invoice-product", "onAllPortal"]], function(){
	Route::get("/products/{productType}/save/{productId}", [InvoiceProductController::class, "saveProductView"]);	
});

// Invoices

Route::group(["middleware"=>["authCheck:view-user-invoice", "onAllPortal"]], function(){
	Route::get("/documents/invoice/manage", [InvoiceDocumentController::class, "invoicesView"]);	
});

Route::group(["middleware"=>["authCheck:add-user-invoice", "onAllPortal"]], function(){
	Route::get("/documents/invoice/save", [InvoiceDocumentController::class, "saveInvoiceView"]);	
});

Route::group(["middleware"=>["authCheck:update-user-invoice", "onAllPortal"]], function(){
	Route::get("/documents/invoice/save/{documentId}", [InvoiceDocumentController::class, "saveInvoiceView"]);	
});

// Proposals

Route::group(["middleware"=>["authCheck:view-user-invoice-proposal", "onAllPortal"]], function(){
	Route::get("/documents/proposal/manage", [InvoiceDocumentController::class, "proposalsView"]);	
});

Route::group(["middleware"=>["authCheck:add-user-invoice-proposal", "onAllPortal"]], function(){
	Route::get("/documents/proposal/save", [InvoiceDocumentController::class, "saveProposalView"]);	
});

Route::group(["middleware"=>["authCheck:update-user-invoice-proposal", "onAllPortal"]], function(){
	Route::get("/documents/proposal/save/{documentId}", [InvoiceDocumentController::class, "saveProposalView"]);	
});


// Delivery Notes

Route::group(["middleware"=>["authCheck:add-user-invoice-delivery-note", "onAllPortal"]], function(){
	Route::get("/documents/delivery-note/save", [InvoiceDocumentController::class, "saveDeliveryNoteView"]);	
});

Route::group(["middleware"=>["authCheck:update-user-invoice-delivery-note", "onAllPortal"]], function(){
	Route::get("/documents/delivery-note/save/{documentId}", [InvoiceDocumentController::class, "saveDeliveryNoteView"]);	
});

Route::group(["middleware"=>["authCheck:view-user-invoice-delivery-note", "onAllPortal"]], function(){
	Route::get("/documents/delivery-note/manage", [InvoiceDocumentController::class, "deliveryNotesView"]);	
});

// Expense

Route::group(["middleware"=>["authCheck:add-user-invoice-expense", "onAllPortal"]], function(){
	Route::get("/expense/save", [InvoiceExpenseController::class, "saveExpenseView"]);	
});

Route::group(["middleware"=>["authCheck:update-user-invoice-expense", "onAllPortal"]], function(){
	Route::get("/expense/save/{expenseId}", [InvoiceExpenseController::class, "saveExpenseView"]);	
});

Route::group(["middleware"=>["authCheck:view-user-invoice-expense", "onAllPortal"]], function(){
	Route::get("/expense/manage", [InvoiceExpenseController::class, "expensesView"]);	
});

Route::group(["middleware"=>["authCheck:view-user-invoice-expense-category", "onAllPortal"]], function(){
	Route::get("/expense/categories", [InvoiceExpenseController::class, "expenseCategoriesView"]);	
});

Route::group(["middleware"=>["authCheck:add-user-invoice-expense-category", "onAllPortal"]], function(){
	Route::get("/expense/categories/save", [InvoiceExpenseController::class, "saveCategoryView"]);	
});

Route::group(["middleware"=>["authCheck:update-user-invoice-expense-category", "onAllPortal"]], function(){
	Route::get("/expense/categories/save/{categoryId}", [InvoiceExpenseController::class, "saveCategoryView"]);	
});

// Business

Route::group(["middleware"=>["authCheck:add-user-invoice-business", "onAllPortal"]], function(){
	Route::get("/business/save", [InvoiceBusinessController::class, "saveBusinessView"]);	
});

Route::group(["middleware"=>["authCheck:update-user-invoice-business", "onAllPortal"]], function(){
	Route::get("/business/save/{businessId}", [InvoiceBusinessController::class, "saveBusinessView"]);	
});

Route::group(["middleware"=>["authCheck:view-user-invoice-business", "onAllPortal"]], function(){
	Route::get("/business/manage", [InvoiceBusinessController::class, "businessesView"]);	
});

?>