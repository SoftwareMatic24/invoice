<?php

use App\Plugins\ContactMessage\App\Controller\ContactMessageController;
use Illuminate\Support\Facades\Route;

Route::post("/send", [ContactMessageController::class, "contactRequest"]);

Route::group(["middleware"=>["auth:sanctum", "ability:view-contact-message", "onAllPortal"]],function(){
	Route::get("/all", [ContactMessageController::class, "contactMessagesRequest"]);
	Route::put("/mark-as-read/{messageId}", [ContactMessageController::class, "markAsRead"]);
	Route::post("/reply", [ContactMessageController::class, "replyContactRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-contact-message", "onAllPortal"]],function(){
	Route::delete("/delete/{contactMessageId}", [ContactMessageController::class, "deleteContactMessagesRequest"]);
});