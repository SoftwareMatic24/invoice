<?php

namespace App\Plugins\Menu\Controller;

use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Plugins\Menu\Model\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MenuController extends Controller
{

	// views

	function menusView()
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => "Menu",
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => "Menu",
			"pageSlug" => "menu",
			"pluginConfig" => $config,
		];

		return PluginController::loadView(__DIR__, "menus.blade.php", $pageData);
	}

	function saveMenuView($menuId = NULL)
	{

		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => $menuId === NULL ? "New Menu" : "Edit Menu",
			"backURL" => Util::prefixedURL($config["slug"] . "/"),
			"pageName" => $menuId === NULL ? "New Menu" : "Edit Menu",
			"pageSlug" => "menu",
			"pluginConfig" => $config,
			"menuId" => $menuId
		];

		return PluginController::loadView(__DIR__, "save-menu.blade.php", $pageData);
	}

	/**
	 * ===== Layout Methods
	 */

	function menuLayout($menuId, $options = [])
	{
		$parentClass = $options["parentClass"] ?? "";
		$layout = "";
		$menu = Menu::getMenu($menuId);
		$menuItems = $menu->items ?? NULL;
		$menuItems = json_decode($menuItems === NULL ? "[]" : $menuItems, true);
		$layout = $this->menuChildrenLayout($menuItems, $options);
		return "<ul class='" . $parentClass . "'>" . $layout . "</ul>";
	}

	function menuChildrenLayout($items, $options)
	{
		$layout = "";
		$childClass = $options["childClass"] ?? "";
		foreach ($items as $item) {
			$url = $item["url"];
			$text = $item["text"];
			$target = $item["newTab"] == true ? "_blank" : "_self";
			if ($url === NULL) $url = "";
			$url = url($url);

			if ($item["children"] ?? false) {
				$childrenLayout = $this->menuChildrenLayout($item["children"], $options);
				if ($childrenLayout !== "") {
					$layout .= '<li class="' . $childClass . '" ><a target="'. $target .'" href="' . $url . '">' . $text . '</a>';
					$layout .= '<ul>' . $childrenLayout . '</ul>';
					$layout .= '</li>';
				}
			} else {
				if ($text !== "") {
					$layout .= '<li class="' . $childClass . '"><a target="'. $target .'" href="' . $url . '">' . $text . '</a></li>';
				}
			}
		}
		return $layout;
	}


	/**
	 * ===== Methods
	 */

	function formatMenu(array $menu){
		$urlLanguageCode = app()->getLocale();
		$languages = Cache::get("languages");

		$primaryLanguageCode = array_reduce($languages, function($acc, $lang){
			if($lang["type"] === "primary") $acc = $lang["code"];
			return $acc;
		}, NULL);

		if(empty($menu)) return [];
		$items = $menu["items"];
		if(!empty($items) && is_string($items)) $items = json_decode($items, true); 
		$fItems = $this->formatMenuItems($items, $urlLanguageCode, $primaryLanguageCode);
		$menu["items"] = $fItems;
		return $menu;
	}

	function formatMenuItems(array $items, $urlLanguageCode, $primaryLanguageCode){
		$arr = [];
		foreach($items as $item){
			
			$tempArr = [
				"title"=>$item["text"],
				"url"=>NULL,
				"slug"=>NULL,
				"newTab"=>$item["newTab"],
				"type"=>$item["type"],
			];

			if(empty($item["url"]) && $tempArr["type"] === "page") {
				$tempArr["url"] = Util::themeURL("/", $urlLanguageCode, $primaryLanguageCode);
				$tempArr["slug"] = Str::slug($item["title"]);
			}
			else if(!empty($item["url"]) && $tempArr["type"] === "page") {
				$tempArr["url"] = Util::themeURL($item["url"], $urlLanguageCode, $primaryLanguageCode);
				$tempArr["slug"] = $item["url"];
			}
			else if(empty($item["url"]) && $tempArr["type"] !== "page") {
				$tempArr["url"] = "#";
				$tempArr["slug"] = Str::slug($item["title"]);
			}
			else if(!empty($item["url"]) && $tempArr["type"] !== "page") {
				$tempArr["url"] = $item["url"];
				$tempArr["slug"] = Str::slug($item["title"]);
			}

			if(!empty($item["children"])){
				$tempArr["children"] = $this->formatMenuItems($item["children"], $urlLanguageCode, $primaryLanguageCode);
			}

			$arr[] = $tempArr;

		}
		return $arr;
	}

	function getFormatMenuByName($name){
		$menu = Menu::getMenuByName($name);
		if(!empty($menu)) $menu = $menu->toArray();
		return $this->formatMenu($menu);
	}

	function getMenus()
	{
		return Menu::getMenus();
	}

	function getMenu($menuId)
	{
		return Menu::getMenu($menuId);
	}

	function saveMenu($menuId = NULL, $data)
	{


		if ($menuId === NULL) {

			$validator = Validator::make($data, [
				"menu" => "required|unique:menus,name"
			]);

			if ($validator->fails()) return ["status" => "fail", "msg" => $validator->errors()->all()[0]];

			$menu = Menu::addMenu($data["menu"], $data["items"], $data["displayName"] ?? NULL);
			$menuId = $menu->id;
		} else {

			
			$menuExists = Menu::getMenuByName($data["menu"]);
			if ($menuExists !== NULL && $menuExists->id != $menuId) return ["status" => "fail", "msg" => "Menu with this name already exists."];
			
			if(!$this->canUpdateName($menuId, $data['menu'])){
				return ["status" => "fail", "msg" => "This menu name is not allowed to update."];
			}

			Menu::updateMenu($menuId, $data["menu"], $data["items"], $data["displayName"] ?? NULL);
		}

		return ["status" => "success", "msg" => "Menu is saved.", "menuId" => $menuId];
	}

	function deleteMenu($menuId)
	{

		$menu = $this->getMenu($menuId);
		if(empty($menu)) return ["status" => "fail", "msg" => "Could not delete menu."];
		else if($menu->presistence === 'permanent') return ["status" => "fail", "msg" => "Default menu can not be deleted."]; 

		Menu::deleteMenu($menuId);
		return ["status" => "success", "msg" => "Menu is deleted."];
	}

	// Utils

	function canUpdateName($menuId, $newName){
		$menu = $this->getMenu($menuId);
		if(empty($menu)) return true;

		if($menu->name != $newName && $menu->lock_name == 1) return false;

		return true;
	}


	/**
	 * ===== API
	 */

	function menusRequest()
	{
		return $this->getMenus();
	}

	function menuRequest($menuId)
	{
		return $this->getMenu($menuId);
	}

	function addMenuRequest(Request $request)
	{
		$data = $request->post();
		return $this->saveMenu(NULL, $data);
	}

	function updateMenuRequest(Request $request, $menuId)
	{
		$data = $request->post();
		return $this->saveMenu($menuId, $data);
	}

	function deleteMenuRequest($menuId)
	{
		return $this->deleteMenu($menuId);
	}
}
