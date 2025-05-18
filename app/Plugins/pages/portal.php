<?php

use App\Plugins\Pages\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware"=>["authCheck:add-page","onAllPortal"]], function(){
	Route::get("/save", [PageController::class, "savePageView"]);
});

Route::group(["middleware"=>["authCheck:update-page","onAllPortal"]], function(){
	Route::get("/manage", [PageController::class, "managePagesView"]);
	Route::get("/save/{pageId}", [PageController::class, "savePageView"]);
});


?>