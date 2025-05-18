<?php

require_once __DIR__ ."/app/controllers/EmailTemplateController.php";

use App\Plugins\EmailTemplate\Controller\EmailTemplateController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware"=>["auth:sanctum", "ability:view-email-template", "onAllPortal"]], function(){
	Route::get("/all", [EmailTemplateController::class, "emailTemplates"]);
	Route::get("/one/{emailTemplateId}", [EmailTemplateController::class, "emailTemplate"]);
	Route::get("/signatures/all", [EmailTemplateController::class, "emailSignatures"]);
	Route::get("/signatures/one/{signatureId}", [EmailTemplateController::class, "emailSignature"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:update-email-template", "onAllPortal"]], function(){
	Route::put("/save/{emailTemplateId}", [EmailTemplateController::class, "saveEmailTemplateRequest"]);
	Route::post("/signatures/save", [EmailTemplateController::class, "saveSignatureRequest"]);
	Route::put("/signatures/save/{signatureId}", [EmailTemplateController::class, "saveSignatureRequest"]);
	Route::delete("/signatures/delete/{signatureId}", [EmailTemplateController::class, "deleteSignature"]);
});

?>