<?php

namespace App\Plugins\Appearance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Branding extends Model
{

	protected $fillable = [
		'column_name',
		'column_value'
	];

	public $timestamps = false;
	protected $table = 'branding';

	// Query: Get

	static function getBranding()
	{
		return self::get()->pluck('column_value', 'column_name');
	}

	// Query: Save

	static function saveBranding($columnName, $columnValue)
	{

		Cache::forget("branding");
		$record = self::getBranding()->toArray();

		if (!array_key_exists($columnName, $record)) return self::addBranding($columnName, $columnValue);
		else return self::updateBranding($columnName, $columnValue);
	}

	static function addBranding($columnName, $columnValue)
	{
		return self::create([
			"column_name" => $columnName,
			"column_value" => $columnValue
		]);
	}

	static function updateBranding($columnName, $columnValue)
	{
		return self::where("column_name", $columnName)->update([
			"column_value" => $columnValue
		]);
	}
}
