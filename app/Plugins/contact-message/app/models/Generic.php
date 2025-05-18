<?php

namespace App\Plugins\ContactMessage\App\Models;
require_once __DIR__."/ContactMessage.php";

use Illuminate\Database\Eloquent\Model;

class Generic extends Model {

	static function index($userRole = NULL){

		$data = [];
		if($userRole === "admin") $data["contact-message--count"] = ContactMessage::unreadMessageCount();
		return $data;
	}

}
