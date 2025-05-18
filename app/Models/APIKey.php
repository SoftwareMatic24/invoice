<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APIKey extends Model
{
	use HasFactory;

	public $timestamps = false;
	protected $fillable = [
		"key",
		"public_key",
		"private_key",
		"create_datetime"
	];
	protected $table = "api_keys";

	function ipAccess(){
		return $this->hasMany(APIKeyIPAccess::class, "api_key_id", "id");
	}

}
