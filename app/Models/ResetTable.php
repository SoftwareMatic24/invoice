<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ResetTable extends Model
{

	function conditions()
	{
		return $this->hasMany(ResetTableCondition::class, 'reset_table_id', 'id');
	}
}
