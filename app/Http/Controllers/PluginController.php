<?php

namespace App\Http\Controllers;

use App\Classes\File as MyFiles;
use App\Classes\FS;
use App\Models\Plugin;
use App\Models\Theme;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

class PluginController extends Controller
{

	static function getAutoloadableFiles($dir){
		$files = [];

		$directories = glob($dir . '/*', GLOB_ONLYDIR);
		$files = array_merge($files, glob($dir . '/*.php'));
	
		foreach ($directories as $subDir) {
			$files = array_merge($files, self::getAutoloadableFiles($subDir));
		}
	
		return $files;
	}

	static function autoloadPluginFiles($pluginSlug){

		$pluginAppPath = app_path("Plugins/$pluginSlug/app");
		$pluginFactoryPath = app_path("Plugins/$pluginSlug/database/factories");

		$files = self::getAutoloadableFiles($pluginAppPath);

		foreach($files as $file){
			require_once $file;
		}

		if(is_dir($pluginFactoryPath)){
			$factoryFiles = self::getAutoloadableFiles($pluginFactoryPath);
			foreach($factoryFiles as $file){
				require_once $file;
			}
			
		}

	}

	static function requireOnce($absolutePath){
		$classInstance = require_once $absolutePath;

		if(is_object($classInstance)) return $classInstance;
		else {

			$formattedAbsolutePath = str_replace("/", "\\", $absolutePath);
			$match = NULL;
			$declaredClasses = get_declared_classes();

			for($i = sizeof($declaredClasses) - 1; $i >= 0; $i--){
				$declaredClass = $declaredClasses[$i];
				$declaredClass = str_replace("/", "\\", $declaredClass);

				if(strpos($declaredClass, $formattedAbsolutePath) !== false){
					$match = $declaredClass;
					break;
				}
			}
			
			if($match === NULL) $classInstance = NULL;
			else $classInstance = (new $match);
		}

		return $classInstance;
	}

	static function loadRoutes($routesType)
	{
		
		$activePlugins = Cache::get("activePlugins");
		if(empty($activePlugins)) return;
		
	
		$slugs = array_column($activePlugins, "slug");

	
		$pluginsAbsolutePath = app_path("Plugins");

		foreach ($slugs as $slug) {

			$webRouteFile = $pluginsAbsolutePath . "/" . $slug . "/web.php";
			$portalRouteFile = $pluginsAbsolutePath . "/" . $slug . "/portal.php";
			$apiRouteFile = $pluginsAbsolutePath . "/" . $slug . "/api.php";
			$globalAPIRouteFile = $pluginsAbsolutePath . "/" . $slug . "/global-api.php";
			$directiveFile = $pluginsAbsolutePath . "/" . $slug . "/directive.php";
			$livewireFile = $pluginsAbsolutePath . "/" . $slug . "/livewire.php";
			$mainFile = $pluginsAbsolutePath . "/" . $slug . "/main.php";

			self::autoloadPluginFiles($slug);

			if (file_exists($portalRouteFile) && $routesType === "web") {
				Route::group(["prefix" => config("app.portal_prefix") . "/" . $slug], function () use ($portalRouteFile) {
					require_once $portalRouteFile;
				});
			}

			if (file_exists($webRouteFile) && $routesType === "web") {
				Route::group([], function () use ($webRouteFile) {
					require_once $webRouteFile;
				});
			}

			if (file_exists($apiRouteFile) && $routesType === "api") {
				Route::group(["prefix" => $slug], function () use ($apiRouteFile) {
					require_once $apiRouteFile;
				});
			}

			if (file_exists($globalAPIRouteFile) && $routesType === "api") {
				Route::group([], function () use ($globalAPIRouteFile) {
					require_once $globalAPIRouteFile;
				});
			}

			if(file_exists($mainFile)) require_once $mainFile;
			if(file_exists($directiveFile)) require_once $directiveFile;
			if(file_exists($livewireFile)) require_once $livewireFile;

			Route::get("plugin/{pluginSlug}/assets/{file}", [PluginController::class, "pluginAsset"]);
			Route::get("plugin/{pluginSlug}/css/{file}", [PluginController::class, "pluginCSS"]);
			Route::get("plugin/{pluginSlug}/js/{file}", [PluginController::class, "pluginJS"]);
			Route::get("plugin/{pluginSlug}/fonts/{fontFamily}/{fileName}", [PluginController::class, "pluginFonts"]);
			Route::get("plugin/{pluginSlug}/icons/{fileName}", [PluginController::class, "pluginIcon"]);
		}
	}

