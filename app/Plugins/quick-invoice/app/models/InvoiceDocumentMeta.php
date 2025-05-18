<?php

namespace App\Plugins\QuickInvoice\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InvoiceDocumentMeta extends Model
{
	public $timestamps = false;
	protected $fillable = [
		"column_name",
		"column_value",
		"document_id"
	];
	protected $table = "document_meta";

	// Query: Save Meta
	static function saveMeta($documentId, $data){
		try {
			DB::beginTransaction();
			self::deleteMeta($documentId);
			$dataToInsert = [];
			foreach ($data as $columnName=>$columnValue) {
				$dataToInsert[] = [
					"column_name"=>$columnName,
					"column_value"=>$columnValue ?? "",
					"document_id"=>$documentId
				];
			}
			$response = self::insert($dataToInsert);
			DB::commit();
			return $response;
		} catch (Exception $e) {
			DB::rollBack();
		}
	}

	// Query: Delete Meta
	static function deleteMeta($documentId){
		return self::where("document_id", $documentId)->delete();
	}
}
