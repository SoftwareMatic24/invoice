<?php

require_once __DIR__."/app/controllers/SocialMediaController.php";

use App\Plugins\SocialMedia\Controller\SocialMediaController;
use Illuminate\Support\Facades\Route;


Route::group(["middleware"=>["auth:sanctum", "ability:view-social-media-links"]], function(){
	Route::get("/social-links/all", [SocialMediaController::class, "allSocialMediaLinks"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:add-social-media-links"]], function(){
	Route::post("/social-links/save", [SocialMediaController::class, "saveSocialMediaLinksRequest"]);
});


?>