	static function loadAsset($pluginSlug, $relativePath)
	{
		$file = app_path("Plugins/$pluginSlug/$relativePath");
		if (!file_exists($file)) return;
		MyFiles::readFile($file);
	}

	// TODO: depreciate
	static function loadIcon($pluginSlug, $iconName, $options = [])
	{
		$classes = $options["classes"] ?? [];
		$style = $options["style"] ?? "";

		$iconFile = app_path("Plugins/$pluginSlug/icons/$iconName.svg");

		$svg = '
			<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" viewBox="0 0 408 408">
				<g>
					<g xmlns="http://www.w3.org/2000/svg">
						<g>
							<path d="M332,121.921H184.8l-29.28-34.8c-0.985-1.184-2.461-1.848-4-1.8H32.76C14.628,85.453,0,100.189,0,118.321v214.04    c0.022,18.194,14.766,32.938,32.96,32.96H332c18.194-0.022,32.938-14.766,32.96-32.96v-177.48    C364.938,136.687,350.194,121.943,332,121.921z">
							</path>
						</g>
					</g>
					<g xmlns="http://www.w3.org/2000/svg">
						<g>
							<path d="M375.24,79.281H228l-29.28-34.8c-0.985-1.184-2.461-1.848-4-1.8H76c-16.452,0.027-30.364,12.181-32.6,28.48h108.28    c5.678-0.014,11.069,2.492,14.72,6.84l25,29.72H332c26.005,0.044,47.076,21.115,47.12,47.12v167.52    c16.488-2.057,28.867-16.064,28.88-32.68v-177.48C407.957,94.1,393.34,79.413,375.24,79.281z">
							</path>
						</g>
					</g>
					<g xmlns="http://www.w3.org/2000/svg">
					</g>
				</g>
			</svg>
		';

		if (file_exists($iconFile)) $svg = file_get_contents($iconFile);

		$svg = simplexml_load_string($svg);
		$svg["class"] = implode(" ", $classes);
		$svg->addAttribute("style", $style);
		$svg = $svg->asXML();

		return $svg;
	}

	static function loadLayout($plugins, $layoutFileName)
	{
		
		$filesLoaded = [];
		foreach ($plugins as $plugin) {

			$pluginSlug = $plugin["slug"];
			$slug = $pluginSlug;

			if ($plugin["type"] === "child") {
				$options = $plugin["options"];
				$options = json_decode($options, true);
				$slug = $options["childOf"];
			}

			$file = app_path("Plugins/$slug/layouts/" . $layoutFileName . ".blade.php");
			if (file_exists($file) && !in_array($file, $filesLoaded)) {
				$filesLoaded[] = $file;
				echo view::file($file, ["plugin" => $plugin, "parentPluginSlug"=>$slug])->render();
			}
		}
	}

	static function loadWidget($pluginSlug, $widgetFileName, $options = [])
	{
		$file = app_path("Plugins/$pluginSlug/widgets/" . $widgetFileName . ".blade.php");
		if (file_exists($file)) echo view::file($file, $options)->render();
	}

	static function loadFile($pluginSlug, $relativePath, $options = [])
	{
		$path = "app/Plugins/$pluginSlug/$relativePath";
		return ProjectController::loadFile($path, $options);
	}

	static function loadView($path, $file, $params = [])
	{	

		$config = PluginController::getPluginConfig($path);
		$pluginSlug = $config["slug"];
		$viewFile = app_path("Plugins/$pluginSlug/views/$file");
		return View::file($viewFile, $params);
	}

