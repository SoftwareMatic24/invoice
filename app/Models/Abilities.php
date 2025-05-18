<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abilities extends Model
{
	use HasFactory;
	public $timestamps= false;
	protected $fillable = [
		"ability",
		"role_title"
	];

	// Query

	static function roleAbilities($roleTitle){
		return self::where("role_title", $roleTitle)->get();
	}

}
