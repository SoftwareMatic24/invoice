<?php

use App\Plugins\Component\Controller\ComponentController;
use Illuminate\Support\Facades\Route;


Route::group(["middleware"=>["authCheck:view-components", "onAllPortal"]], function(){
	Route::get("/", [ComponentController::class, "componentsView"]);
});

Route::group(["middleware"=>["authCheck:update-components", "onAllPortal"]], function(){
	Route::get("/save/{componentSlug}", [ComponentController::class, "saveComponentView"]);
});

?>