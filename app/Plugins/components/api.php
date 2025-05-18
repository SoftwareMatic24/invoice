<?php

use App\Plugins\Component\Controller\ComponentController;
use Illuminate\Support\Facades\Route;

Route::post("/find/slugs", [ComponentController::class, "findBySlugsRequest"]);


Route::group(["middleware"=>["auth:sanctum", "ability:view-components", "onAllPortal"]], function(){
	Route::get("/all", [ComponentController::class, "components"]);
	Route::get("/one/{componentSlug}", [ComponentController::class, "component"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:update-components", "onAllPortal"]], function(){
	Route::put("/save/{componentSlug}", [ComponentController::class, "saveComponentRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-components", "onAllPortal"]], function(){
	Route::delete("/delete/{componentId}", [ComponentController::class, "deleteComponentRequest"]);
});

?>