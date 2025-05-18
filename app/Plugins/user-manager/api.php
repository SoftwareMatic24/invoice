<?php

use App\Http\Controllers\AbilityController;
use App\Http\Controllers\RoleController;
use App\Plugins\UserManager\Controller\UserManagerController;
use Illuminate\Support\Facades\Route;

Route::group(["middleware"=>["auth:sanctum", "ability:view-roles", "onAllPortal"]], function(){
	Route::get("/roles/all", [RoleController::class, "getRoles"]);
	Route::get("/roles/one/{roleId}", [RoleController::class, "getRole"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:add-role", "onAllPortal"]], function(){
	Route::post("/roles/add", [RoleController::class, "saveRole"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:update-role", "onAllPortal"]], function(){
	Route::put("/roles/update/{roleId}", [RoleController::class, "saveRole"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-role", "onAllPortal"]], function(){
	Route::delete("/roles/delete/{roleId}", [RoleController::class, "deleteRole"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:manage-abilities", "onAllPortal"]], function(){
	Route::delete("/abilities/remove", [RoleController::class, "removeAbilityRequest"]);
	Route::post("/abilities/assign", [AbilityController::class, "saveAbility"]);
});

Route::post("/accounts/auth", [UserManagerController::class, "authRequest"]);
Route::get("/accounts/logout", [UserManagerController::class, "logoutRequest"]);
Route::post("/accounts/register", [UserManagerController::class, "registerRequest"]);
Route::post("/accounts/forgot-password", [UserManagerController::class, "forgotPasswordRequest"]);
Route::post("/accounts/reset-password", [UserManagerController::class, "resetPasswordRequest"]);

Route::group(["middleware"=>["auth:sanctum", "ability:view-accounts", "onAllPortal"]], function(){
	Route::get("/users/one/{userId}", [UserManagerController::class, "user"]);
	Route::get("/users/all", [UserManagerController::class, "users"]);
});

Route::group(["middleware"=>["auth:sanctum", "ability:delete-account", "onAllPortal"]], function(){
	Route::delete("/users/delete/{userId}", [UserManagerController::class, "deleteUserRequest"]);
});

Route::group(["middleware" => ["auth:sanctum", "ability:add-account", "onAllPortal"]], function () {
	Route::post("/users/save", [UserManagerController::class, "saveUserRequest"]);
});

Route::group(["middleware" => ["auth:sanctum", "ability:update-account",  "onAllPortal"]], function () {
	Route::put("/users/save/{userId}", [UserManagerController::class, "saveUserRequest"]);	
});

Route::group(["middleware" => ["auth:sanctum", "ability:admin",  "onAllPortal"]], function () {
	Route::put("/user/users/update/role-and-status", [UserManagerController::class, "userRoleAndStatusUpdateRequest"]);
});

Route::group(["middleware" => ["auth:sanctum", "onAllPortal"]], function () {
	Route::put("/user/users/update/profile-information", [UserManagerController::class, "userProfileInformationUpdateRequest"]);
	Route::put("/user/users/update/password", [UserManagerController::class, "userPasswordUpdateRequest"]);
	Route::put("/user/users/update/about", [UserManagerController::class, "userAboutUpdateRequest"]);
	Route::put("/user/users/update/address", [UserManagerController::class, "userAddressUpdateRequest"]);
	Route::put("/user/users/update/additional", [UserManagerController::class, "userAdditionalUpdateRequest"]);
	Route::put("/user/users/update/profile-picture", [UserManagerController::class, "userProfilePictureUpdateRequest"]);

	Route::get("/user/users/settings/all", [UserManagerController::class, "userSettingsRequest"]);
	Route::post("/user/users/settings/save", [UserManagerController::class, "userSaveSettingsRequest"]);
});




?>