<?php

namespace App\Plugins\QuickInvoice\Helpers;

use App\Plugins\QuickInvoice\Models\InvoiceProduct;

class QuickInvoiceProduct {

	static function userProducts($userId){
		return InvoiceProduct::userProducts($userId)->toArray();
	}

}

?>