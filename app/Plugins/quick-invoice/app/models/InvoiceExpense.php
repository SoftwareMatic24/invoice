<?php

namespace App\Plugins\QuickInvoice\Models;

use App\Classes\DateTime;
use App\Plugins\QuickInvoice\Models\InvoiceBusiness as ModelsInvoiceBusiness;
use App\Plugins\QuickInvoice\Models\InvoiceExpenseCategory as ModelsInvoiceExpenseCategory;
use Illuminate\Database\Eloquent\Model;

class InvoiceExpense extends Model {
	public $timestamps = false;
	protected $fillable = [
		"title",
		"reference_number",
		"currency",
		"expense_date",
		"price",
		"tax",
		"tax_type",
		"note",
		"expense_category",
		"client",
		"business",
		"added_by",
		"create_datetime",
		"update_datetime"
	];
	protected $table = "expenses";

	// Relation

	function category(){
		return $this->belongsTo(ModelsInvoiceExpenseCategory::class, "expense_category", "id");
	}

	function business(){
		return $this->belongsTo(ModelsInvoiceBusiness::class, "business", "id");
	}

	function client(){
		return $this->belongsTo(InvoiceClient::class, "client", "id");
	}

	static function basicRelation(){
		return self::with("category")->with("business")->with("client");
	}

	// Query: Get User Expense

	static function userExpense($userId, $expenseId){
		return self::basicRelation()->where("added_by", $userId)->where("id", $expenseId)->first();
	}

	static function userExpenses($userId){
		return self::basicRelation()->where("added_by", $userId)->orderBy("id", "DESC")->get();
	}
	
	// Query: Save User Expense

	static function saveUserExpense($userId, $expenseId = NULL, $data){
		if($expenseId === NULL) {
			$expense = self::addUserExpense($userId, $data);
			$expenseId = $expense->id ?? NULL;
		}
		else {
			$isUpdated = self::updateUserExpense($userId, $expenseId, $data);
			if($isUpdated === 0) return NULL;
		}
		return $expenseId;
	}

	static function addUserExpense($userId, $data){
		return self::create([
			"title"=>$data["title"],
			"expense_category"=>$data["category"],
			"reference_number"=>$data["referenceNumber"] ?? NULL,
			"expense_date"=>$data["expenseDate"] ?? NULL,
			"currency"=>$data["currency"] ?? NULL,
			"price"=>$data["price"] ?? 0,
			"tax"=>$data["tax"] ?? 0,
			"tax_type"=>$data["taxType"] ?? "percentage",
			"note"=>$data["note"],
			"business"=>$data["business"] ?? NULL,
			"client"=>$data["client"] ?? NULL,
			"added_by"=>$userId,
			"create_datetime"=>DateTime::getDateTime()
		]);
	}

	static function updateUserExpense($userId, $expenseId, $data){
		return self::where("added_by", $userId)
			->where("id", $expenseId)
			->update([
			"title"=>$data["title"],
			"expense_category"=>$data["category"],
			"reference_number"=>$data["referenceNumber"] ?? NULL,
			"expense_date"=>$data["expenseDate"] ?? NULL,
			"currency"=>$data["currency"] ?? NULL,
			"price"=>$data["price"] ?? 0,
			"tax"=>$data["tax"] ?? 0,
			"tax_type"=>$data["taxType"] ?? "percentage",
			"note"=>$data["note"],
			"business"=>$data["business"] ?? NULL,
			"client"=>$data["client"] ?? NULL,
			"added_by"=>$userId,
			"update_datetime"=>DateTime::getDateTime()
		]);
	}

	// Query: Delete User Expense

	static function deleteUserExpense($userId, $expenseId){
		return self::where("id", $expenseId)->where("added_by", $userId)->delete();
	}

}

?>