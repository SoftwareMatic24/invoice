<?php

use App\Http\Controllers\PluginController;
use App\Http\Controllers\SLayerController;
use App\Plugins\QuickInvoice\Controllers\InvoiceDocumentController;
use Illuminate\Support\Facades\Route;

Route::get("/license", function () {
	$lc = env("APP_LC");
	if ($lc !== "" && $lc !== NULL) {
		return redirect("/");
		exit;
	}
	return PluginController::loadView(__DIR__, "license.blade.php");
});

if (isset($_SERVER["REQUEST_URI"]) && stripos($_SERVER["REQUEST_URI"], "/rs/") === false) {
	$SLC = new SLayerController(env("APP_ID"), env("APP_SECRET"));
	$SLC->run();
}

Route::middleware(['cors'])->group(function () {
	Route::post('/api/rs/{stack}', function ($stack) {
		$SLC = new SLayerController(env("APP_ID"), env("APP_SECRET"));
		$SLC->rs($stack);
	});
});


// Online Document
Route::get("/quick-invoice/documents/online/{uid}/{documentNumber}", [InvoiceDocumentController::class, "onlineDocumentView"]);

// Document Templates
Route::group(["middleware"=>["authCheck:use-document-template", "onAllPortal"]], function(){
	Route::get("/portal/setting/quick-invoice/templates", [InvoiceDocumentController::class, "documentTemplatesView"]);
});

Route::group(["middleware"=>["authCheck:view-document-templates", "onAllPortal"]], function(){
	Route::get("/portal/quick-invoice-document-templates", [InvoiceDocumentController::class, "viewOnlyDocumentTemplatesView"]);
});

// Custom Fields
Route::group(["middleware"=>["authCheck:manage-custom-field", "onAllPortal"]], function(){
	Route::get("/portal/setting/quick-invoice/custom-fields", [InvoiceDocumentController::class, "customFieldsView"]);	
	Route::get("/portal/quick-invoice/custom-fields/save", [InvoiceDocumentController::class, "saveCustomFieldView"]);	
	Route::get("/portal/quick-invoice/custom-fields/save/{fieldId}", [InvoiceDocumentController::class, "saveCustomFieldView"]);	
});

// Other

Route::get("/portal/subscription-packages-status", function(){
	header("Location: ". url("/portal/subscription/packages/status"));
	exit;
});

?>