<?php

use App\Plugins\UserManager\Controller\UserManagerController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware"=>["authCheck:view-accounts", "onAllPortal"]], function(){
	Route::get("/manage", [UserManagerController::class, "manageUsersView"]);
});

Route::group(["middleware"=>["authCheck:add-account", "onAllPortal"]], function(){
	Route::get("/save", [UserManagerController::class, "saveUserView"]);
});

Route::group(["middleware"=>["authCheck:update-account", "onAllPortal"]], function(){
	Route::get("/save/{userId}", [UserManagerController::class, "saveUserView"]);
});

Route::group(["middleware"=>["authCheck:view-roles", "onAllPortal"]], function(){
	Route::get("/roles", [UserManagerController::class, "rolesView"]);
});

Route::group(["middleware"=>["authCheck:add-role", "onAllPortal"]], function(){
	Route::get("/roles/save", [UserManagerController::class, "saveRoleView"]);
});

Route::group(["middleware"=>["authCheck:update-role", "onAllPortal"]], function(){
	Route::get("/roles/save/{roleId}", [UserManagerController::class, "saveRoleView"]);
});

Route::group(["middleware"=>["authCheck:manage-abilities", "onAllPortal"]], function(){
	Route::get("/abilities", [UserManagerController::class, "abilitiesView"]);
});

?>