	static function loadPluginsGenericData($pluginSlugs, $userRole = NULL)
	{
		$data = [];

		foreach ($pluginSlugs as $pluginSlug) {
			$file = app_path("Plugins/$pluginSlug/app/models/Generic.php");
			if (file_exists($file)) {
				require_once($file);
			}
		}

		foreach (get_declared_classes() as $class) {

			if (strpos($class, "App\Plugins\\") !== false && (strpos($class, "Models\Generic") || strpos($class, "Model\Generic"))) {
				$genericData = (new $class)->index($userRole);
				$data = array_merge($data, $genericData);
			}
		}

		return $data;
	}

	static function pluginAsset($pluginSlug, $file)
	{
		return self::loadAsset($pluginSlug, "assets/" . $file);
	}

	static function pluginCSS($pluginSlug, $file){
		$css = self::loadFile($pluginSlug, "css/$file", [
			"rawContent" => true
		]);
		
		
		$response = new Response($css);
		$response->header('Content-Type', 'text/css');
		return $response;
	}

	static function pluginJS($pluginSlug, $file){
		$css = self::loadFile($pluginSlug, "js/$file", [
			"rawContent" => true
		]);
		
		$response = new Response($css);
		$response->header('Content-Type', 'text/js');
		return $response;
	}

	static function pluginFonts($pluginSlug, $fontFamily, $fileName){
		$options = [
			"rawContent" => true
		];
		$font = PluginController::loadFile($pluginSlug, "fonts/$fontFamily/$fileName", $options);

		$response = new Response($font);
		$response->header('Content-Type', 'font/font-ttf');
		return $response;
	}

	static function pluginIcon($pluginSlug, $fileName){
		$options = [
			"rawContent" => true
		];
		$font = PluginController::loadFile($pluginSlug, "icons/$fileName", $options);

		$response = new Response($font);
		$response->header('Content-Type', 'image/svg+xml');
		return $response;
	}

	static function getActivePlugins()
	{
		return Plugin::getActivePlugins();
	}

	static function getActivePluginSlugs(){
		return Plugin::getActivePluginSlugs();
	}

	static function isPluginActive($slug)
	{
		$isActive = false;
		$plugin = Plugin::getPluginBySlug($slug);
		if ($plugin === NULL) $isActive = false;
		else if ($plugin["status"] === "active") $isActive = true;
		return $isActive;
	}

	static function getPluginConfig($path)
	{
		$delimiter = "\\";
		$delimiterPhrase = "app\Plugins\\";

		$path = str_replace("\\", "/", $path);

		if (strpos($path, $delimiterPhrase) === false) {
			$delimiter = "/";
			$delimiterPhrase = "app/Plugins/";
		}

		$chunks = explode($delimiterPhrase, $path);

		if (sizeof($chunks) <= 1) return null;
		$basePath = $chunks[0];
		$chunks2 = explode($delimiter, $chunks[1]);
		if (sizeof($chunks) <= 0) return null;
		$pluginFolderName = $chunks2[0];
		$dir = $basePath . $delimiterPhrase . $pluginFolderName;

		if (!file_exists($dir . $delimiter . "config.json")) return null;
		$fileContent = file_get_contents($dir . $delimiter . "config.json");
		return json_decode($fileContent, true);
	}

	static function getActivePluginScheduleTasks(){
		$pluginSlugs = self::getActivePluginSlugs();
		$tasks = [];

		foreach($pluginSlugs as $pluginSlug){
			$pluginTasks = self::getPluginScheduleTasks($pluginSlug);
			$tasks = [...$tasks, ...$pluginTasks];
		}

		return $tasks;
	}

	static function getPluginScheduleTasks($pluginSlug){
		$file = app_path("Plugins/$pluginSlug/app/console/schedule.php");
		if(!is_file($file)) return [];

		$classInstance = require $file;
		if($classInstance === NULL) return [];

		if(method_exists($classInstance, "tasks")) return $classInstance->tasks();
		else return [];
	}

	static function getActivePluginsScheduleCommands($options = []){
		$pluginSlugs = self::getActivePluginSlugs();
		$commands = [];

		foreach($pluginSlugs as $pluginSlug){
			$pluginCommands = self::getPluginScheduleCommands($pluginSlug, $options);
			$commands = [...$commands, ...$pluginCommands];
		}

		return $commands;
	}

