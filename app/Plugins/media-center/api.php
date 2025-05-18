<?php

use App\Plugins\MediaCenter\Controllers\MediaCenterController;
use Illuminate\Support\Facades\Route;


Route::group(["middleware"=>["auth:sanctum", "ability:media-center", "onAllPortal"]], function(){
	Route::get("/folders/all", [MediaCenterController::class, "foldersAndMediaRequest"]);
	Route::post("/folders/save", [MediaCenterController::class, "saveFolderReuqest"]);
	Route::delete("/folders/delete/{folderId}", [MediaCenterController::class, "deleteFolderReuqest"]);
	Route::post("/upload", [MediaCenterController::class, "uploadFileRequest"]);
	Route::post("/save-media", [MediaCenterController::class, "saveMediaRequest"]);
	Route::post("/move", [MediaCenterController::class, "moveMediaRequest"]);
	Route::delete("/delete", [MediaCenterController::class, "deleteMediaRequest"]);
});

?>