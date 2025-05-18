<?php

use App\Plugins\Pages\Controllers\PageController;
use Illuminate\Support\Facades\Route;


Route::group(["middleware"=>["auth:sanctum", "ability:add-page", "onAllPortal"]], function(){
	Route::get("/one/{pageId}", [PageController::class, "pageRequest"]);
	Route::get("/all", [PageController::class, "pagesRequest"]);
	Route::get("/all/status/{status}", [PageController::class, "getPagesByStatus"]);
	Route::post("/save", [PageController::class, "savePageRequest"]);
	Route::delete("/delete/{pageId}", [PageController::class, "deletePageRequest"]);
});


?>