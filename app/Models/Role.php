<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	use HasFactory;

	protected $fillable = [
		"title"
	];

	function abilities()
	{
		return $this->hasMany(Abilities::class, "role_title", "title");
	}

	static function basicRelation(){
		return self::with('abilities');
	}

	/**
	 * Query: Get
	 */

	static function getRole($id)
	{
		return self::basicRelation()->find($id);
	}

	static function getRoles()
	{
		return self::basicRelation()->orderBy("title", "asc")->get();
	}
}
