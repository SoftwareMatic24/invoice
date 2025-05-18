<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryClassification extends Model
{	

	public $timestamps = false;

	/**
	 * Query: Get
	 */

	static function rows() {
		return self::orderBy('id', 'DESC')->get();
	}
}
