<?php

use App\Plugins\Newsletter\Controller\NewsletterController;
use Illuminate\Support\Facades\Route;

require_once __DIR__."/app/controllers/NewsletterController.php";

Route::group(["middleware"=>["authCheck:view-newsletter"]], function(){
	Route::get("/", [NewsletterController::class, "newsletterView"]);
});

Route::group(["middleware"=>["authCheck:add-newsletter"]], function(){
	Route::get("/save", [NewsletterController::class, "addNewsletterView"]);
});

Route::group(["middleware"=>["authCheck:update-newsletter"]], function(){
	Route::get("/save/{newsletterId}", [NewsletterController::class, "addNewsletterView"]);
});

?>