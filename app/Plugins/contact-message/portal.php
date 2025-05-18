<?php


use App\Plugins\ContactMessage\App\Controller\ContactMessageController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware"=>["authCheck:view-contact-message", "onAllPortal"]], function(){
	Route::get("/", [ContactMessageController::class, "manageContactMessagesView"]);
});
