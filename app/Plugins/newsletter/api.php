<?php

use App\Plugins\Newsletter\Controller\NewsletterController;
use Illuminate\Support\Facades\Route;

require_once __DIR__."/app/controllers/NewsletterController.php";

Route::post("/save", [NewsletterController::class, "addNewsletterRequest"]);

Route::group(["middleware"=>["auth:sanctum", "ability:update-newsletter"]], function(){
	Route::post("/save/{newsletterId}", [NewsletterController::class, "updateNewsletterRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:view-newsletter"]], function(){
	Route::get("/one/{newsletterId}", [NewsletterController::class, "getOneNewsletter"]);
	Route::get("/all", [NewsletterController::class, "getNewsletter"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-newsletter"]], function(){
	Route::delete("/delete/{newsletterId}", [NewsletterController::class, "deleteNewsletter"]);
});

?>