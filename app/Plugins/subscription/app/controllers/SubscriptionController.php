<?php

namespace App\Plugins\Subscription\Controller;

use App\Classes\DateTime;
use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Plugins\Currency\Model\Currency;
use App\Plugins\Ecommerce\Model\EcommerceSetting;
use App\Plugins\PaymentMethods\Controller\PaymentController;
use App\Plugins\PaymentMethods\Model\SystemPaymentMethod;
use App\Plugins\Subscription\Model\SubscriptionPackage;
use App\Plugins\Subscription\Model\SubscriptionPackageClassification;
use App\Plugins\Subscription\Model\SubscriptionPackagePluginLimit;
use App\Plugins\Subscription\Model\SubscriptionSubscribers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;
use HTTP;

class SubscriptionController extends Controller
{

	function subscriptionPackagesView()
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => __('subscription packages'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('subscription packages'),
			"pageSlug" => "subscription-manage-packages",
			"pluginConfig" => $config,
		];

		return PluginController::loadView(__DIR__, "packages.blade.php", $pageData);
	}

	function saveSubscriptionPackageView($subscriptionPackageId = NULL)
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$activePlugins = Cache::get("activePlugins");

		$limits = SubscriptionPackagePluginLimit::getActivePluginLimits()->toArray();

		$formattedLimits = [];

		foreach ($limits as $key => $row) {
			$pluginSlug = $row["plugin"]["slug"];
			if (!isset($formattedLimits[$pluginSlug])) $formattedLimits[$pluginSlug] = [];
			$formattedLimits[$pluginSlug][] = [
				"label" => $row["label"],
				"slug" => $row["limit_slug"],
			];
		}

		$pluginLimitKeys = array_keys($formattedLimits);
		$limits = [];

		foreach ($activePlugins as $plugin) {
			$pluginSlug = $plugin["slug"] ?? NULL;
			if (in_array($pluginSlug, $pluginLimitKeys))  $limits =  array_merge($limits, $formattedLimits[$pluginSlug]);
		}

		$pageData = [
			"tabTitle" =>  empty($subscriptionPackageId) ? __('new subscription package') : __('update subscription package'),
			"backURL" => Util::prefixedURL("/subscription/manage"),
			"pageName" => empty($subscriptionPackageId) ? __('new subscription package') : __('update subscription package'),
			"pageSlug" => "subscription-save-package",
			"pluginConfig" => $config,
			"subscriptionPackageId" => $subscriptionPackageId,
			"limits" => $limits,
		];

		return PluginController::loadView(__DIR__, "save-package.blade.php", $pageData);
	}

	function classificationsView()
	{

		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => __('subscription package classification'),
			"backURL" => url("/portal/dashboard"),
			"pageName" => __('subscription package classification'),
			"pageSlug" => "subscription-classifications",
			"pluginConfig" => $config
		];

		return PluginController::loadView(__DIR__, "classifications.blade.php", $pageData);
	}

	function saveClassificationView($slug = NULL)
	{

		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => empty($slug) ? __('new classification') : __('update classification'),
			"backURL" => url('/portal/subscription/classifications'),
			"pageName" => empty($slug) ? __('new classification') : __('update classification'),
			"pageSlug" => "subscription-classifications",
			"pluginConfig" => $config,
			'slug' => $slug
		];

		return PluginController::loadView(__DIR__, "save-classification.blade.php", $pageData);
	}

	function subscribersView()
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => __('package subscribers'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('package subscribers'),
			"pageSlug" => "subscription-subscribers",
			"pluginConfig" => $config,
		];

		return PluginController::loadView(__DIR__, "subscribers.blade.php", $pageData);
	}

	function saveSubscriberView($subscriberUserId = NULL)
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => empty($subscriberUserId) ? __('new subscriber') : __('update subscriber'),
			"backURL" => url("/portal/subscription/subscribers"),
			"pageName" => empty($subscriberUserId) ? __('new subscriber') : __('update subscriber'),
			"pageSlug" => "subscription-subscribers",
			"pluginConfig" => $config,
			"subscriberUserId" => $subscriberUserId
		];

		return PluginController::loadView(__DIR__, "save-subscriber.blade.php", $pageData);
	}

	function userPackageUpdateView()
	{
		$config = PluginController::getPluginConfig(__DIR__);
		$primaryCurrency = Currency::getPrimaryCurrency();

		$pageData = [
			"tabTitle" => __('your subscription package'),
			"pageName" => __('your subscription package'),
			"pageSlug" => "user-subscription-package",
			"pluginConfig" => $config,
			"primaryCurrencySymbol" => $primaryCurrency->symbol
		];

		return PluginController::loadView(__DIR__, "user-package-update.blade.php", $pageData);
	}


	/**
	 * Packages: Get
	 */

	function packages()
	{
		return SubscriptionPackage::getPackages();
	}

	function activePackages()
	{
		return SubscriptionPackage::getPackagesByStatus("active");
	}

	function package($subscriptionPackageId = NULL)
	{
		return SubscriptionPackage::getPackage($subscriptionPackageId);
	}

	/**
	 * Package: Save
	 */

	function savePackage($userId = NULL, $subscriptionPackageId = NULL, $data)
	{
		$errorMessage = NULL;

		$validator = Validator::make($data, [
			"title" => "required|max:255",
			"price" => "required|numeric",
			"classificationId" => "required",
			"status"=>"required|in:active,inactive"
		], [
			'title.required' => __('title-field-required'),
			'price.required' => __('price-field-required'),
			'price.numeric' => __('price-field-numeric'),
			'status.required'=>__('status-field-required')
		]);

		if ($validator->fails()) return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);

		$limits = $data["limits"] ?? [];

		foreach ($limits as $limit) {
			if (!is_numeric($limit["value"]) && $limit["value"] !== "" && $limit["value"] !== NULL) {
				$errorMessage = __('package-limit-numeric');
			}
		}

		if ($errorMessage !== NULL) return HTTP::inBoolArray(false, __('request-failed'), $errorMessage);

		if ($subscriptionPackageId === NULL) {
			$package = SubscriptionPackage::addPackage($userId, $data);
			$subscriptionPackageId = $package["id"] ?? NULL;
		} else SubscriptionPackage::updatePackage($subscriptionPackageId, $data);

		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	// Request

	function savePackageRequest(Request $request, $subscriptionPackageId = NULL)
	{
		$userId = $request->user()->id;
		$data = $request->post();
		$inBoolArray = $this->savePackage($userId, $subscriptionPackageId, $data);
		return HTTP::inStringResponse($inBoolArray);
	}


	/**
	 * Package: Delete
	 */

	function deletePackage($subscriptionPackageId = NULL)
	{
		SubscriptionPackage::deletePackage($subscriptionPackageId);
		return HTTP::inBoolArray(true, __('delete-notification-heading'), __('delete-notification-description'));
	}

	// Request

	function deletePackageRequest($subscriptionPackageId)
	{
		$response = $this->deletePackage($subscriptionPackageId);
		return HTTP::inStringResponse($response);
	}

	/**
	 * Classifications: Get
	 */

	function classifications()
	{
		return SubscriptionPackageClassification::classifications();
	}

	/**
	 * Classification: Save
	 */

	function saveClassification($slug, $name)
	{

		if (!$this->isActionAllowedOnClassification($slug)) {
			return HTTP::inBoolArray(false, __('request-failed'), __('default-classifiction-not-updatable'));
		} else if (empty($name)) {
			return HTTP::inBoolArray(false, __('action-required'), __('name-field-required'));
		}

		$newSlug = Str::slug($name);

		try {
			SubscriptionPackageClassification::saveClassification($name, $slug, $newSlug);
			return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
		} catch (Exception $e) {
			$error = MySQLExceptionMessage($e->getCode(), 'Name');
			return HTTP::inBoolArray(false, $error['heading'], $error['description']);
		}
	}


	//  Request

	function saveClassificationRequest(Request $request, $slug = NULL)
	{
		$data = $request->post();
		$inBoolResponse = $this->saveClassification($slug, $data['name'] ?? NULL);
		return HTTP::inStringResponse($inBoolResponse);
	}

	/**
	 * Classifcaitions: Delete
	 */

	function deleteClassificationBySlug($slug)
	{
		if (!$this->isActionAllowedOnClassification($slug)) {
			return HTTP::inBoolArray(false, __('request-failed'), __('default-classifiction-not-deletable'));
		}
		SubscriptionPackageClassification::deleteClassificationBySlug($slug);

		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	// Util

	function isActionAllowedOnClassification($slug)
	{
		if ($slug === 'default') return false;
		return true;
	}

	// Request

	function deleteClassificationBySlugRequest($slug)
	{
		$inBoolResponse = $this->deleteClassificationBySlug($slug);
		return HTTP::inStringResponse($inBoolResponse);
	}

	/**
	 * Subscribers: Get
	 */

	function subscribers()
	{
		return SubscriptionSubscribers::getSubscribers();
	}

	function subscriber($userId)
	{
		return SubscriptionSubscribers::getSubscriber($userId);
	}

	// Util

	function isSubscriptionValid($userId)
	{

		// function chargeSellersBySubscription()
		// {
		// 	$ecommerceSettings = EcommerceSetting::getSettings();
		// 	$ecommerceSettings = $ecommerceSettings->toArray();
		// 	if ($ecommerceSettings["charge-sellers"]["column_value"] !== "subscription" && $ecommerceSettings["charge-sellers"]["column_value"] !== "sale-and-subscription") return false;
		// 	return true;
		// }

		function hasSubscription($userId)
		{
			$subscription = SubscriptionSubscribers::getSubscriber($userId);
			if ($subscription === NULL) return false;
			return $subscription->toArray();
		}

		function isPackageActive($subscription)
		{
			$package = $subscription["subscription_package"];
			return $package["status"] === "active" ? true : false;
		}

		function isPackageExpired($subscription)
		{
			$now = DateTime::getDateTime();
			$expiry = $subscription["expiry_datetime"];
			if(empty($expiry)) return false;
			return !DateTime::dateTimeLessThan($now, $expiry);
		}

		//if (!chargeSellersBySubscription()) return true;
		$subscription = hasSubscription($userId);
		if ($subscription === false) return false;
		if (!isPackageActive($subscription)) return false;
		if (isPackageExpired($subscription)) return false;

		return true;
	}

	// Request

	function userSubscriptionRequest(Request $request)
	{
		$userId = $request->user()->id;
		return self::subscriber($userId);
	}

	/**
	 * Subscribers: Save
	 */

	function addSubscriber($userId, $packageId, $transactionId = NULL, $disable = false, $expiryDateTime = NULL)
	{
		$now = DateTime::getDateTime();
		if (empty($expiryDateTime)) $expiryDateTime = DateTime::addMonths($now, 1);
		return SubscriptionSubscribers::saveSubscriber($packageId, $transactionId, $userId, $expiryDateTime, $disable);
	}

	function subscribe($userId, $subscriptionPackageId, $paymentMethodSlug = NULL)
	{
		$paymentMethodIdentifier = "default";
		$paymentController = new PaymentController();
		$paymentMethod = SystemPaymentMethod::getPaymentMethodByIdentifierAndSlug($paymentMethodIdentifier, $paymentMethodSlug);

		$package = SubscriptionPackage::getPackage($subscriptionPackageId);
		if ($package === NULL || $paymentMethod === NULL) die(__('admin-payment-method-not-setup'));
		if ($paymentMethod["status"] !== "active") die($paymentMethodSlug . " is not available.");

		$quantity = 1;
		$title = $package["title"];
		$price = $package["price"];
		$successURL = Util::prefixedURL("/subscription/packages/subscription-redirect");
		$cancelURL = Util::prefixedURL("/subscription/packages/subscription-redirect");


		$paymentController->setPaymentMethod($paymentMethodSlug, $paymentMethod["public_key"], $paymentMethod["private_key"]);
		$paymentController->setProduct($title, $price, $quantity);
		$paymentController->setOther([
			"actionData" => [
				"userId" => $userId,
				"packageId" => $package["id"],
				"paymentMethodIdentifier" => $paymentMethodIdentifier
			],
			"successURL" => $successURL,
			"cancelURL" => $cancelURL
		]);

		$intent = $paymentController->initPayment();

		if (!isset($intent["redirectURL"])) die("Error has occured!");
		return redirect($intent["redirectURL"]);
		exit;
	}

	function completeSubscription($reference, $other)
	{
		$paymentController = new PaymentController();
		$response = $paymentController->completePayment(SubscriptionConstants::$TRANSACTION_TYPE_SUBSCRIPTION, $reference, $other);
		if ($response["status"] !== "success") return $response;

		$packageId = $response["details"]["packageId"];
		$userId = $response["details"]["userId"];
		$transactionId = $response["transactionId"];

		$this->addSubscriber($userId, $packageId, $transactionId);
		return redirect(Util::prefixedURL("/dashboard"))
			->with(
				'flashMessage',
				json_encode([
					"status" => "success",
					'heading' => __('package-subscription-notification-heading'),
					'description' => __('package-subscription-notification-description')
				])
			);
	}

	// Request

	function saveSubscriberRequest(Request $request)
	{
		$requestData = $request->post();

		$validator = Validator::make($requestData, [
			'packageId' => 'required|numeric',
			'userId' => 'required|numeric',
			'disable' => 'required|in:yes,no',
			'expiryDateTime' => 'required'
		]);

		if ($validator->fails()) {
			return HTTP::inStringResponse(HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]));
		}

		$this->addSubscriber(
			$requestData['userId'],
			$requestData['packageId'],
			NULL,
			($requestData['disable'] == 'yes' ? true : false),
			$requestData['expiryDateTime']
		);

		return HTTP::inStringResponse(HTTP::inBoolArray(true, __('save-notification-heading'), __('subscriber-save-notification-description')));
	}

	function subscribeRequest(Request $request, $subscriptionPackageId = NULL, $paymentMethodSlug = NULL)
	{
		$userId = $request["loggedInUser"]["id"] ?? NULL;

		if (empty($userId)) die(__('please-login-account'));
		if ($userId === NULL || $subscriptionPackageId === NULL || $paymentMethodSlug === NULL) die(__('request-failed'));

		return $this->subscribe($userId, $subscriptionPackageId, $paymentMethodSlug);
	}

	function subscriptionRedirectRequest(Request $request)
	{
		$reference = $request->reference ?? NULL;
		if ($reference === NULL) die(__('error-notification-heading'));
		return $this->completeSubscription($reference, ["params" => $request->all()]);
	}
}
