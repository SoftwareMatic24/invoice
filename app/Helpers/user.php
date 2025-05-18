<?php

use App\Models\User as ModelsUser;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\PersonalAccessToken;

class User
{

	static function user($userId = NULL)
	{
		$user = NULL;
		if (empty($userId)) $user = self::authUser($_COOKIE ?? NULL);
		else $user = ModelsUser::getUserById($userId);

		if (!empty($user) && $user instanceof Model) $user = $user->toArray();
		return $user;
	}

	static function authUser($cookieStr)
	{
		$userAndToken = self::tokenAndUser($cookieStr["bt"] ?? NULL);
		return $userAndToken["user"] ?? NULL;
	}

	static function authUserAbilities($cookieStr){
		$userAndToken = self::tokenAndUser($cookieStr["bt"] ?? NULL);
		return $userAndToken['token']->abilities;
	}

	static function tokenAndUser($bearerToken)
	{
		if (empty($bearerToken)) return ["token" => NULL, "user" => NULL];

		$token = PersonalAccessToken::findToken($bearerToken);
		$user = $token->tokenable ?? NULL;
	
		$user = ModelsUser::getUserById($user->id ?? NULL);

		return ["token" => $token,"user" => $user];
	}

	static function userName($userId = NULL){
		$firstName = self::userFirstName($userId);
		$lastName = self::userLastName($userId);
		return fullName($firstName, $lastName);
	}

	static function userFirstName($userId = NULL){
		$user = self::user($userId);
		return $user['first_name'] ?? NULL;
	}

	static function userLastName($userId = NULL){
		$user = self::user($userId);
		return $user['last_name'] ?? NULL;
	}

	static function userEmail($userId = NULL){
		$user = self::user($userId);
		return $user['email'] ?? NULL;
	}

	static function userPhone($userId = NULL){
		$user = self::user($userId);
		return $user['phone'] ?? NULL;
	}

	static function userDOB($userId = NULL){
		$user = self::user($userId);
		return $user['dob'] ?? NULL;
	}

	static function userRole($userId = NULL){
		$user = self::user($userId);
		return $user['role_title'];
	}

	static function userStatus($userId = NULL){
		$user = self::user($userId);
		return $user['status'];
	}

	static function userImage($userId = NULL){
		$user = self::user($userId);
		return $user['image'];
	}

	static function users(){
		return ModelsUser::getUsers()->toArray();
	}

	static function usersGroupByRoleTitle(){
		return ModelsUser::getUsersGroupByRoleTitle()->toArray();
	}

	static function usersByRoleTitle(string $role_title, null|string $status = NULL, null|string $visibility = NULL){
		return ModelsUser::getUsersByRole($role_title, $status, $visibility)->toArray();
	}

	static function usersByIds(array $user_ids, null|string $status = NULL, null|string $visibility = NULL){
		return ModelsUser::getUsersByIds($user_ids, $status, $visibility)->toArray();
	}

	static function userBySlug(string $slug, null|string $status = NULL){
		$user = ModelsUser::getUserBySlug($slug, $status);
		return !empty($user) ? $user->toArray() : NULL;
	}

	static function userByEmail(string $email){
		$user = ModelsUser::getUserByEmail($email);
		return !empty($user) ? $user->toArray() : NULL;
	}

	static function authUserCan($cookieStr, $ability){
		$response = self::tokenAndUser($cookieStr['bt'] ?? NULL);
		if(empty($response)) return false;

		$token = $response['token'] ?? NULL;
		if(empty($token)) return NULL;

		$token = $token->toArray();
		$tokenAbilities = $token['abilities'] ?? [];

		if(in_array($ability, $tokenAbilities)) return true;
		return false;
	}

}
