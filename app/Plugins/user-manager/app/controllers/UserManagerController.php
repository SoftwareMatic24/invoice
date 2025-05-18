<?php

namespace App\Plugins\UserManager\Controller;

use App\Classes\DateTime;
use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginBridgeController;
use App\Http\Controllers\PluginController;
use App\Http\Controllers\ShortCodeController;
use App\Jobs\SendEmailJob;
use App\Models\Abilities;
use App\Models\Action;
use App\Models\Lock;
use App\Models\Role;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserDetail;
use App\Models\UserSetting;
use App\Plugins\EmailTemplate\Model\EmailTemplate;
use App\Plugins\Setting\Controller\TwoFactorAuthController;
use HTTP;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use User as GlobalUser;

class UserManagerController extends Controller
{

	// views

	function saveUserView($userId = NULL)
	{
		$user = [];
		$config = PluginController::getPluginConfig(__DIR__);
		$settings = Cache::get("settings");

		if ($userId !== NULL) $user = User::getUserById($userId);
		$roles = Role::getRoles();

		$pageData = [
			"tabTitle" => $userId === NULl ? ucwords(__("add new user")) : ucwords(__("edit user")),
			"backURL" => Util::prefixedURL($config["slug"] . "/manage"),
			"pageName" => $userId === NULl ? ucwords(__("new user")) : ucwords(__("edit user")),
			"pageSlug" => $userId == NULL ? "user-save" : "user-manage",
			"pluginConfig" => $config,
			"userId" => $userId,
			"user" => $user,
			"roles" => $roles,
			"settings" => $settings
		];

		return PluginController::loadView(__DIR__, "save-user.blade.php", $pageData);
	}

	function manageUsersView()
	{
		$config = PluginController::getPluginConfig(__DIR__);


		$pageData = [
			"tabTitle" => ucwords(__("manage users")),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => ucwords(__("manage users")),
			"pageSlug" => "user-manage",
			"pluginConfig" => $config
		];

		return PluginController::loadView(__DIR__, "manage-users.blade.php", $pageData);
	}

