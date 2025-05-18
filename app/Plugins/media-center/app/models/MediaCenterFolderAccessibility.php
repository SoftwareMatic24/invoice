<?php

namespace App\Plugins\MediaCenter\Models;

use Illuminate\Database\Eloquent\Model;

class MediaCenterFolderAccessibility extends Model {

	public $timestamps = false;
	protected $fillable = [
		"role_title",
		"media_center_folder_id",
		"create_datetime"
	];

}

?>