<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class OnAllPortalMiddleware
{

	public function handle(Request $request, Closure $next): Response
	{
		$settings = Cache::get("settings");
		App::setLocale($settings["portal-lang"]["column_value"] ?? "en");
		return $next($request);
	}
}
