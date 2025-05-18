<?php

namespace App\Plugins\Stripe\Controller;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Models\Action;
use App\Plugins\PaymentMethods\Model\SystemPaymentMethod;
use App\Plugins\Stripe\Model\Stripe as StripeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\PaymentIntent;
use Stripe\StripeClient;


class StripeController extends Controller
{

	// constructor

	public function __construct($privakeKey = NULL, $stripeFee = false)
	{
		$this->stripeFee = $stripeFee;
		$this->stripeFeePercentage = 1.5;
		$this->stripeFeeAddon = 0.20;
		if($privakeKey !== NULL) $this->privateKey = $privakeKey;
	}

	// methods

	function checkout($options = []){
		$response = $this->createCheckoutSession($options);
		$uid = $response["uid"];
		$checkoutSession = $response["checkoutSession"];

		if(!isset($checkoutSession->url)) return ["status"=>"fail", "msg"=>"Error has occured."];
		return ["status"=>"success", "msg"=>"Checkout session generated.", "redirectURL"=>$checkoutSession->url, "uid"=>$uid];
	}

	function createCheckoutSession($options = []){

		function hasQueryParameter($string) {
			return preg_match('/[?&][^=]+=[^&]+/', $string) === 1;
		}

		$price = $options["price"] ?? 0;
		$currency = $options["currency"] ?? "USD";
		$quantity = $options["quantity"] ?? 1;
		$productName = $options["productName"] ?? "Product";
		$successURL = $options["successURL"] ?? NULL;
		$cancelURL = $options["cancelURL"] ?? NULL;
		$other = $options["other"] ?? [];
		
		$uid = Str::uuid();
		
		if($this->privateKey === NULL) return ["status"=>"fail", "msg"=>"No active stripe details found."];
		else if($successURL === NULL) return ["status"=>"fail", "msg"=>"Please provide success url"]; 
		else if($cancelURL === NULL) return ["status"=>"fail", "msg"=>"Please provide cancel url"]; 
	
		if(hasQueryParameter($successURL) === true) $successURL = $successURL."&reference=".$uid;
		else $successURL = $successURL."?reference=".$uid;

		if(hasQueryParameter($cancelURL) === true) $cancelURL = $cancelURL."&reference=".$uid;
		else $cancelURL = $cancelURL."?reference=".$uid;

		$secretKey = $this->privateKey;
		$stripe = new StripeClient($secretKey);
		\Stripe\Stripe::setApiKey($secretKey);

		$fee = 0;

		if($this->stripeFee === true){
			$fee = $this->calculateFee($price);
			$price = $price + $fee;
		}

		$price = number_format($price, 2, ".", "");
		
		$price = \Stripe\Price::create([
			"unit_amount" => $price * 100,
			"currency" => $currency,
			"product_data"=> [
				"name"=>$productName
			]

		]);


		$lineItems = [
			["price" => $price->id, "quantity" => $quantity]
		];
		
		if(isset($options["description"])) $lineItems[0]["description"] = $options["description"];

		$sessionData = [
			"success_url" => $successURL,
			"cancel_url" => $cancelURL,
			"mode" => "payment",
			"line_items" => $lineItems
		];

		if(isset($options["receiptEmail"])) $sessionData["customer_email"] = $options["receiptEmail"];

		$checkoutSession = $stripe->checkout->sessions->create($sessionData);

		if(isset($checkoutSession->url)){
			Action::addAction([
				"slug"=>"STRIPE_CHECKOUT",
				"uid"=>$uid,
				"status"=>"pre-complete",
				"data"=>[
					"checkoutSessionId"=>$checkoutSession->id,
					...$other
				]
			]);
		}

		return [ "uid"=>$uid, "checkoutSession"=>$checkoutSession];
	}

	function retrieveCheckoutSession($checkoutSessionId){
		if($this->privateKey === NULL) return ["status"=>"fail", "msg"=>"No active stripe details found."];
		$secretKey = $this->privateKey;
		\Stripe\Stripe::setApiKey($secretKey);
		return \Stripe\Checkout\Session::retrieve($checkoutSessionId);
	}

