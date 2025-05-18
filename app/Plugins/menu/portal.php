<?php

use App\Plugins\Menu\Controller\MenuController;
use Illuminate\Support\Facades\Route;

require_once __DIR__."/app/controllers/MenuController.php";

Route::group(["middleware"=>["authCheck:view-menu"]], function(){
	Route::get("/", [MenuController::class, "menusView"]);
});

Route::group(["middleware"=>["authCheck:add-menu"]], function(){
	Route::get("/save", [MenuController::class, "saveMenuView"]);
});

Route::group(["middleware"=>["authCheck:update-menu"]], function(){
	Route::get("/save/{menuId}", [MenuController::class, "saveMenuView"]);
});


?>