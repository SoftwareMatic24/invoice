<?php

namespace App\Http\Controllers;

use App\Classes\File;
use App\Classes\Req;
use App\Models\StorageRoleAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class PrivateStorageController extends Controller
{

	// Read

	function accessFile($relativePath, $download){
		$storage = "private";
		
		$user = Req::loggedInUser($_COOKIE);
		if($user === NULL) return response("Unauthorized", 401);
			
		$relativePath = str_replace("\\","/", $relativePath);
		$relativePath = str_replace("//","/", $relativePath);

		$roleTitle = $user["role_title"];
		list($baseFolder) = explode("/", $relativePath);
		
		if(!$this->hasAccess($storage, $baseFolder, $roleTitle)) return response("Unauthorized", 401);

		$filePath = storage_path("app/$storage/$relativePath");
		if(!is_file($filePath)) return response("File not found.", 404);
		return File::readFile($filePath, $download);
	}

	// Request

	function storageRequest(Request $request){
		$queryParams = $request->query();

		$file = $queryParams["file"] ?? NULL;
		$download = ($queryParams["download"] ?? false) ? ($queryParams["download"] == "true" ? true : false) : false;
		
		if($file !== NULL) return $this->accessFile($file, $download);
		return response("Unauthorized", 401);
	}

	// Checks

	function hasAccess($storage, $baseFolder, $roleTitle){
		$records = Cache::get("storageRoleAccess");
		$match = false;
		foreach($records as $record){
			if($record["storage"] === $storage && $record["base_folder"] === $baseFolder && $record["role_title"] === $roleTitle){
				$match = true;
				break;
			}
		}
		return $match;
	}
}
