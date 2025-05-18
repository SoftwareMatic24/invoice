<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reset extends Model
{

	protected $fillable = [
		'name',
		'status',
		'create_datetime',
		'update_datetime'
	];

	function tables()
	{
		return $this->hasMany(ResetTable::class, 'reset_id', 'id');
	}

	static function basicRelation()
	{
		return self::with('tables.conditions');
	}

	/**
	 * Query: Get
	 */

	static function resetById(null|int|string $id){
		return self::basicRelation()
			->where('id', $id)
			->first();
	}

	static function activeResets()
	{
		return self::basicRelation()->where('status', 'active')->get();
	}
}
