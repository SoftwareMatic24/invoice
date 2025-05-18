<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ResetSetting extends Model
{
	public $timestamps = false;

	/**
	 * Setting: Get
	 */

	static function settings()
	{
		return self::first();
	}

	/**
	 * Setting: Save
	 */

	static function updateSetting(string $column, mixed $value){
		return self::where('id', 1)->update([
			$column=>$value
		]);
	}

}
