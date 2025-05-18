<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APIKeyIPAccess extends Model
{
	use HasFactory;

	public $timestamps = false;
	protected $fillable = [
		"ip",
		"access",
		"api_key_id"
	];
	protected $table = "api_key_ip_access";

}
