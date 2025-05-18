<?php

namespace App\Plugins\Appearance\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultBranding extends Model {

	protected $fillable = [
		'column_name',
		'column_value'
	];

	protected $table = 'default_branding';

	// Query: Get

	static function getDefaultBranding(){
		return self::get()->pluck('column_value', 'column_name');
	}

}


?>