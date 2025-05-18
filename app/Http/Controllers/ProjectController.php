<?php

namespace App\Http\Controllers;

use App\Classes\Util;
use App\Rules\GeneralRule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class ProjectController extends Controller
{

	static function init($routeType){

		if($routeType === "web"){
			PluginController::loadRoutes("web");
			Route::fallback([ThemeController::class, "fallbackPageView"]);
		}
		else if($routeType === "api"){
			PluginController::loadRoutes("api");
		}

		(new GeneralRule());
		self::initLivewire();
	}

	static function initLivewire(){
		
		$subfolder = Util::subfolder();
		$fileName = "livewire.min.js";
		if(config(("app.debug")) == "true") $fileName = "livewire.js";
		
		$fileURL = url("/livewire/$fileName");
		$updateURL = "/livewire/update";

		if(!empty($subfolder)) $updateURL = "$subfolder/livewire/update";

		Livewire::setScriptRoute(function($handle) use($fileURL) {
			return Route::post($fileURL, $handle);
		});

		Livewire::setUpdateRoute(function($handle) use($updateURL) {
			return Route::post($updateURL, $handle);
		});

	}


	// Reset

	function resetProject(){
		Artisan::call("migrate:fresh --seed");
		Artisan::call("setup:Plugins");
		return ["status"=>true];
	}

	// Files

	// TODO: depreciate
	static function loadFile($relativePath, $options = []){
		
		$rawContent = $options["rawContent"] ?? false;
		
		$file = base_path($relativePath);
		if (!file_exists($file)) return;

		$fileExtension = pathinfo($relativePath, PATHINFO_EXTENSION);
		$content = file_get_contents($file);
		
		if ($fileExtension === "js" && $rawContent === false) $content = "<script>$content</script>";
		else if ($fileExtension === "css" && $rawContent === false) $content = "<style>$content</style>";
		else if ($rawContent === true) return $content;
		
		return $content;
	}

}
