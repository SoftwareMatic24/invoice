<?php

require_once __DIR__."/app/controllers/EmailTemplateController.php";

use App\Plugins\EmailTemplate\Controller\EmailTemplateController;
use Illuminate\Support\Facades\Route;


Route::group(["middleware"=>["authCheck:view-email-template", "onAllPortal"]], function(){
	Route::get("/", [EmailTemplateController::class, "emailTemplatesView"]);
	Route::get("/signatures", [EmailTemplateController::class, "emailSignatureTemplates"]);
});

Route::group(["middleware"=>["authCheck:update-email-template", "onAllPortal"]], function(){
	Route::get("/save/{emailTemplateId}", [EmailTemplateController::class, "saveEmailTemplateView"]);
	Route::get("/signatures/save/", [EmailTemplateController::class, "saveSignatureTemplateView"]);
	Route::get("/signatures/save/{signatureId}", [EmailTemplateController::class, "saveSignatureTemplateView"]);
});


?>