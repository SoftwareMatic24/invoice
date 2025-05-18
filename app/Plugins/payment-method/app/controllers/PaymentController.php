<?php

namespace App\Plugins\PaymentMethods\Controller;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Plugins\Currency\Model\Currency;
use App\Plugins\PaymentMethods\Model\SystemPaymentMethod;
use App\Plugins\PaymentMethods\Model\Transaction;
use App\Plugins\PaymentMethods\PaymentConstants\PaymentConstants;
use App\Plugins\PayPal\Controller\PayPalController;
use App\Plugins\Stripe\Controller\StripeController;

class PaymentController extends Controller
{
	protected $paymentMethod = [];
	protected $product = [];
	protected $other = [];

	// setters
	function setPaymentMethod($paymentMethodSlug, $publicKey, $privateKey)
	{
		$this->paymentMethod["slug"] = $paymentMethodSlug;
		$this->paymentMethod["publicKey"] = $publicKey;
		$this->paymentMethod["privateKey"] = $privateKey;
		return $this;
	}

	function setProduct($name = NULL, $totalPrice = NULL, $quantity = 1)
	{
		if ($name === NULL) return ["status" => "fail", "msg" => "Product name is required"];
		else if ($totalPrice === NULL) return ["status" => "fail", "msg" => "Total price is required"];

		$this->product["name"] = $name;
		$this->product["totalPrice"] = $totalPrice;
		$this->product["quantity"] = $quantity;

		return $this;
	}

	function setOther($other)
	{
		$this->other = $other;
	}

	// methods
	function initPayment()
	{
		$currency = $this->other["currency"] ?? NULL;

		if ($currency === NULL) {
			$primaryCurrency = Currency::getPrimaryCurrency();
			$primaryCurrency = $primaryCurrency->toArray();
			$this->other["currency"] = $primaryCurrency["currency"];
		}

		$response = NULL;
		if ($this->paymentMethod["slug"] === "stripe") $response = $this->initStripePayment();
		else if ($this->paymentMethod["slug"] === "paypal") $response = $this->initPayPalPayment();

		return $response;
	}

	function completePayment($paymentType, $reference, $other)
	{
		$details = $this->getPaymentDetails($reference, $other);
		
		if (($details["paymentStatus"] ?? NULL) != "complete") return ["status" => "fail", "msg" => "Payment is not complete. Contact admin."];
		Action::updateStatusByUid($reference . "-data", "complete");

		$transaction = Transaction::addTransaction([
			"uid" => $reference,
			"customerName" => NULL,
			"customerEmail" => NULL,
			"productName" => $details["productName"],
			"productAmount" => $details["totalPricePaid"],
			"productQuantity" => $details["quantity"],
			"currency" => $details["currency"] ?? NULL,
			"paymentMethod"=>$details["paymentMethod"] ?? NULL,
			"status" => $details["paymentStatus"],
			"user_id" => $details["userId"] ?? NULL,
			"type" => $paymentType,
		]);
		$transactionId = $transaction["id"];

		return ["status"=>"success", "msg"=>"Payment completed", "details"=>$details, "transactionId"=>$transactionId];
	}

	function getPaymentDetails($reference, $other)
	{	
		$actionForCheckoutSession = Action::getActionByUid($reference);
		$actionForData = Action::getActionByUid($reference . "-data");
		if ($actionForData["status"] === "complete") return ["status" => "fail", "msg" => "Link expired"];

		$checkoutSessionId = $actionForCheckoutSession->data['checkoutSessionId'] ?? NULL;
		$paymentMethodSlug = $actionForData->data['paymentMethod'] ?? NULL;
		$paymentMethodIdentifier = $actionForData->data['paymentMethodIdentifier'];

		$paymentMethod = SystemPaymentMethod::getPaymentMethodByIdentifierAndSlug($paymentMethodIdentifier,$paymentMethodSlug);
		$this->setPaymentMethod($paymentMethodSlug, $paymentMethod["public_key"], $paymentMethod["private_key"]);
		
		$details = [];
		foreach($actionForData->data as $key=>$value){
			$details[$key] = $value;
		}

		if ($paymentMethodSlug === "stripe") $details = $this->stripeCheckoutSessionData($checkoutSessionId, $details);
		else if ($paymentMethodSlug === "paypal") $details = $this->paypalCheckoutSessionData($other["params"]["token"], $details);

		return $details;
	}