	static function getPluginScheduleCommands($pluginSlug, $options = []){
		$backgroundCommands = $options["backgroundCommands"] ?? false;

		$file = app_path("Plugins/$pluginSlug/app/console/schedule.php");
		if(!is_file($file)) return [];

		$classInstance = require($file);

		if($backgroundCommands === true && method_exists($classInstance, "backgroundCommands")) return $classInstance->backgroundCommands();
		else return [];
	}

	
	// setup plugins

	static function runMigration($pluginSlug)
	{	
		$migrationDir = app_path("Plugins/$pluginSlug/database/migrations");
		if (!is_dir($migrationDir)) return;

		$files = glob($migrationDir . "/*.php");

		foreach ($files as $file) {

			$classInstance = require_once $file;
			$classInstance->down();
			$classInstance->up();

			if (method_exists($classInstance, "bridge")) {
				$bridgetArr = $classInstance->bridge();
				$oldBridgeArr = session()->get("pluginBridge");
				$mergedBridgeArr = array_merge($oldBridgeArr, $bridgetArr);
				session()->put("pluginBridge", $mergedBridgeArr);
			}
		}

		$pluginMigrationDirs = session()->get("pluginMigrationDirs");
		$pluginMigrationDirs[] = $migrationDir;
		session()->put("pluginMigrationDirs", $pluginMigrationDirs);

		self::runPluginBridge();
	}

	static function runLateMigrations(){
		$path = base_path('database/late-migrations');
		if(!is_dir($path)) return;

		$files = glob($path . "/*");

		foreach($files as $file){
			$classInstance = require_once $file;

			if(!is_bool($classInstance)){
				$classInstance->down();
				$classInstance->up();
			}
			
		}
	}

	static function runSeeder($pluginSlug)
	{
		$seederDir = app_path("Plugins/$pluginSlug/database/seeders");

		if (is_dir($seederDir)) {
			$seederFiles = glob($seederDir . "/*");

			foreach ($seederFiles as $file) {
				$classInstance = require_once $file;
				$classInstance->run();

				if (method_exists($classInstance, "bridge")) {
					$bridgetArr = $classInstance->bridge();
					$oldBridgeArr = session()->get("pluginBridge");
					$mergedBridgeArr = array_merge($oldBridgeArr, $bridgetArr);
					session()->put("pluginBridge", $mergedBridgeArr);
				}
			}

			$pluginMigrationDirs = session()->get("pluginMigrationDirs");
			$pluginMigrationDirs[] = $seederDir;
			session()->put("pluginMigrationDirs", $pluginMigrationDirs);
		}

		self::runPluginBridge();
	}

	static function runPluginBridge()
	{
		
		$completedMigrationDirs = session()->get("pluginMigrationDirs");
		$bridgeArr = session()->get("pluginBridge");

	
		foreach ($bridgeArr as $rowIndex => $row) {
			$match = [];
			$migrationsDirs = $row["dirs"] ?? [];
			$blueprints = $row["blueprints"] ?? [];
			$seeds = $row["seeds"] ?? [];

			foreach ($migrationsDirs as $migrationDir) {
				foreach ($completedMigrationDirs as $completedDir) {
					$completedDir = str_replace("\\", "/", $completedDir);
					if (strpos($completedDir, $migrationDir) !== false) $match[] = true;
				}
			}

			if (sizeof($migrationsDirs) != sizeof($match)) return;

			if (sizeof($blueprints) > 0) {
				foreach ($blueprints as $tableName => $blueprint) {
					Schema::create($tableName, $blueprint);
				}
			}

			if (sizeof($seeds) > 0) {
				foreach ($seeds as $func) {
					$func();
				}
			}

			unset($bridgeArr[$rowIndex]);
			session()->put("pluginBridge", $bridgeArr);
		}
	}

	static function deletePluginFolderInViews($folderName = "the-folder-name")
	{
		$destinationPath = resource_path("views/plugin-mails/$folderName");
		FS::deleteFolder([
			["path" => $destinationPath]
		]);
	}

