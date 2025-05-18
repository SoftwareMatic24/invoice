<?php

namespace App\Plugins\QuickInvoice\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InvoiceDocumentItem extends Model
{
	public $timestamps = false;
	protected $fillable = [
		"title",
		"quantity",
		"unit_price",
		"unit",
		"code",
		"vat",
		"document_id"
	];
	protected $table = "document_items";

	
	// Query: Save Items

	static function saveItems($documentId, $items)
	{
		try {
			DB::beginTransaction();
			self::deleteItems($documentId);
			$dataToInsert = [];
			foreach ($items as $item) {
				$dataToInsert[] = [
					"title" => $item["item-name"],
					"quantity" => $item["item-quantity"],
					"unit_price" => $item["item-unit-price"] ?? 0,
					"unit" => $item["item-unit"] ?? NULL,
					"code" => $item["item-code"] ?? NULL,
					"vat" => $item["item-vat"] ?? 0,
					"document_id" => $documentId
				];
			}
			$response = self::insert($dataToInsert);
			DB::commit();
			return $response;
		} catch (Exception $e) {
			DB::rollBack();
		}
	}

	// Query: Delete Items

	static function deleteItems($documentId)
	{
		return self::where("document_id", $documentId)->delete();
	}
}
