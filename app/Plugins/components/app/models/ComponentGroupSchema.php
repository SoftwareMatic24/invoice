<?php

namespace App\Plugins\Components\Model;

use Illuminate\Database\Eloquent\Model;

class ComponentGroupSchema extends Model
{

	public $timestamps = false;

	protected $fillable = [
		"label",
		"type",
		"component_group_id",
		"create_datetime",
		"update_datetime"
	];
}
