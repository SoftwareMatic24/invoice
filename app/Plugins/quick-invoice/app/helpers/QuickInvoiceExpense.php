<?php

namespace App\Plugins\QuickInvoice\Helpers;

use App\Plugins\QuickInvoice\Models\InvoiceExpense;
use App\Plugins\QuickInvoice\Models\InvoiceExpenseCategory;

class QuickInvoiceExpense {

	static function userExpenses($userId){
		return InvoiceExpense::userExpenses($userId)->toArray();
	}

	static function userCategories($userId){
		return InvoiceExpenseCategory::userCategories($userId)->toArray();
	}

}

?>