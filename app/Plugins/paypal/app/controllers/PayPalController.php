<?php

namespace App\Plugins\PayPal\Controller;

require_once __DIR__."/../../../payment-method/app/models/UserPaymentMethod.php";

use App\Http\Controllers\Controller;
use App\Plugins\PaymentMethods\Model\UserPaymentMethod;
use Srmklive\PayPal\Services\PayPal;
use Illuminate\Support\Str;

class PayPalController extends Controller {

	function prepareConfig($clientId, $clientSecret){
		$config = config("paypal");
		$mode = $config["mode"];

		$config[$mode]["client_id"] = $clientId;
		$config[$mode]["client_secret"] = $clientSecret;

		return $config;
	}

	function prepareUserConfig($userId){
		$paymentMethod = UserPaymentMethod::getUserPaymentMethodByUserIdPaymentMethodSlug($userId, "paypal");
		$paymentMethod = $paymentMethod->toArray();
		if(sizeof($paymentMethod) > 0) $paymentMethod = $paymentMethod[0];
	
		if($paymentMethod === NULL || $paymentMethod["status"] !== "active") return NULL;
		$config = $this->prepareConfig($paymentMethod["public_key"], $paymentMethod["private_key"]);
		return $config;
	}

	function createOrder($config, $currency, $price, $cancelURL, $returnURL){
		$provider = new PayPal($config);
		$provider->setApiCredentials($config);
		$provider->getAccessToken();

		$uid = Str::uuid()->toString();

		$price = number_format($price, 2, ".", "");
		
		$data = [
			"intent" => "CAPTURE",
			"purchase_units" => [
				[
					"amount" => [
						"currency_code" => $currency,
						"value" => $price
					]
				]
			],
			"application_context" => [
				"brand_name" => config("app.name"),
				"shipping_preference" => "NO_SHIPPING",
				"cancel_url" => $cancelURL."?reference=".$uid,
				"return_url" => $returnURL."?reference=".$uid
			]
		];

		return [
			"order"=>$provider->createOrder($data),
			"uid"=>$uid
		];
	}

	function orderDetails($config, $token){
		if ($token == null || $token == false) return null;
		$provider = new PayPal($config);
		$provider->getAccessToken();
		return $provider->capturePaymentOrder($token);
	}
}

?>