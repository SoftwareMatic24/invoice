<?php

use App\Classes\Util;
use App\Plugins\UserManager\Controller\UserManagerController;
use Illuminate\Support\Facades\Route;

Route::get("account/confirmation/{uid}", [UserManagerController::class, "accountConfirmationRequest"]);
Route::get(Util::prefixedRelativeURL("/logout"), [UserManagerController::class, "logout"]);

?>