	// Stripe

	function initStripePayment()
	{
		if (!$this->paymentMethodHasKeys($this->paymentMethod)) ["status" => "fail", "mgs" => "Stripe account is available. Choose another payment method."];

		$stripeController = new StripeController($this->paymentMethod["privateKey"]);
		$actionData = $this->other["actionData"] ?? [];

		$response = $stripeController->checkout([
			"price" => $this->product["totalPrice"],
			"currency" => $this->other["currency"],
			"productName" => $this->product["name"],
			"quantity" => $this->product["quantity"],
			"successURL" => $this->other["successURL"],
			"cancelURL" => $this->other["cancelURL"],
		]);

		$uid = $response["uid"];

		$actionData["paymentMethod"] = "stripe";
		$actionData["productName"] = $this->product["name"];
		$actionData["quantity"] = $this->product["quantity"];
		$actionData["currency"] = $this->other["currency"];
		$actionData["totalPrice"] = $this->product["totalPrice"];

		Action::addAction([
			"slug" => PaymentConstants::$PAYMENT_TYPE_STRIPE,
			"uid" => $uid . "-data",
			"status" => "pending",
			"data" => $actionData
		]);

		return $response;
	}

	function stripeCheckoutSessionData($checkoutSessionId, $data)
	{
		$stripeController = new StripeController($this->paymentMethod["privateKey"]);
		$transactionData = $stripeController->retrieveCheckoutSessionData($checkoutSessionId);
		$data["paymentStatus"] = ($transactionData["paymentStatus"] ?? NULL) == "paid" ? "complete" : "pending";
		$data["totalPricePaid"] = $transactionData["amountTotal"] ?? NULL;
		return $data;
	}

	// PayPal

	function initPayPalPayment()
	{
		$paypalController = new PayPalController();
		$config = $paypalController->prepareConfig($this->paymentMethod["publicKey"], $this->paymentMethod["privateKey"]);
		$paypalOrder = $paypalController->createOrder($config, $this->other["currency"], $this->product["totalPrice"], $this->other["cancelURL"], $this->other["successURL"]);

		if (($paypalOrder["order"]["status"] ?? NULL) !== "CREATED") return ["status" => "fail", "msg" => "PayPal order could not be created"];

		$link = array_filter($paypalOrder["order"]["links"], function ($link) {
			if (($link["rel"] ?? NULL) === "approve") return $link;
		});

		$link = reset($link);
		if (!isset($link["href"])) return ["status" => "fail", "msg" => "PayPal payment link count not be created."];

		$actionData = $this->other["actionData"] ?? [];

		$actionData["paymentMethod"] = "paypal";
		$actionData["productName"] = $this->product["name"];
		$actionData["quantity"] = $this->product["quantity"];
		$actionData["currency"] = $this->other["currency"];
		$actionData["totalPrice"] = $this->product["totalPrice"];

		Action::addAction([
			"slug" => PaymentConstants::$PAYMENT_TYPE_PAYPAL,
			"uid" => $paypalOrder["uid"] . "-data",
			"status" => "pending",
			"data" => $actionData
		]);

		$paypalOrder["redirectURL"] = $link["href"];

		return $paypalOrder;
	}

	function paypalCheckoutSessionData($token, $data)
	{
		$paypalController = new PayPalController();
		$config = $paypalController->prepareConfig($this->paymentMethod["publicKey"], $this->paymentMethod["privateKey"]);

		$transactionData = $paypalController->orderDetails($config, $token);

		if(!isset($transactionData["status"])) die("Payment already captured.");

		$status = $transactionData["status"] === "COMPLETED" ? "complete" : "pending";
		$pricePaid = $transactionData["purchase_units"][0]["payments"]["captures"][0]["amount"]["value"] ?? NULL;

		$data["paymentStatus"] = $status;
		$data["totalPricePaid"] = $pricePaid;
		
		return $data;
	}

	// other

	function paymentMethodHasKeys($paymentMethod)
	{
		$status = false;

		if (
			($paymentMethod["publicKey"] !== NULL && $paymentMethod["publicKey"] !== "") &&
			($paymentMethod["privateKey"] !== NULL && $paymentMethod["privateKey"] !== "")
		) {
			$status = true;
		}

		return $status;
	}
}
