<?php

namespace App\Plugins\PaymentMethods\Helpers;

use App\Plugins\PaymentMethods\Model\PaymentMethod as ModelPaymentMethod;
use App\Plugins\PaymentMethods\Model\SystemPaymentMethod;
use App\Plugins\PaymentMethods\Model\UserPaymentMethod;
use Illuminate\Support\Facades\Auth;

class PaymentMethod {

	static function paymentMethods(){
		return ModelPaymentMethod::paymentMethods()->toArray();
	}

	// function paymentMethodEntries($type, $paymentMethodSlug){

	// 	if($type === 'system') {
	// 		$paymentMethod = SystemPaymentMethod::getSystemPaymentMethodByPaymentMethodSlug($paymentMethodSlug);
	// 		return !empty($paymentMethod) ? $paymentMethod->toArray() : NULL;
	// 	}
	// 	elseif($type === 'user'){
	// 		$userId = Auth::user()->id;
	// 		$paymentMethod = UserPaymentMethod::getUserPaymentMethodByUserIdPaymentMethodSlug($userId, $paymentMethodSlug);
	// 		return !empty($paymentMethod) ? $paymentMethod->toArray() : NULL;
	// 	}

	// 	return NULL;
	// }

}

?>