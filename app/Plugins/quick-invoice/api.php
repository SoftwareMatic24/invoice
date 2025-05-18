<?php

use App\Plugins\QuickInvoice\Controllers\InvoiceBusinessController;
use App\Plugins\QuickInvoice\Controllers\InvoiceClientController;
use App\Plugins\QuickInvoice\Controllers\InvoiceDocumentController;
use App\Plugins\QuickInvoice\Controllers\InvoiceExpenseController;
use App\Plugins\QuickInvoice\Controllers\InvoiceProductController;
use Illuminate\Support\Facades\Route;


// Invoices

Route::group(["middleware"=>["auth:sanctum", "ability:view-user-invoice", "onAllPortal"]], function(){
	Route::get("user/documents/invoice/all", [InvoiceDocumentController::class, "userInvoicesRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:add-user-invoice", "onAllPortal"]], function(){
	Route::post("user/documents/invoice/save", [InvoiceDocumentController::class, "saveUserInvoiceRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:update-user-invoice", "onAllPortal"]], function(){
	Route::put("user/documents/invoice/save/{documentId}", [InvoiceDocumentController::class, "saveUserInvoiceRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-user-invoice", "onAllPortal"]], function(){
	Route::delete("user/documents/invoice/delete/{documentId}", [InvoiceDocumentController::class, "deleteUserInvoiceRequest"]);
});

// Proposals

Route::group(["middleware"=>["auth:sanctum", "ability:view-user-invoice-proposal", "onAllPortal"]], function(){
	Route::get("user/documents/proposal/all", [InvoiceDocumentController::class, "userProposalsRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:add-user-invoice-proposal", "onAllPortal"]], function(){
	Route::post("user/documents/proposal/save", [InvoiceDocumentController::class, "saveUserProposalRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:update-user-invoice-proposal", "onAllPortal"]], function(){
	Route::put("user/documents/proposal/save/{documentId}", [InvoiceDocumentController::class, "saveUserProposalRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-user-invoice-proposal", "onAllPortal"]], function(){
	Route::delete("user/documents/proposal/delete/{documentId}", [InvoiceDocumentController::class, "deleteUserProposalRequest"]);
});

// Delivery Note

Route::group(["middleware"=>["auth:sanctum", "ability:view-user-invoice-delivery-note", "onAllPortal"]], function(){
	Route::get("user/documents/delivery-note/all", [InvoiceDocumentController::class, "userDeliveryNotesRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:add-user-invoice-delivery-note", "onAllPortal"]], function(){
	Route::post("user/documents/delivery-note/save", [InvoiceDocumentController::class, "saveUserDeliveryNoteRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:update-user-invoice-delivery-note", "onAllPortal"]], function(){
	Route::put("user/documents/delivery-note/save/{documentId}", [InvoiceDocumentController::class, "saveUserDeliveryNoteRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-user-invoice-delivery-note", "onAllPortal"]], function(){
	Route::delete("user/documents/delivery-note/delete/{documentId}", [InvoiceDocumentController::class, "deleteUserDeliveryNoteRequest"]);
});

// Copy Document

Route::group(["middleware"=>["auth:sanctum", "ability:copy-user-invoice-document", "onAllPortal"]], function(){
	Route::post("user/documents/copy", [InvoiceDocumentController::class, "copyUserDocumentRequest"]);
});

// Email Document

Route::group(["middleware"=>["auth:sanctum", "ability:email-user-invoice-document", "onAllPortal"]], function(){
	Route::post("user/documents/send-via-email", [InvoiceDocumentController::class, "emailUserDocumentViaEmailRequest"]);
});

// Document Payments

Route::group(["middleware"=>["auth:sanctum", "ability:add-user-invoice-document-payment", "onAllPortal"]], function(){
	Route::get("user/documents/{documentId}/payments/all", [InvoiceDocumentController::class, "userDocumentPaymentsRequest"]);
	Route::post("user/documents/payments/save", [InvoiceDocumentController::class, "saveUserDocumentPaymentRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-user-invoice-document-payment", "onAllPortal"]], function(){
	Route::delete("user/documents/payments/delete/{paymentId}", [InvoiceDocumentController::class, "deleteUserDocumentPaymentRequest"]);
});

// Document Fields

Route::group(["middleware"=>["auth:sanctum", "ability:manage-custom-field", "onAllPortal"]], function(){
	Route::get("user/documents/fields/all", [InvoiceDocumentController::class, "userDocumentFieldsRequest"]);
	Route::post("user/documents/fields/save", [InvoiceDocumentController::class, "saveUserDocumentFieldRequest"]);
	Route::put("user/documents/fields/save/{fieldId}", [InvoiceDocumentController::class, "saveUserDocumentFieldRequest"]);
	Route::delete("user/documents/fields/delete/{fieldId}", [InvoiceDocumentController::class, "deleteUserDocumentFieldRequest"]);
	Route::post("/update-color", [InvoiceDocumentController::class, "updatecolor"]);
});


// Clients

Route::group(["middleware"=>["auth:sanctum", "ability:view-user-invoice-client", "onAllPortal"]], function(){
	Route::get("user/clients/all", [InvoiceClientController::class, "userClientsRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:add-invoice-client", "onAllPortal"]], function(){
	Route::post("clients/save", [InvoiceClientController::class, "addClientRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:update-invoice-client", "onAllPortal"]], function(){
	Route::put("clients/save/{clientId}", [InvoiceClientController::class, "updateClientRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-user-invoice-client", "onAllPortal"]], function(){
	Route::delete("/user/clients/delete/{clientId}", [InvoiceClientController::class, "deleteUserClientRequest"]);
});


// Products

Route::group(["middleware"=>["auth:sanctum", "ability:view-user-invoice-product", "onAllPortal"]], function(){
	Route::get("/user/products/all", [InvoiceProductController::class, "userProductsRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:add-user-invoice-product", "onAllPortal"]], function(){
	Route::post("/user/products/save", [InvoiceProductController::class, "addProductRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:update-user-invoice-product", "onAllPortal"]], function(){
	Route::put("/user/products/save/{productId}", [InvoiceProductController::class, "updateProductRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-user-invoice-product", "onAllPortal"]], function(){
	Route::delete("/user/products/delete/{productId}", [InvoiceProductController::class, "deleteUserProductRequest"]);
});

// Expense

Route::group(["middleware"=>["auth:sanctum", "ability:view-user-invoice-expense-category", "onAllPortal"]], function(){
	Route::get("/user/expense/categories/all", [InvoiceExpenseController::class, "userCategoriesRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:add-user-invoice-expense-category", "onAllPortal"]], function(){
	Route::post("/user/expense/categories/save", [InvoiceExpenseController::class, "saveUserCategoryRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:update-user-invoice-expense-category", "onAllPortal"]], function(){
	Route::put("/user/expense/categories/save/{categoryId}", [InvoiceExpenseController::class, "saveUserCategoryRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-user-invoice-expense-category", "onAllPortal"]], function(){
	Route::delete("/user/expense/categories/delete/{categoryId}", [InvoiceExpenseController::class, "deleteUserCategoryRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:view-user-invoice-expense", "onAllPortal"]], function(){
	Route::get("/user/expense/all", [InvoiceExpenseController::class, "userExpensesRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:add-user-invoice-expense", "onAllPortal"]], function(){
	Route::post("/user/expense/save", [InvoiceExpenseController::class, "saveUserExpenseRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:update-user-invoice-expense", "onAllPortal"]], function(){
	Route::put("/user/expense/save/{expenseId}", [InvoiceExpenseController::class, "saveUserExpenseRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-user-invoice-expense", "onAllPortal"]], function(){
	Route::delete("/user/expense/delete/{expenseId}", [InvoiceExpenseController::class, "deleteUserExpenseRequest"]);
});

// Business

Route::group(["middleware"=>["auth:sanctum", "ability:view-user-invoice-business", "onAllPortal"]], function(){
	Route::get("/user/business/all", [InvoiceBusinessController::class, "userBusinessesRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:add-user-invoice-business", "onAllPortal"]], function(){
	Route::post("/user/business/save", [InvoiceBusinessController::class, "saveUserBusinessRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:update-user-invoice-business", "onAllPortal"]], function(){
	Route::put("/user/business/save/{businessId}", [InvoiceBusinessController::class, "saveUserBusinessRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-user-invoice-business", "onAllPortal"]], function(){
	Route::delete("/user/business/delete/{businessId}", [InvoiceBusinessController::class, "deleteUserBusinessRequest"]);
});


// Templates

Route::group(["middleware"=>["auth:sanctum", "ability:manage-user-document-templates", "onAllPortal"]], function(){
	Route::post("/user/documents/templates/activate", [InvoiceDocumentController::class, "activateUserDocumentTemplateRequest"]);
	Route::post("/user/documents/templates/save", [InvoiceDocumentController::class, "saveUserDocumentTemplateRequest"]);
});



?>