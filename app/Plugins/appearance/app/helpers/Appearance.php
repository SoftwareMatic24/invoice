<?php

namespace App\Plugins\Appearance\Helpers;

use App\Plugins\Appearance\Models\Branding;
use App\Plugins\Appearance\Models\DefaultBranding;

class Appearance {

	static function defaultBranding(){
		return DefaultBranding::getDefaultBranding()->toArray();
	}

	static function branding(){
		return Branding::getBranding()->toArray();
	}

	static function brandName(){
		$branding = self::branding();
		return $branding['brand-name'] ?? config('app.name');
	}

	static function brandPortalLogo(){
		$branding = self::branding();
		return $branding['brand-portal-logo'] ?? NULL;
	}

}

?>