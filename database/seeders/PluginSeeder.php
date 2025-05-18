<?php

namespace Database\Seeders;

use App\Classes\DateTime;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PluginSeeder extends Seeder
{
	public function run(): void
	{
		try {
			$project = getActiveProject();
			$plugins = getPlugins();

			$projectPlugins = $project["plugins"] ?? [];
			$projectPluginChildren = $project["pluginChildren"] ?? [];
			$projectPluginUpdate = $project["pluginUpdate"] ?? [];
			
		
		
			foreach ($plugins as $pluginIndex => &$plugin) {
				if (in_array($plugin["slug"], $projectPlugins) === true) {
					$plugin["status"] = "active";
					$plugin["options"] = json_encode($plugin["options"] ?? []);
					$plugin["create_datetime"] = DateTime::getDateTime();
				}
				else unset($plugins[$pluginIndex]);
			}
	
			
			foreach ($projectPluginChildren as $pluginSlug => $children) {
				foreach ($plugins as &$plugin) {
					if ($pluginSlug === $plugin["slug"] && pluginHasChildren($plugin)) {
	
						$plugin["options"] = json_decode($plugin["options"], true);
	
						foreach ($children as $child) {

							foreach($plugin["options"]["children"] as $originalChildIndex=>$originalChild){
								if($originalChild["slug"] == $child["slug"]) unset($plugin["options"]["children"][$originalChildIndex]);
							}

							$plugin["options"]["children"][] = $child;
						}
	
						$plugin["options"] = json_encode($plugin["options"]);
					}
				}
			}
		
			foreach ($projectPluginUpdate as $pluginSlug => $row) {
				foreach ($plugins as &$plugin) {
					if ($pluginSlug === $plugin["slug"]) {
						foreach ($row as $key => $value) {
							if ($key == "options") {
								$plugin[$key] = json_encode($value);
							}
							else $plugin[$key] = $value;
						}
					}
				}
			}
			
		
			DB::table("plugins")->insert($plugins);
		}
		catch(Exception $e){}
	}
}

// Project & Plugins

function getActiveProject()
{
	$projectSlug = getActiveProjectSlug();
	$projectFileContent = file_get_contents(base_path("_projects/$projectSlug.json"));
	return json_decode($projectFileContent, true);
}

function getActiveProjectSlug()
{
	$projectFilePath = base_path("_projects/project.json");
	$projectFileContent = file_get_contents($projectFilePath);
	$projectFileContent = json_decode($projectFileContent, true);
	return $projectFileContent["project"];
}

function getPlugins()
{
	$plugins = [];
	$files = getPluginFiles();
	foreach ($files as $fileName) {
		$path = base_path("_plugins/$fileName");
		$fileContent = file_get_contents($path);
		$fileContent = json_decode($fileContent, true);
		$plugins = [...$plugins, ...$fileContent];
	}
	return $plugins;
}

function getPluginFiles()
{
	$pluginFiles = scandir(base_path("_plugins"));
	$pluginFiles = array_diff($pluginFiles, ['.', '..']);
	return $pluginFiles;
}

// DEBUG

function pluginHasOptions($plugin)
{
	if (isset($plugin["options"]) && $plugin["options"] !== NULL) return true;
	return false;
}

function pluginHasChildren($plugin)
{
	if (!pluginHasOptions($plugin)) return false;
	$options = json_decode($plugin["options"], true);

	if (isset($options["children"]) && sizeof($options["children"]) > 0) return true;
	else return false;
}

function listPluginsChildren($pluginSlugs, $plugins)
{
	foreach ($plugins as $plugin) {
		if (in_array($plugin["slug"], $pluginSlugs)) {
			print('<pre>' . print_r("======" . $plugin["slug"] . "======", true) . '</pre>');
			print('<pre>' . print_r("", true) . '</pre>');
			if (!pluginHasChildren($plugin)) continue;
			$pluginOptions = json_decode($plugin["options"], true);

			foreach ($pluginOptions["children"] as $child) {
				print('<pre>' . print_r($child, true) . '</pre>');
			}
		}
	}
}


// ============= CODE BLOCK

// $pagesChild = [
		// 	"title" => "Page Child",
		// 	"slug" => "page-child",
		// 	"version" => "1.0.0",
		// 	"status" => "active",
		// 	"type"=>"child",
		// 	"presistence" => "permanent",
		// 	"visibility" => "sidebar",
		// 	"order" => 1,
		// 	"options" => json_encode([
		// 		"icon" => "solid-pages",
		// 		"navigationSlug" => "pages",
		// 		"abilities" => ["view-page"],
		// 		"childOf"=>"pages",
		// 		"children" => [
		// 			[
		// 				"title" => "PC1",
		// 				"slug" => "pc1",
		// 				"navigationSlug" => "pages-save"
		// 			],
		// 			[
		// 				"title" => "PC2",
		// 				"slug" => "manage",
		// 				"navigationSlug" => "pages-manage"
		// 			]
		// 		]
		// 	]),
		// 	"create_datetime" => MyDateTime::getDateTime()
		// ];