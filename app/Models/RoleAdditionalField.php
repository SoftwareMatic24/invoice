<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleAdditionalField extends Model
{
    use HasFactory;

	// Query: Get

	static function roleAdditonalFields($role){
		return self::where('role_title', $role)->get();
	}

}