	function rolesView()
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => ucwords(__("manage roles")),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => ucwords(__("manage roles")),
			"pageSlug" => "user-roles",
			"pluginConfig" => $config
		];

		return PluginController::loadView(__DIR__, "manage-roles.blade.php", $pageData);
	}

	function saveRoleView($roleId = NULL)
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => $roleId == NULL ? ucwords(__("add new role")) : ucwords(__("edit role")),
			"backURL" => Util::prefixedURL($config["slug"] . "/roles"),
			"pageName" => $roleId == NULL ? ucwords(__("new role")) : ucwords(__("edit role")),
			"pageSlug" => "user-roles",
			"pluginConfig" => $config,
			"roleId" => $roleId
		];

		return PluginController::loadView(__DIR__, "save-role.blade.php", $pageData);
	}

	function abilitiesView()
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$abilities = Abilities::get();

		$pageData = [
			"tabTitle" => __("privileges"),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __("privileges"),
			"pageSlug" => "user-abilities",
			"pluginConfig" => $config,
			"abilities" => $abilities
		];

		return PluginController::loadView(__DIR__, "manage-abilities.blade.php", $pageData);
	}
	

	/**
	 *  Account
	 */

	function auth(array $data, array $options = [])
	{

		$rValid = $this->validateAuthData($data);
		if (!$rValid["status"]) return HTTP::inStringResponse(HTTP::inBoolArray(false, __('login-failed'), $rValid['msg'], 422));

		$fData = $this->formatAuthData($data);
		$fOptions = $this->formatAuthOptions($options);

		$user = NULL;
		if ($fOptions["userIdToAuth"] !== false) $user = User::getUserById($fOptions["userIdToAuth"]);
		else $user = User::getUserByEmail($fData["email"]);

		$rAllowedToLogin = $this->isUserAllowedToLogin($user);

		if (!$rAllowedToLogin["status"]) {
			if (!empty($user) && $user->status === "inactive") $this->sendAccountConfirmationMail($user->id);
			return HTTP::inStringResponse(HTTP::inBoolArray(false, __('login-failed'), $rAllowedToLogin["msg"], 403));
		}

		$rLocked = $this->isUserLockedToAuth($user->id);

		if ($rLocked["status"]) {
			return HTTP::inStringResponse(HTTP::inBoolArray(false, __('account-locked-notification-heading'), $rLocked["msg"], 403));
		}

		$maxAttempts = $user->max_attempts;
		$remainingAttempts = (--$maxAttempts);
		$error = NULL;
		$errorCode = 403;

		$rPasswordVerify = $this->isUserPasswordCorrect($data["password"], $user["password"], $fOptions["skipPasswordVerification"]);

		if (!$rPasswordVerify["status"]) {
			$error = $rPasswordVerify["msg"] ?? __('invalid-cred-notification-description');
			$errorCode = 403;
		}

		$r2FA = $this->canEnforce2FA($user->id);
		if (!$fOptions["skip2Fa"] && $r2FA["status"] && empty($error)) return (new TwoFactorAuthController)->send2FaMail($user->id);

		if (!$fOptions["skipAuthSuccessfulBridge"]) {
			$rBridge = $this->successAuthBridge($user, $data);
			if (isset($rBridge["status"]) && $rBridge["status"] === "fail") $error = $rBridge["msg"] ?? __('invalid-cred-notification-description');
			if ($rBridge !== NULL) return $rBridge;
		}


		if (!empty($error) && $remainingAttempts === 0) $error = __('account-locked-notification-description', ['minutes' => 5]);
		else if (!empty($error) && $remainingAttempts <= 3) $error = __('account-lock-attempts-notification-description', ['attempts' => $remainingAttempts]);

		if (!empty($error)) {
			$this->onInvalidAuthPassword($user->id, $remainingAttempts);
			return HTTP::inStringResponse(HTTP::inBoolArray(false, __('login-failed'), $error, $errorCode));
		}

		$this->onSuccessfulAuth($user->id);

		$abilities = Abilities::where('role_title', $user['role_title'])->pluck('ability')->toArray();
		$token = $user->createToken('accountToken', $abilities)->plainTextToken;

		setcookie('bt', $token, 0, '/');
		return response()->json(["status" => "success", "msg" => __("authenticated-notification"), "user" => $user, "token" => $token]);
	}

	function register(array $data)
	{
		if (!$this->isRegistrationAllowed()) {
			return HTTP::inBoolArray(false, __('registration-failed'), __('reg-not-allowed-notification-description'), 403);
		}

		if (!$this->isRegisterableRole($data["roleTitle"] ?? NULL)) {
			return HTTP::inBoolArray(false, __('registration-failed'), __('reg-role-not-allowed-notification-description'), 403);
		}

		$firstName = $data["firstName"] ?? NULL;
		$lastName = $data["lastName"] ?? NULL;

		if (isset($data["name"])) {
			$names = Util::firstLastNames($data["name"]);
			$firstName = $names["firstName"];
			$lastName = $names["lastName"];
		}

		$safeData = [
			"firstName" => $firstName,
			"lastName" => $lastName,
			"email" => $data["email"] ?? NULL,
			"password" => $data["password"] ?? NULL,
			"confirmPassword" => $data["confirmPassword"] ?? NULL,
			"roleTitle" => $data["roleTitle"] ?? NULL
		];

		return $this->addUser($safeData);
	}

	function accountConfirmation($uid)
	{

		$action = Action::getActionByUid($uid);
		if (empty($action)) die("Link expired.");

		$action = $action->toArray();
		$slug = $action['slug'];
		$status = $action['status'];

		if ($slug !== "ACCOUNT_CONFIRM" || $status != "pending") die("Link expired.");

		$expiry = $action['data']['expiry'];
		$now = DateTime::getDateTime();

		if (DateTime::dateTimeLessThan($expiry, $now)) die("Link expired");

		$userId = $action['data']['userId'];
		User::updateStatus($userId, 'active');

		Action::updateStatusByUid($uid, 'complete');

		return ['status' => true];
	}

	function forgotPassword($email)
	{
		$user = User::getUserByEmail($email);

		if (empty($email)) {
			return HTTP::inBoolArray(false, __('action-required'), __('email-field-required'), 422);
		} else if (empty($user)) {
			return HTTP::inBoolArray(false, __('request-failed'), __('account-not-found-notification-description'), 404);
		}

		$this->sendForgotPasswordMail($user->id);

		return HTTP::inBoolArray(true, __('forgot-password-notification-heading'), __('forgot-password-notification-description'));
	}

	function resetPassword(array $data)
	{

		$uid = $data["uid"] ?? NULL;
		$password = $data["password"] ?? NULL;
		$confirmPassword = $data["confirmPassword"] ?? NULL;
		$action = Action::getActionByUid($uid);

		if (empty($action) || $action->status !== "pending") {
			return HTTP::inBoolArray(false, __('link-expired-notification-heading'), __('link-expired-notification-description'));
		}

		$userId = $action->data['userId'];

		$expiry = $action->data['expiry'];
		$now = DateTime::getDateTime();
		$isExpired = DateTime::dateTimeLessThan($expiry, $now);

		if ($isExpired) {
			return HTTP::inBoolArray(false, __('link-expired-notification-heading'), __('link-expired-notification-description'));
		}

		$uResponse = $this->updateUserPassword($userId, $password, $confirmPassword);
		if (!$uResponse["status"]) return $uResponse;

		Action::updateStatusByUid($uid, "complete");

		return HTTP::inBoolArray(true, __('password-update-notification-heading'), __('password-update-notification-description'));
	}

	function logout($redirect = true)
	{
		$param = request()->input();
		$redirectURL = $param["redirectURL"] ?? Util::prefixedURL("/login");

		Session::forget('loggedInUser');
		Session::forget('loggedInUserExtra');
		setcookie('bt', '', 0, '/');
		$bearerToken = $_COOKIE["bt"] ?? null;

		if ($bearerToken === null) return ["status" => "fail", "msg" => "Failed to logout. Invalid token.", "code" => 422];
		$token = PersonalAccessToken::findToken($bearerToken);
		if ($token !== null) $token->delete();

		(new PluginBridgeController())->bridge("LOGOUT", NULL);

		if ($redirect === true) return redirect($redirectURL);
		return ["status" => "success", "msg" => "Successfully logged out."];
	}

	// Requests

	function authRequest(Request $request)
	{
		$data = $request->post();
		return $this->auth($data);
	}

	function registerRequest(Request $request)
	{
		$data = $request->post();
		$response = $this->register($data);
		return HTTP::inStringResponse($response);
	}

	function accountConfirmationRequest($uid)
	{
		$response = $this->accountConfirmation($uid);
		if (!$response['status']) die($response['msg']);

		Session::flash(
			'flashMessage',
			[
				'status' => 'success',
				'heading' => __('account-confirmed-notification-heading'),
				'description' => __('account-confirmed-notification-description')
			]
		);
		return redirect(url('/portal/login'));
	}

	function forgotPasswordRequest(Request $request)
	{
		$data = $request->post();
		$response = $this->forgotPassword($data["email"] ?? NULL);
		return HTTP::inStringResponse($response);
	}

	function resetPasswordRequest(Request $request)
	{
		$data = $request->post();
		$response = $this->resetPassword($data);
		return HTTP::inStringResponse($response);
	}

	function logoutRequest()
	{
		$response = $this->logout(false);
		return response()->json($response, $response["code"] ?? 200);
	}

	// Lifecycle

	function onInvalidAuthPassword($userId, $remainingAttempts)
	{
		if ($remainingAttempts <= 0) {
			$remainingAttempts = 0;
			$expiryDateTime = DateTime::getDateTime();
			$expiryDateTime = DateTime::addMinutes($expiryDateTime, 5);
			Lock::acquire("user-auth-$userId", $expiryDateTime);
			User::updateMaxAttempts($userId, 1);
		} else User::updateMaxAttempts($userId, $remainingAttempts);
	}

	function onSuccessfulAuth($userId)
	{
		User::updateMaxAttempts($userId, 5);
		Lock::release("user-auth-$userId");
		Cache::forget("storageRoleAccess");
		Session::forget("portalFirstInteractionTime");
	}

	// Util

	function validateAuthData($data)
	{
		$validationRules = [
			"email" => "required|email|max:255",
			"password" => "required|max:255"
		];

		$validator = Validator::make($data, $validationRules, [
			'email.required' => __('email-field-required'),
			'email.email' => __('email-field-invalid'),
			'password.required' => __('password-field-required')

		]);
		if ($validator->fails()) return ["status" => false, "msg" => $validator->errors()->all()[0]];

		return ["status" => true];
	}

	function formatAuthData($data)
	{
		return [
			"email" => $data["email"],
			"password" => $data["password"]
		];
	}

	function formatAuthOptions($options)
	{
		return [
			"skipPasswordVerification" => $options["skipPasswordVerification"] ?? false,
			"skipAuthSuccessfulBridge" => $options["skipAuthSuccessfulBridge"] ?? false,
			"skip2Fa" => $options["skip2Fa"] ?? false,
			"userIdToAuth" => $options["userIdToAuth"] ?? false
		];
	}

	function isUserAllowedToLogin($user)
	{
		if ($user === NULL) return ["status" => false, "msg" => __("invalid-cred-notification-description")];
		else if ($user->status === "inactive") return ["status" => false, "msg" => __("account-confirmation-notification-description")];
		else if ($user["status"] !== "active") return ["status" => false, "msg" => __("account-not-active-notification-description", ["status" => $user["status"]])];
		return ["status" => true];
	}

	function isUserLockedToAuth($userId)
	{

		$slug = "user-auth-$userId";
		$now = DateTime::getDateTime();

		if (!Lock::isExpired($slug)) {

			$lock = Lock::getLock($slug);
			$expiryDateTime = $lock["expiry_datetime"];
			$minutes = DateTime::diffInMinutes($now, $expiryDateTime);
			$minutes = ceil($minutes);
			if ($minutes == 0) $minutes = 1;

			return ["status" => true, "msg" => __('account-locked-notification-description', ['minutes' => $minutes])];
		}

		return ["status" => false];
	}

	function isUserPasswordCorrect($passwordStr, $hashPassword, $skipPasswordVerification = false)
	{
		if ($skipPasswordVerification === true) return ["status" => true];
		$passwordMatch = password_verify($passwordStr, $hashPassword);
		if ($passwordMatch) return ["status" => true];
		return ["status" => false, "msg" => __("invalid-cred-notification-description")];
	}

	function isRegistrationAllowed()
	{
		$settings = Cache::get("settings");
		if (!isset($settings["user-registration"]) || $settings["user-registration"]["column_value"] == 0) return false;
		return true;
	}

	function isRegisterableRole($role)
	{
		if ($role === "admin" || $role === "super admin" || empty($role)) return false;
		return true;
	}

	function canEnforce2FA($userId)
	{
		$twoFaController = new TwoFactorAuthController();
		$twoFactorAuthResponse = $twoFaController->get2Fa($userId);

		if (empty($twoFactorAuthResponse)) return ["status" => false, "msg" => "Error has occured in 2FA."];
		else if ($twoFactorAuthResponse["status"] !== "active") return ["status" => false, "msg" => "2FA is disabled."];
		return ["status" => true];
	}

	function successAuthBridge($user, $data)
	{
		return (new PluginBridgeController)->bridge("AUTH_SUCCESSFUL", [
			"user" => $user,
			"data" => $data
		]);
	}

	function generatePassword()
	{
		$uppercaseAlphabets = range('A', 'Z');
		$specialChars = ["!", "#", "&", "$"];

		$uppercaseChar = $uppercaseAlphabets[array_rand($uppercaseAlphabets)];
		$lowercaseChar = strtolower($uppercaseAlphabets[array_rand($uppercaseAlphabets)]);
		$specialChar = $specialChars[array_rand($specialChars)];

		$random = Str::random(8);
		$password = $random . $uppercaseChar . $lowercaseChar . $specialChar;

		return $password;
	}

	// Mails

	function sendAccountConfirmationMail($userId)
	{
		$user = User::getUserById($userId);
		if (empty($user)) return false;
		$user = $user->toArray();

		$settings = Cache::get("settings");
		$shortCodeController = new ShortCodeController();
		$mailTemplate = EmailTemplate::getEmailTemplateBySlug("registration");

		$code = Str::random(16);
		$code .= $userId;
		$code = strtolower($code);
		$verificationLink = config('app.url') . '/account/confirmation/' . $code;

		$mailTemplate = $shortCodeController->parseEmailTemplateShortCodes($mailTemplate, [
			"textShortCodes" => [
				"[user-name]" => Util::fullName($user["first_name"], $user["last_name"]),
				"[verification-link]" => $verificationLink
			]
		]);

		Action::addAction([
			"slug" => "ACCOUNT_CONFIRM",
			"uid" => $code,
			"status" => "pending",
			"data" => [
				"expiry" => DateTime::addMinutes(DateTime::getDateTime(), 1440),
				"userId" => $userId
			]
		]);

		$emailTemplate = "mails." . $settings["email-template"]["column_value"] . ".master";

		$mailDetails = [
			"template" => $emailTemplate,
			"subject" => $mailTemplate["subject"],
			"data" => $mailTemplate["content"],
			"signature" => $mailTemplate["signature"] ?? NULL
		];

		dispatch(new SendEmailJob($user['email'], $mailDetails))->onQueue("email");
		return true;
	}

	function sendAccountRegistrationPasswordMail($userId, $password, $other = [])
	{

		$user = User::getUserById($userId);
		if (empty($user)) return false;

		$settings = Cache::get("settings");
		$shortCodeController = new ShortCodeController();

		$textShortCodes = [
			"[user-name]" => Util::fullName($user["first_name"], $user["last_name"]),
			"[user-password]" => $password,
			"[user-email]" => $user['email']
		];

		foreach ($other["withPassword"]["headers"] ?? [] as $headerIndex => $header) {
			$headerSlug = Str::slug($header);
			$value = $other["withPassword"]["values"][$headerIndex];
			$code = "[$headerSlug]";
			$textShortCodes[$code] = $value;
		}

		$mailTemplate = EmailTemplate::getEmailTemplateBySlug("registration-with-details");
		$mailTemplate = $shortCodeController->parseEmailTemplateShortCodes($mailTemplate, [
			"textShortCodes" => $textShortCodes
		]);

		$emailTemplate = "mails." . $settings["email-template"]["column_value"] . ".master";

		$mailDetails = [
			"template" => $emailTemplate,
			"subject" => $mailTemplate["subject"],
			"data" => $mailTemplate["content"],
			"signature" => $mailTemplate["signature"] ?? NULL
		];

		dispatch(new SendEmailJob($user['email'], $mailDetails))->onQueue("email");
		return true;
	}

	function sendForgotPasswordMail($userId)
	{

		$user = User::getUserById($userId);
		if (empty($user)) return false;

		$settings = Cache::get("settings");
		$shortCodeController = new ShortCodeController();

		$userId = $user["id"];
		$code = Str::random(16);
		$code .= $userId;
		$code = strtolower($code);
		$resetPasswordLink = config('app.url') . '/portal/reset/' . $code;

		$mailTemplate = EmailTemplate::getEmailTemplateBySlug("forgot-password");
		$mailTemplate = $shortCodeController->parseEmailTemplateShortCodes($mailTemplate, [
			"textShortCodes" => [
				"[user-name]" => Util::fullName($user["first_name"], $user["last_name"]),
				"[reset-link]" => $resetPasswordLink
			]
		]);

		Action::addAction([
			"slug" => "FORGOT_PASSWORD",
			"uid" => $code,
			"status" => "pending",
			"data" => [
				"expiry" => DateTime::addMinutes(DateTime::getDateTime(), 1440),
				"userId" => $userId
			]
		]);

		$emailTemplate = "mails." . $settings["email-template"]["column_value"] . ".master";

		$mailDetails = [
			"template" => $emailTemplate,
			"subject" => $mailTemplate["subject"],
			"data" => $mailTemplate["content"],
			"signature" => $mailTemplate["signature"] ?? NULL,
		];

		dispatch(new SendEmailJob($user['email'], $mailDetails))->onQueue("email");
		return true;
	}


	/**
	 * User
	 */

	function users()
	{
		return User::getUsers();
	}

	function user($userId)
	{
		return User::getUserById($userId);
	}

	function addUser($data, $options = [])
	{

		$confirmationMail = $options["confirmationMail"] ?? true;
		$passwordMail = $options["passwordMail"] ?? false;

		$successHeading = __('add-account-notification-heading');
		$successMsg = $options["successMsg"] ?? __('add-account-notification-description');

		$oldUser = User::getUserByEmail($data["email"] ?? NULL);

		if (!empty($oldUser) && $oldUser->status === "inactive") {
			$this->sendAccountConfirmationMail($oldUser->id);
			return HTTP::inBoolArray(true, __('confirmation-required'), __('account-confirmation-notification-description'));
		} else if (!empty($oldUser)) {
			return HTTP::inBoolArray(false, __('registration-failed'), __('reg-account-already-exists-notification-description'));
		}

		if(empty($data['slug'])) $data['slug'] = Str::uuid()->toString();

		$validator = Validator::make($data, [
			"firstName" => "required|first_name",
			"lastName" => "last_name",
			"email" => "required|email|max:255",
			"password" => "required|same:confirmPassword|strong_password",
			"confirmPassword" => "required",
			"roleTitle" => "required",
			"phone" => "nullable|phone",
			"dob" => "nullable|date_format:Y-m-d",
			'slug' => 'required|regex:/^[a-zA-Z0-9\-:\/]+$/',
		], [
			"firstName.required" => __('first-name-field-required'),
			"email.required" => __('email-field-required'),
			"email.email" => __('email-field-invalid'),
			"dob.date_format" => __('dob-field-format')
		]);


		if ($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0], 422);
		}

		$user = User::addUser($data);
		$userId = $user->id ?? NULL;

		if (empty($userId)) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}

		if ($confirmationMail === true && $userId !== NULL) {
			$this->sendAccountConfirmationMail($user->id);
			$successHeading = __('action-required');
			$successMsg = __("account-confirmation-notification-description");
		}

		if ($passwordMail === true && $userId !== NULL) {
			$data["id"] = $userId;
			$rBridge = (new PluginBridgeController())->bridge("ADD_USER", $data);
			$this->sendAccountRegistrationPasswordMail($user->id, $data["password"], $rBridge["passwordMailOther"] ?? []);
		}

		return HTTP::inBoolArray(true, $successHeading, $successMsg, NULL, ['userId' => $userId]);
	}

	function updateUser($userId, $data, $options = [])
	{

		$skipDuplicateEmailCheck = $options["skipDuplicateEmailCheck"] ?? false;

		$validator = Validator::make($data, [
			"firstName" => "required|first_name",
			"lastName" => "last_name",
			"email" => "required|email|max:255",
			"password" => "nullable|strong_password",
			"phone" => "nullable|phone",
			"dob" => "nullable|date_format:Y-m-d",
			'slug' => 'required|regex:/^[a-zA-Z0-9\-:\/]+$/',
		], [
			"firstName.required" => __('first-name-field-required'),
			"email.required" => __('email-field-required'),
			"email.email" => __('email-field-invalid'),
			"dob.date_format" => __('dob-field-format')
		]);

		if ($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0], 422);
		}

		$user = User::getUserById($userId);
		if (empty($user)) {
			return HTTP::inBoolArray(false, __('request-failed'), __('account-not-found-notification-description'), 404);
		}

		$anotherUser = User::getUserByEmail($data["email"]);
		if ($skipDuplicateEmailCheck === false && !empty($anotherUser) && $anotherUser->id != $userId) {
			return HTTP::inBoolArray(false, __('request-failed'), __('reg-account-already-exists-notification-description'), 422);
		}

		$anotheSlugUser = User::getUserBySlug($data["slug"]);
		if (!empty($anotheSlugUser) && $anotheSlugUser->id != $userId) {
			return HTTP::inBoolArray(false, __('request-failed'), __('slug-field-exists'), 422);
		}

		$rUser = User::updateUser($userId, $data);
		if (empty($rUser->id)) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), $rUser["msg"], 422);
		}

		return HTTP::inBoolArray(true, __('update-notification-heading'), __('update-notification-description'));
	}

	function deleteUser($userId)
	{
		$admins = User::getUsersByRole("admin");
		$deleteUser = User::getUserById($userId);

		if ($deleteUser !== null && $deleteUser["role_title"] === "admin" && count($admins) <= 1) {
			return HTTP::inBoolArray(false, __('request-failed'), __("at-least-one-admin-required-in-system-notification-description"), 404);
		}
		else if (empty($deleteUser)) {
			return HTTP::inBoolArray(false, __('request-failed'), __('account-not-found-notification-description'), 404);
		}

		User::deleteUser($userId);

		return HTTP::inBoolArray(true, __('delete-notification-heading'), __('delete-notification-description'));
	}

	function updateUserProfilePicture($userId, $url)
	{
		$rUser = User::updateImage($userId, $url);
		if (empty($rUser->id)) return ["status" => "fail", "msg" => $rUser["msg"] ?? __('error-notification-description'), "code" => 422];

		return HTTP::inBoolArray(true, __('update-notification-heading'), __('profile-picture-update-notification-description'));
	}

	function updateUserPassword($userId, $password, $confirmPassword)
	{
		$data = [
			"userId" => $userId,
			"password" => $password,
			"confirmPassword" => $confirmPassword
		];

		$validator = validator::make($data, [
			"userId" => "required|numeric",
			"password" => "required|same:confirmPassword|strong_password",
			"confirmPassword" => "required"
		], [
			'password.required' => __('password-field-required'),
			'password.same' => __('password-fields-same'),
			'confirmPassword.required' => __('confirm-password-field-required'),
		]);

		if ($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}

		$rUser = User::updatePassword($userId, $password);

		if (($rUser['status'] ?? false) === false) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), $rUser["msg"], 422);
		}

		return HTTP::inBoolArray(true, __('password-update-notification-heading'), __('password-update-notification-description'));
	}

	function updateUserRoleAndStatus($userId, $role, $status)
	{

		$data = [
			"userId" => $userId,
			"role" => $role,
			"status" => $status
		];

		$validator = validator::make($data, [
			"userId" => "required|numeric",
			"role" => "required|exists:roles,title",
			"status" => "required|in:active,inactive,banned"
		]);

		if ($validator->fails()) return ["status" => "fail", "msg" => $validator->errors()->all()[0]];

		User::updateRoleAndStatus($userId, $role, $status);
		
		return HTTP::inBoolArray(true, __('update-notification-heading'), __('update-notification-description'));
	}

	function updateUserAbout($userId, $about){
		User::updateAbout($userId, $about);
		return HTTP::inBoolArray(true, __('update-notification-heading'), __('update-notification-description'));
	}

	function updateUserAddress($userId, $requestData){
		UserAddress::saveAddress($userId, $requestData);
		return HTTP::inBoolArray(true, __('update-notification-heading'), __('update-notification-description'));
	}

	function updateUserAdditional($userId, $requestData){
		UserDetail::updateDetails($userId, $requestData);
		return HTTP::inBoolArray(true, __('update-notification-heading'), __('update-notification-description'));
	}

	function revokeUserAbilities($abilities, $user = NULL)
	{

		if ($user === NULL) {
			$user = request()->user();
			if($user !== NULL) $token = $user->currentAccessToken();
		}		

		if ($user === NULL) {
			$user = request()["loggedInUser"] ?? NULL;
			$token = $user->token;
			
		} else if ($user !== NULL && $user->token !== NULL) $token = $user->currentAccessToken();
		else if ($user !== NULl) {
			if($user->token !== NULL) $token = $user->token;
		} else return;

		$tokenAbilities = $token->abilities;

		$newAbilities = array_diff($tokenAbilities, $abilities);

		$token->update(["abilities" => $newAbilities]);
	}

	function assignUserAbilities($abilities, $user = NULL)
	{
		if ($user === NULL) {
			$user = request()->user();
			$token = NULL;
		}

		if ($user === NULL) {
			$user = request()["loggedInUser"] ?? NULL;
			$token = $user->token;
		} else if ($user !== NULL) $token = $user->currentAccessToken();
		else return;

		$tokenAbilities = $token->abilities;

		$newAbilities = [...$tokenAbilities, ...$abilities];
		$token->update(["abilities" => $newAbilities]);
	}


	//  Requests

	function saveUserRequest(Request $request, $userId = NULL)
	{
		$data = $request->post();
		$response = [];

		if (empty($userId)) {
			$data["confirmPassword"] = $data["confirmPassword"] ?? ($data["password"] ?? NULL);
			$response = $this->addUser($data, [
				"confirmationMail" => false,
				"passwordMail" => true,
				"successMsg" => __("account-saved-notification")
			]);
		} else $response = $this->updateUser($userId, $data);

		return HTTP::inStringResponse($response);
	}

	function deleteUserRequest($userId)
	{
		$response = $this->deleteUser($userId);
		return HTTP::inStringResponse($response);
	}

	function userProfileInformationUpdateRequest(Request $request)
	{

		$userId = $request->user()->id;
		$data = $request->post();

		$response = $this->updateUser($userId, $data);
		return HTTP::inStringResponse($response);
	}

	function userProfilePictureUpdateRequest(Request $request)
	{
		$userId = $request->user()->id;
		$data = $request->post();

		$response = $this->updateUserProfilePicture($userId, $data["url"] ?? NULL);
		return HTTP::inStringResponse($response);
	}

	function userPasswordUpdateRequest(Request $request)
	{
		$userId = $request->user()->id;
		$data = $request->post();

		$response = $this->updateUserPassword($userId, $data['password'] ?? NULL, $data['confirmPassword'] ?? NULL);
		return HTTP::inStringResponse($response);
	}

	function userRoleAndStatusUpdateRequest(Request $request)
	{
		$userId = $request->user()->id;
		$data = $request->post();

		$response = $this->updateUserRoleAndStatus($userId, $data['role'] ?? NULL, $data['status'] ?? NULL);
		return HTTP::inStringResponse($response);
	}

	function userAboutUpdateRequest(Request $request){
		$userId = $request->user()->id;
		$data = $request->post();

		$response = $this->updateUserAbout($userId, $data['about'] ?? NULL);
		return HTTP::inStringResponse($response);
	}

	function userAddressUpdateRequest(Request $request){
		$userId = $request->user()->id;
		$data = $request->post();

		$response = $this->updateUserAddress($userId, $data ?? NULL);
		return HTTP::inStringResponse($response);
	}	

	function userAdditionalUpdateRequest(Request $request){
		$userId = $request->user()->id;
		$data = $request->post();

		$response = $this->updateUserAdditional($userId, $data ?? []);
		return HTTP::inStringResponse($response);
	}


	/**
	 * User Setting
	 */

	function userSettings($userId)
	{
		return UserSetting::getUserSettings($userId);
	}

	function userSaveSettings($userId, array $data)
	{
		foreach ($data as $column => $value) {
			UserSetting::saveUserSetting($column, $value, $userId);
		}
		return ["status" => "success", "msg" => "User settings are saved."];
	}

	// Request

	function userSettingsRequest(Request $request)
	{
		$userId = $request->user()->id;
		return $this->userSettings($userId);
	}

	function userSaveSettingsRequest(Request $request)
	{
		$userId = $request->user()->id;
		$data = $request->post();
		$response = $this->userSaveSettings($userId, $data);
		return response()->json($response, $response["code"] ?? 200);
	}
}
