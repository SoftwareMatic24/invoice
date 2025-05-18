<?php

namespace App\Plugins\SocialMedia\Helpers;

use App\Plugins\SocialMedia\Model\SocialMediaLinks;

class SocialMedia {

	static function links(){
		return SocialMediaLinks::getSocialMediaLinks()->toArray();
	}

}

?>