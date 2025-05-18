<?php

namespace App\Plugins\Menu\Model;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{

	public $timestamps = false;
	protected $fillable = [
		"name",
		"display_name",
		"items",
		"presistence",
		"create_datetime",
		"update_datetime"
	];


	/**
	 * ===== Query
	 */

	static function getMenus()
	{
		return self::orderBy("id", "DESC")->get();
	}

	static function getMenu($menuId)
	{
		return self::where("id", $menuId)->first();
	}

	static function getMenuByName($menuName)
	{
		return self::where("name", $menuName)->first();
	}

	static function addMenu($name, $items, $displayName = NULL, $presistence = 'temporary')
	{
		return self::create([
			"name" => $name,
			"display_name"=>$displayName,
			"items" => json_encode($items ?? []),
			"presistence" => $presistence,
			"create_datetime" => DateTime::getDateTime()
		]);
	}

	static function updateMenu($menuId, $name, $items, $displayName = NULL)
	{
		return self::where("id", $menuId)->update([
			"name" => $name,
			"display_name"=>$displayName,
			"items" => json_encode($items ?? []),
			"update_datetime" => DateTime::getDateTime()
		]);
	}

	static function deleteMenu($menuId)
	{
		return self::where("id", $menuId)->delete();
	}
};