	function retrieveCheckoutSessionData($checkoutSessionId){
		
		$checkoutSession = $this->retrieveCheckoutSession($checkoutSessionId);

		return [
			"paymentStatus"=>$checkoutSession->payment_status,
			"amountTotal"=>$checkoutSession->amount_total / 100,
			"currency"=>$checkoutSession->currency,
			"customerDetails"=>$checkoutSession->customer_details->toArray(),
		];
	}

	function retrivePaymentIntent($paymentIntentId){
		return PaymentIntent::retrieve($paymentIntentId);
	}

	function createPaymentIntent($options = []){

		$price = $options["price"] ?? 0;
		$currency = $options["currency"] ?? "USD";
		$userId = $options["userId"] ?? NULL;
		$other = $options["other"] ?? [];
		
		$paymentMethodData = $options["paymentMethodData"] ?? NULL;
		$confirmationMethod = $options["confirmtionMethod"] ?? NULL;

		$secretKey = $this->privateKey;
		$stripe = new StripeClient($secretKey);
		\Stripe\Stripe::setApiKey($secretKey);

		$originalPrice = $price;
		$fee = 0;

		if($this->stripeFee === true){
			$fee = $this->calculateFee($price);
			$price = $price + $fee;
		}	

		$price = number_format($price, 2, ".", "");
		
		$intentArr = [
			"amount" => $price * 100,
			"currency" => $currency,
			"automatic_payment_methods" => [
				'enabled' => true,
			],
		];

		if(isset($other["description"])) $intentArr["description"] = $other["description"];
		if(isset($other["receiptEmail"])) $intentArr["receipt_email"] = $other["receiptEmail"];

		if($confirmationMethod !== NULL) $intentArr["confirmation_method"] = $confirmationMethod;
		if($paymentMethodData !== NULL) $intentArr["payment_method_data"] = $paymentMethodData;

		$paymentIntent = $stripe->paymentIntents->create($intentArr);
		
		$uid = Str::uuid();

		$otherData = [
			"amount"=>$originalPrice,
			"userId"=>$userId
		];

		foreach($other as $key=>$value){
			$otherData[$key] = $value;
		}

		Action::addAction([
			"slug"=>"STRIPE_CHECKOUT",
			"uid"=>$uid,
			"status"=>"pending",
			"data"=>$otherData
		]);

		return [
			"clientSecret"=>$paymentIntent->client_secret,
			"uid"=>$uid
		];

	}

	function retreievePaymentIntentData($paymentIntentId){

		if($this->privateKey === NULL) return ["status"=>"fail", "msg"=>"No active stripe details found."];

		$secretKey = $this->privateKey;
		\Stripe\Stripe::setApiKey($secretKey);
		$intent = \Stripe\PaymentIntent::retrieve($paymentIntentId);


		$paymentMethod = $intent->payment_method;
		$amount = $intent->amount;
		$currency = $intent->currency;

			
		return [
			"paymentMethod"=>$paymentMethod,
			"paymentStatus"=>$intent->status,
			"amountTotal"=>$amount / 100,
			"currency"=>$currency,
		];

		return $intent;
	}


	// request

	function createPaymentIntentRequest(Request $request){
		$data = $request->post();

		$stripePaymentMethod = SystemPaymentMethod::getPaymentMethodByIdentifierAndSlug("sales-account","stripe");

		if($stripePaymentMethod === NULL || $stripePaymentMethod["status"] != "active" || $stripePaymentMethod["private_key"] === "" || $stripePaymentMethod["private_key" === NULL]){
			return ["status"=>"fail", "msg"=>"Admin does not have a setup stripe account. Contact Admin."];
		}

		$this->privateKey = $stripePaymentMethod["private_key"];

		$this->includeFee(true);
		$this->setFee(1.5, 0.20);

		$response = $this->createPaymentIntent($data);
		
		return ["status"=>"success", "msg"=>"Intent created.", "intent"=>$response["clientSecret"], "uid"=>$response["uid"]];
	}

	// other

	function setFee($stripeFeePercentage, $stripeFeeAddon){
		$this->stripeFeePercentage = $stripeFeePercentage;
		$this->stripeFeeAddon = $stripeFeeAddon;
	}

	function includeFee($bool = false){
		$this->stripeFee = $bool;
	}

	function calculateFee($price){
		$fee = ($this->stripeFeePercentage / 100) * $price;
		$fee = $fee + $this->stripeFeeAddon;
		return $fee;
	}

}
