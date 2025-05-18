<?php

namespace App\Plugins\Setting\Controller;

use App\Classes\DateTime;
use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ShortCodeController;
use App\Jobs\SendEmailJob;
use App\Models\Action;
use App\Models\User;
use App\Plugins\EmailTemplate\Model\EmailTemplate;
use App\Plugins\Setting\Model\TwoFactorAuth;
use App\Plugins\UserManager\Controller\UserManagerController;
use HTTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use User as HelperUser;

class TwoFactorAuthController extends Controller
{

	/**
	 * 2FA: Get
	 */

	function get2Fa($userId)
	{
		return TwoFactorAuth::get2FaForUser($userId);
	}

	/**
	 * 2FA: Save
	 */

	function save2Fa($userId, $data)
	{

		$validator = Validator::make($data, [
			"status" => "required"
		]);

		if ($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}

		TwoFactorAuth::change2FaStatus($userId, $data["status"]);
		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	function send2FaMail($userId)
	{
		$settings = Cache::get("settings");
		$shortCodeController = new ShortCodeController();

		$user = User::getUserById($userId);

		if (empty($user)) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}

		$uid = Str::uuid();
		$code = strtoupper(Str::random(5));
		$link = url("/portal/2fa-verify/" . $uid);

		Action::addAction([
			"slug" => "2FA",
			"uid" => $uid,
			"status" => "pending",
			"data" => [
				"userId" => $userId,
				"code" => $code
			]
		]);

		$mailTemplate = EmailTemplate::getEmailTemplateBySlug("2fa-confirmation");
		$mailTemplate = $shortCodeController->parseEmailTemplateShortCodes($mailTemplate, [
			"textShortCodes" => [
				"[user-name]" => Util::fullName($user["first_name"], $user["last_name	"]),
				"[verification-code]" => $code,
				"[verification-link]" => $link
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

		return HTTP::inBoolArray(true, __('2fa-notification-heading'), __('2fa-notification-description'), NULL, ['redirect' => $link]);
	}

	function verifyCodeByUid($uid, $code)
	{

		$actionData = Action::getActionByUid($uid);
		if ($actionData === NULL) {
			return HTTP::inBoolArray(false, __('2fa-invalid-code-notification-heading'), __('2fa-invalid-code-notification-description'));
		}

		$actionData = json_decode($actionData, true);

		if ($actionData["status"] === "complete") {
			return HTTP::inBoolArray(false, __('2fa-expired-code-notification-heading'), __('2fa-expired-code-notification-description'));
		}

		$actionDateTime = $actionData["create_datetime"];
		$userId = $actionData["data"]["userId"];
		$dbCode = $actionData["data"]["code"];

		if ($dbCode != $code) {
			return HTTP::inBoolArray(false, __('2fa-invalid-code-notification-heading'), __('2fa-invalid-code-notification-description'));
		}

		$expirtyDateTime = DateTime::addMinutes($actionDateTime, 10);

		$now = DateTime::getDateTime();
		$isExpired = DateTime::dateTimeLessThan($expirtyDateTime, $now);

		if ($isExpired === true) {
			return HTTP::inBoolArray(false, __('2fa-invalid-code-notification-heading'), __('2fa-invalid-code-notification-description'));
		}

		$user = HelperUser::user($userId);
	
		
		(new UserManagerController())->auth([
			'email'=>$user['email'],
			'password'=>'dummy'
		],[
			'skipPasswordVerification'=>true,
			'skip2Fa'=>true,
			'userIdToAuth'=>$userId
		]);

		Action::updateStatusByUid($uid, "complete");

		return HTTP::inBoolArray(true, __('2fa-verified-code-notification-heading'), __('2fa-verified-code-notification-description'), 200, ['redirect'=>url('/portal/dashboard')]);
	}

	// Request

	function save2FaRequest(Request $request)
	{
		$userId = $request->user()->id;
		$data = $request->post();
		$response = $this->save2Fa($userId, $data);
		return HTTP::inStringResponse($response);
	}

	function getUser2FaRequest(Request $request)
	{
		$userId = $request->user()->id;
		$response = $this->get2Fa($userId);
		return HTTP::inStringResponse($response);
	}

	function verifyCodeRequest(Request $request)
	{
		$data = $request->post();
		$response = $this->verifyCodeByUid($data["uid"] ?? "", $data["code"] ?? "");
		return HTTP::inStringResponse($response);
	}
}
