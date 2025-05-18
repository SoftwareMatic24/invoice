<?php

namespace App\Plugins\QuickInvoice\Helpers;

use App\Plugins\QuickInvoice\Models\InvoiceBusiness;

class QuickInvoiceBusiness {

	static function userBusinesses($userId){
		return InvoiceBusiness::userBusinesses($userId)->toArray();
	}

	static function userBusiness($businessId, $userId){
		$business = InvoiceBusiness::userBusiness($userId, $businessId);
		return !empty($business) ? $business->toArray() : NULL;
	}

}

?>