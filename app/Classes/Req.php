<?php

namespace App\Classes;

use Laravel\Sanctum\PersonalAccessToken;

class Req {

	// TODO: depreciate
	static function getTokenAndUser($bearerToken = NULL){
		if ($bearerToken === NULL) return ["token"=>NULL, "user"=>NULL];
		
		$token = PersonalAccessToken::findToken($bearerToken);
		$user = $token->tokenable ?? NULL;

		return [
			"token"=>$token,
			"user"=>$user
		];
	}

	// TODO: depreciate
	static function loggedInUser($COOKIE){
		$bt = $_COOKIE["bt"] ?? NULL;
		$userAndToken = Req::getTokenAndUser($bt);
		return $userAndToken["user"] ?? NULL;
	}


	static function safeUser($user){
		$safeUser = array_merge([], $user->toArray());
		unset($safeUser["abilities"]);
		unset($safeUser["token"]);
		return $safeUser;
	}


}

?>