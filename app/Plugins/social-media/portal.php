<?php

require_once __DIR__."/app/controllers/SocialMediaController.php";
use App\Plugins\SocialMedia\Controller\SocialMediaController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware"=>["authCheck:view-social-media-links"]], function(){
	Route::get("/social-links", [SocialMediaController::class, "socialLinksView"]);
});

?>