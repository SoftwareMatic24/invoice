<?php

namespace App\Plugins\PaymentMethods\Controller;


use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Plugins\PaymentMethods\Model\PaymentMethod;
use App\Plugins\PaymentMethods\Model\PaymentMethodIdentifier;
use App\Plugins\PaymentMethods\Model\SystemPaymentMethod;
use App\Plugins\PaymentMethods\Model\UserPaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use User;
use HTTP;

class PaymentMethodController extends Controller
{

	function paymentMethodsView()
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$settings = Cache::get("settings");
		$paymentMethods = PaymentMethod::paymentMethods();

		$pageData = [
			"tabTitle" => __('payment methods'),
			"backURL" => Util::prefixedURL("/dashboard"),
			"pageName" => __('payment methods'),
			"pageSlug" => "payment-methods",
			"pluginConfig" => $config,
			"settings" => $settings,
			"paymentMethods" => $paymentMethods
		];

		return PluginController::loadView(__DIR__, "payment-methods.blade.php", $pageData);
	}

	function paymentMethodEntriesView(Request $request, $type, $paymentMethodSlug)
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$settings = Cache::get("settings");
		$userId = $request["loggedInUser"]["id"] ?? NULL;

		$paymentMethodName = ucwords(Str::slug($paymentMethodSlug, " "));
		$paymentMethodEntries = [];

		$canAccessSystem = User::authUserCan($_COOKIE, "system-payment-method");

		if ($type === "system" && $canAccessSystem === true) $paymentMethodEntries = SystemPaymentMethod::getSystemPaymentMethodByPaymentMethodSlug($paymentMethodSlug)->toArray();
		else if ($type === "user") $paymentMethodEntries = UserPaymentMethod::getUserPaymentMethodByUserIdPaymentMethodSlug($userId, $paymentMethodSlug)->toArray();

		$pageData = [
			"tabTitle" => $paymentMethodName . " " . __('payment methods'),
			"backURL" => Util::prefixedURL("/setting/payment-methods"),
			"pageName" => $paymentMethodName . " " . __('payment methods'),
			"pageSlug" => "payment-methods",
			"pluginConfig" => $config,
			"settings" => $settings,
			"paymentMethodEntries" => $paymentMethodEntries,
			"paymentMethodSlug" => $paymentMethodSlug,
			"type" => $type
		];

		return PluginController::loadView(__DIR__, "payment-method-entries.blade.php", $pageData);
	}

	function savePaymentMethodEntryView(Request $request, $type, $paymentMethodSlug, $entryId = NULL)
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$settings = Cache::get("settings");
		$userId = $request["loggedInUser"]["id"] ?? NULL;

		$identifiers = PaymentMethodIdentifier::getIdentifiers();

		$paymentMethodName = ucwords(Str::slug($paymentMethodSlug, " "));
		$entry = NULL;

		$canAccessSystem = User::authUserCan($_COOKIE, "system-payment-method");

		if ($type === "system" && $entryId !== NULL && $canAccessSystem === true) $entry = SystemPaymentMethod::getPaymentMethod($entryId)->toArray();
		else if ($type === "user" && $entryId !== NULL) $entry = UserPaymentMethod::getPaymentMethod($entryId)->toArray();


		if ($entry !== NULL) $entry["other"] = json_decode($entry["other"] ?? "[]", true);

		$page = NULL;
		if ($paymentMethodSlug === "cod") $page = "cod.blade.php";
		else if ($paymentMethodSlug === "bank-transfer") $page = "bank-transfer.blade.php";
		else if ($paymentMethodSlug === "paypal") $page = "paypal.blade.php";
		else if ($paymentMethodSlug === "stripe") $page = "stripe.blade.php";

		$pageData = [
			"tabTitle" => $paymentMethodName . ' ' . __('payment method'),
			"backURL" => Util::prefixedURL("/payment-method/methods/$type/$paymentMethodSlug"),
			"pageName" => $paymentMethodName . ' ' . __('payment method'),
			"pageSlug" => "payment-methods",
			"pluginConfig" => $config,
			"settings" => $settings,
			"entry" => $entry,
			"paymentMethodSlug" => $paymentMethodSlug,
			"type" => $type,
			"entryId" => $entryId,
			"identifiers" => $identifiers
		];

		return PluginController::loadView(__DIR__, $page, $pageData);
	}

	/**
	 * Payment Method: Get
	 */

	function safeSystemPaymentMethod($identifier, $paymentMethodSlug)
	{
		$entry = SystemPaymentMethod::getPaymentMethodByIdentifierAndSlug($identifier, $paymentMethodSlug);
		if ($entry === NULL) return;
		$entry = $entry->toArray();
		unset($entry["private_key"]);
		return $entry;
	}

	function paymentMethods()
	{
		return PaymentMethod::paymentMethods();
	}

	function systemPaymentMethod($paymentMethodSlug)
	{
		return SystemPaymentMethod::getSystemPaymentMethodByPaymentMethodSlug($paymentMethodSlug);
	}

	// Request

	function systemPaymentMethodRequest($paymentMethodSlug)
	{
		return $this->systemPaymentMethod($paymentMethodSlug);
	}

	function systemPaymentMethodsRequest($paymentMethodSlug)
	{
		return SystemPaymentMethod::getSystemPaymentMethodByPaymentMethodSlug($paymentMethodSlug);
	}

	/**
	 * Payment Method: Save
	 */

	function savePaymentMethod($userId, $data)
	{
		$validator = Validator::make($data, [
			"paymentMethod" => "required"
		]);

		if ($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}

		$paymentMethod = $data["paymentMethod"];
		unset($data["paymentMethod"]);

		PaymentMethod::savePaymentMethod($userId, $paymentMethod, $data);

		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	function saveSystemPaymentMethod($entryId, $data)
	{

		if (($data["paymentMethodSlug"] ?? NULL) === "stripe" || ($data["paymentMethodSlug"] ?? NULL) === "paypal") {
			$validator = Validator::make($data, [
				"name" => "required|max:255",
				"email" => "nullable|max:255|email",
				"status" => "nullable|in:active,inactive",
				"identifier" => "required|max:255",
				"paymentMethodSlug" => "required|max:255"
			]);

			if ($validator->fails()) {
				return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
			}
		}

		$row = SystemPaymentMethod::getPaymentMethodByIdentifierAndSlug($data["identifier"], $data["paymentMethodSlug"]);

		if ($row !== NULL && $entryId == NULL) {
			return HTTP::inBoolArray(false, __('request-failed'), __('details-already-exist-for') . ' ' . ucwords($data["paymentMethodSlug"]) . ' ' . Str::slug($data["identifier"]));
		}

		$entryId = SystemPaymentMethod::savePaymentMethod($entryId, $data);

		if ($entryId === NULL) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}

		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	// Request

	function savePaymentMethodRequest(Request $request)
	{
		$data = $request->post();
		$userId = $request->user()->id;
		$response = $this->savePaymentMethod($userId, $data);
		return HTTP::inStringResponse($response);
	}

	function saveSystemPaymentMethodRequest(Request $request, $entryId = NULL)
	{
		$data = $request->post();
		$response = $this->saveSystemPaymentMethod($entryId, $data);
		return HTTP::inStringResponse($response);
	}

	/**
	 * Payment Method: Delete
	 */

	function deleteSystemPaymentMethodEntry($entryId)
	{
		SystemPaymentMethod::deletePaymentMethod($entryId);
		return HTTP::inBoolArray(true, __('delete-notification-heading'), __('delete-notification-description'));
	}

	function deleteSystemPaymentMethodEntryRequest($entryId)
	{
		$response = $this->deleteSystemPaymentMethodEntry($entryId);
		return HTTP::inStringResponse($response);
	}


	/**
	 * User Payment Method: Get
	 */

	function userPaymentsMethod($userId, $paymentMethodSlug)
	{
		return UserPaymentMethod::getUserPaymentMethodByUserIdPaymentMethodSlug($userId, $paymentMethodSlug);
	}

	// Request

	function userPaymentMethodsRequest(Request $request, $paymentMethodSlug)
	{
		$userId = $request->user()->id;
		return $this->userPaymentsMethod($userId, $paymentMethodSlug);
	}

	function myPaymentMethodRequest(Request $request, $paymentMethod)
	{
		$userId = $request->user()->id;
		return $this->getUserPaymentMethod($userId, $paymentMethod);
	}

	/**
	 * User Payment Method: Save
	 */

	function saveUserPaymentMethod($userId, $entryId, $data)
	{
		$entryId = UserPaymentMethod::saveUserPaymentMethod($userId, $entryId, $data);

		if (empty($entryId)) {
			return HTTP::inBoolArray(false, __('error-notification-heading'), __('error-notification-description'));
		}

		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	function saveUserPaymentMethodRequest(Request $request, $entryId = NULL)
	{
		$data = $request->post();
		$userId = $request->user()->id;
		$response = $this->saveUserPaymentMethod($userId, $entryId, $data);
		return HTTP::inStringResponse($response);
	}

	/**
	 * User Payment Method: Delete
	 */

	function deleteUserPaymentMethodEntry($entryId)
	{
		UserPaymentMethod::deletePaymentMethod($entryId);
		return HTTP::inBoolArray(true, __('delete-notification-heading'), __('delete-notification-description'));
	}

	function deleteUserPaymentMethodEntryRequest($entryId)
	{
		$response = $this->deleteUserPaymentMethodEntry($entryId);
		return HTTP::inStringResponse($response);
	}
}
