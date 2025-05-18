<?php

namespace App\Plugins\Components\Model;

use Illuminate\Database\Eloquent\Model;

class ComponentGroup extends Model
{

	public $timestamps = false;

	protected $fillable = [
		"name",
		"max_entries",
		"component_id",
		"create_datetime",
		"update_datetime"
	];

	function schema(){
		return $this->hasMany(ComponentGroupSchema::class, 'component_group_id', 'id');
	}

}