	static function copyPluginFilesToFolderInViews($pluginSlug, $sourceFolderName, $destinationFolderName)
	{

		$sourcePath = app_path("/Plugins/" . $pluginSlug . "/$sourceFolderName");
		$destinationPath = resource_path("views/plugin-mails/$destinationFolderName");

		if (!is_dir($sourcePath)) return;

		if (!is_dir($destinationPath)) {
			FS::createFolder([
				[
					"path" => $destinationPath
				]
			]);
		}

		$sourceFiles = File::files($sourcePath);

		foreach ($sourceFiles as $file) {
			$fileName = basename($file);

			$sourceFilePath = $sourcePath . "/" . $fileName;
			$destinationFilePath = $destinationPath . "/" . $fileName;

			File::copy($sourceFilePath, $destinationFilePath);
		}
	}

	static function seedLanguageFiles($pluginSlugs)
	{
		$destinationPath = lang_path();
		$outputContent = [];

		$activeTheme = Theme::getActiveTheme();
		$themeSlug = $activeTheme["slug"];

		$themeLangPath = resource_path("/views/themes/$themeSlug/lang");
		
		if(is_dir($themeLangPath)) {
			$themeLangFiles = File::files($themeLangPath);

			foreach($themeLangFiles as $file){
				$fileName = pathinfo($file, PATHINFO_FILENAME);
				$extension = pathinfo($file, PATHINFO_EXTENSION);
				if ($extension !== "json") continue;

				$fileContent = file_get_contents($file);
				$fileContent = json_decode($fileContent, true);

				if (!isset($outputContent[$fileName])) $outputContent[$fileName] = [];
				$outputContent[$fileName] = array_merge($outputContent[$fileName], $fileContent);
			}
		}
		
		foreach ($pluginSlugs as $pluginSlug) {

			$sourcePath = app_path("/Plugins/" . $pluginSlug . "/lang");

			if (!is_dir($sourcePath)) continue;

			$sourceFiles = File::files($sourcePath);

			foreach ($sourceFiles as $file) {
				$fileName = pathinfo($file, PATHINFO_FILENAME);
				$extension = pathinfo($file, PATHINFO_EXTENSION);
				if ($extension !== "json") continue;

				$fileContent = file_get_contents($file);
				$fileContent = json_decode($fileContent, true);

				if (!isset($outputContent[$fileName])) $outputContent[$fileName] = [];
				$outputContent[$fileName] = array_merge($outputContent[$fileName], $fileContent);
			}
		}

		foreach ($outputContent as $langCode => $languageContent) {

			$fileContent = [];
			$destinationOriginalFilePath = $destinationPath . "/" . $langCode . "-original.json";
			$destinationFilePath = $destinationPath . "/" . $langCode . ".json";

			if (file_exists($destinationOriginalFilePath)) {
				$fileContent = file_get_contents($destinationOriginalFilePath);
				$fileContent = json_decode($fileContent, true);

				// to retain order or keys
				foreach ($fileContent as $key => $value) {
					unset($outputContent[$langCode][$key]);
				}
			}


			$outputContent[$langCode] = array_merge($fileContent, $outputContent[$langCode]);
			File::put($destinationFilePath, json_encode($outputContent[$langCode]));
		}
	}

	function setupPlugins()
	{
		App::make("clearCachedData");

		session()->put("pluginMigrationDirs", []);
		session()->put("pluginBridge", []);

		$activePlugins = Plugin::getActivePluginSlugs();

		foreach($activePlugins as $pluginSlug){
			self::runMigration($pluginSlug);
		}
		
		self::runLateMigrations();

		foreach ($activePlugins as $pluginSlug) {
			self::runSeeder($pluginSlug);
			self::deletePluginFolderInViews($pluginSlug . "-mails");
			self::copyPluginFilesToFolderInViews($pluginSlug, "mails", $pluginSlug . "-mails");
		}

		self::seedLanguageFiles($activePlugins);

		session()->forget("pluginMigrationDirs");
		session()->forget("pluginBridge");

		echo "Plugins are ready!";
	}
}
