<?php

namespace App\Http\Middleware;

use App\Classes\Req;
use App\Classes\Util;
use App\Plugins\ContactMessage\App\Models\ContactMessage;
use App\Plugins\Setting\Model\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Laravel\Sanctum\PersonalAccessToken;


class AuthCheck
{
	
	public function handle(Request $request, Closure $next, ...$abilities)
	{	
		
		$clientTimezone = $_COOKIE["client_timezone"] ?? NULL;
		$bearerToken = $_COOKIE["bt"] ?? NULL;
		
		$response = Req::getTokenAndUser($bearerToken);
		$user = $response["user"];
		$token = $response["token"];
		
		if ($user === NULL) {
			return redirect(Util::prefixedURL("/login"))->with("flashMessage", ["status" => "fail", "msg" => "Unauthorized access. Login first."]);
			exit;
		}
		
		$hasAbilities = true;
		$tokenAbilities = $token["abilities"];
		
		$user["abilities"] = $token["abilities"];
		$user["token"] = $token;
		
		foreach ($abilities as $ability) {
			if (!in_array($ability, $tokenAbilities)) $hasAbilities = false;
		}
		

		if ($hasAbilities === false) {
			return redirect(Util::prefixedURL("/login"))->with("flashMessage", ["status" => "fail", "msg" => "Unauthorized access. Login first."]);
			exit;
		}

		$settings = Cache::get("settings");
		$safeUser = Req::safeUser($user);


		$request['loggedInUser'] = $user;
		$request['loggedInSafeUser'] = $safeUser;
		$request['settings'] = $settings;
		$request["clientTimeZone"] = $clientTimezone;

		Session::put("loggedInUser", $user);
		
		$inactiveTime = 60 * 60;
		$portalFirstInterationTime = Session::get("portalFirstInteractionTime");

		if($portalFirstInterationTime === NULL) {
			$portalFirstInterationTime = time();
			Session::put("portalFirstInteractionTime", $portalFirstInterationTime);
		}

		if(time() - $portalFirstInterationTime > $inactiveTime) {
			Session::forget("portalFirstInteractionTime");
			return redirect(Util::prefixedURL("/logout"));
			exit;
		}
		else Session::put("portalFirstInteractionTime", time());
		
		return $next($request);
	}
}
