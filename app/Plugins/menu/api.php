<?php

use App\Plugins\Menu\Controller\MenuController;
use Illuminate\Support\Facades\Route;

require_once __DIR__."/app/controllers/MenuController.php";

Route::group(["middleware"=>["auth:sanctum", "ability:view-menu"]], function(){
	Route::get("/all", [MenuController::class, "menusRequest"]);
	Route::get("/one/{menuId}", [MenuController::class, "menuRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:add-menu"]], function(){
	Route::post("/save", [MenuController::class, "addMenuRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:update-menu"]], function(){
	Route::put("/save/{menuId}", [MenuController::class, "updateMenuRequest"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-menu"]], function(){
	Route::delete("/delete/{menuId}", [MenuController::class, "deleteMenuRequest"]);
});

?>