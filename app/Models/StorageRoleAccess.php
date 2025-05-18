<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageRoleAccess extends Model
{
	public $timestamps = false;
	protected $fillable = [
		"storage",
		"base_folder",
		"role_title"
	];
	protected $table = "storage_role_access";

	// Query


	// Rows

	static function getRows(){
		return self::get();
	}

	static function getRowByStorageBaseFolderRoleTitle($storage, $baseFolder, $roleTitle){
		return self::where("storage",$storage)->where("base_folder", $baseFolder)->where("role_title", $roleTitle)->first();
	}


}
