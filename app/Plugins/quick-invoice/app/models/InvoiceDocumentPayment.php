<?php

namespace App\Plugins\QuickInvoice\Models;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Model;

class InvoiceDocumentPayment extends Model {
	public $timestamps = false;
	protected $fillable = [
		"amount",
		"reference_number",
		"note",
		"document_id",
		"payment_datetime",
		"create_datetime",
		"update_datetime"
	];
	protected $table = "document_payments";

	// Query: Get Payment

	static function getPayment($paymentId){
		return self::where("id", $paymentId)->first();
	}

	// Query: Save Payment
	
	static function savePayment($data){
		$payment = self::addPayment($data);
		$paymentId = $payment->id ?? NULL;
		return $paymentId;
	}

	static function addPayment($data){
		return self::create([
			"amount"=>$data["amount"],
			"reference_number"=>$data["referenceNumber"] ?? NULL,
			"note"=>$data["note"] ?? NULL,
			"document_id"=>$data["documentId"],
			"payment_datetime"=>$data["date"] ?? NULL,
			"create_datetime"=>DateTime::getDateTime()
		]);
	}
	
	// Query: Delete Payment

	static function deletePayemnt($paymentId){
		return self::where("id", $paymentId)->delete();
	}

}

?>