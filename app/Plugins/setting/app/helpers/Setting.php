<?php

namespace App\Plugins\Setting\Helpers;

use App\Plugins\Setting\Model\ExternalIntegration;
use App\Plugins\Setting\Model\Sitemap;
use App\Plugins\Setting\Model\TwoFactorAuth;
use Illuminate\Support\Facades\Cache;

class Setting
{
	/**
	 * Setting: Get
	 */

	static function setting($key)
	{
		$settings = self::settings();
		return $settings[$key]['column_value'] ?? NULL;
	}

	static function settings()
	{
		return Cache::get('settings');
	}

	/**
	 * Sitemap: Get
	 */

	static function sitemap()
	{
		$sitemap = Sitemap::getSitemap();
		return $sitemap->toArray();
	}

	/**
	 * 2FA: Get
	 */

	static function get2FA($userId){
		return TwoFactorAuth::get2FaForUser($userId);
	}

	/**
	 * External Integrations: Get
	 */

	static function externalIntegrations(){
		return ExternalIntegration::getExternalIntegrations()->toArray();
	}

}
