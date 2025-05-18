<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	
	public function register(): void
	{
	}

	public function boot(): void
	{
		require_once app_path('Helpers/util.php');
		require_once app_path('Helpers/brand.php');
		require_once app_path('Helpers/theme.php');
		require_once app_path('Helpers/plugin.php');
		require_once app_path('Helpers/role.php');
		require_once app_path('Helpers/user.php');
		require_once app_path('Helpers/category.php');
		require_once app_path('Helpers/http.php');
		require_once app_path('Helpers/constant.php');
		require_once app_path('Helpers/reset.php');
	}
}
