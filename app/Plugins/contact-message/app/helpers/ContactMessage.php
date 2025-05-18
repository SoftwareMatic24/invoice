<?php

namespace App\Plugins\ContactMessage\Helpers;

use App\Plugins\ContactMessage\App\Models\ContactMessage as ContactMessageModel;

class ContactMessage {

	static function messages(){
		return ContactMessageModel::getContactMessages()->toArray();
	}

}

?>