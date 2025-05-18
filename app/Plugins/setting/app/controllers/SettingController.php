<?php

namespace App\Plugins\Setting\Controller;

use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Http\Controllers\ShortCodeController;
use App\Mail\GenericMail;
use App\Plugins\EmailTemplate\Model\EmailTemplate;
use App\Plugins\Setting\Model\Setting;
use Exception;
use HTTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{

	function generalSettingsView()
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => __('general settings'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('general settings'),
			"pageSlug" => "general-settings",
			"pluginConfig" => $config
		];

		return PluginController::loadView(__DIR__, "general-settings.blade.php", $pageData);
	}

	function smtpView()
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$settings = Setting::getSettings();

		$pageData = [
			"tabTitle" => __('configure smtp'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('configure smtp'),
			"pageSlug" => "smtp",
			"pluginConfig" => $config,
			"settings" => $settings
		];

		return PluginController::loadView(__DIR__, "smtp.blade.php", $pageData);
	}

	function globalScriptsView()
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$settings = Setting::getSettings();

		$pageData = [
			"tabTitle" => __('global scripts'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('global scripts'),
			"pageSlug" => "global-scripts",
			"pluginConfig" => $config,
			"settings" => $settings
		];

		return PluginController::loadView(__DIR__, "global-scripts.blade.php", $pageData);
	}

	/**
	 * Settings: Get
	 */

	function getSettings()
	{
		return Setting::getSettings();
	}

	/**
	 * Settings: Save
	 */

	function updateSMTP($data)
	{

		$validator = Validator::make($data, [
			"host" => "required",
			"port" => "required",
			"encryption" => "required",
			"email" => "required",
			"password" => "required",
			"fromName" => "required"
		]);

		if ($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}

		Setting::saveSetting("smtp-host", $data["host"]);
		Setting::saveSetting("smtp-port", $data["port"]);
		Setting::saveSetting("smtp-encryption", $data["encryption"]);
		Setting::saveSetting("smtp-email", $data["email"]);
		Setting::saveSetting("smtp-password", $data["password"]);
		Setting::saveSetting("smtp-from-name", $data["fromName"]);
		Setting::saveSetting("smtp-domain", $data["domain"] ?? NULL);

		return HTTP::inBoolArray(true, __('update-notification-heading'), __('update-notification-description'));
	}

	function updateGlobalScripts($data)
	{
		$head = $data["head"] ?? "";
		$foot = $data["foot"] ?? "";

		Setting::saveSetting("global-scripts-head", $head);
		Setting::saveSetting("global-scripts-foot", $foot);

		return HTTP::inBoolArray(true, __('update-notification-heading'), __('update-notification-description'));
	}

	// Email

	function sendMail($data)
	{

		$settings = Cache::get("settings");
		$shortCodeController = new ShortCodeController();

		$validator = Validator::make($data, [
			"email" => "required",
			"subject" => "required",
			"msg" => "required"
		]);

		if ($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}

		$mailTemplate = EmailTemplate::getEmailTemplateBySlug("test");
		$mailTemplate = $shortCodeController->parseEmailTemplateShortCodes($mailTemplate);

		$emailTemplate = "mails." . $settings["email-template"]["column_value"] . ".master";
		$mailTemplate["content"] = $data["msg"];

		$mailDetails = [
			"template" => $emailTemplate,
			"subject" => $data["subject"],
			"data" => $mailTemplate["content"],
			"signature" => $mailTemplate["signature"] ?? NULL,
		];

		try {
			Mail::to($data["email"])->send(new GenericMail($mailDetails));
		} catch (Exception $e) {
			return HTTP::inBoolArray(false, __('request-failed'), $e->getMessage());
		}

		return HTTP::inBoolArray(true, __('email-sent-notification-heading'), __('email-sent-notification-description'));
	}

	// Request

	function updateSettingsRequest(Request $request)
	{
		$data = $request->post();
		Setting::saveSetting($data["name"], $data["value"]);
		return HTTP::inStringResponse(HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description')));
	}

	function updateSMTPRequest(Request $request)
	{
		$data = $request->post();
		$response = $this->updateSMTP($data);
		return HTTP::inStringResponse($response);
	}

	function testSMTPRequest(Request $request)
	{
		$data = $request->post();
		$response = $this->sendMail($data);
		return HTTP::inStringResponse($response);
	}

	function updateGlobalScriptsRequest(Request $request)
	{
		$data = $request->post();
		$response = $this->updateGlobalScripts($data);
		return HTTP::inStringResponse($response);
	}
}
