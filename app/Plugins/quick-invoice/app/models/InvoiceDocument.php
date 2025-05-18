<?php

namespace App\Plugins\QuickInvoice\Models;

use App\Classes\DateTime;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceDocument extends Model
{
	public $timestamps = false;
	protected $fillable = [
		"uid",
		"document_type",
		"document_number",
		"reference_number",
		"order_number",
		"issue_date",
		"due_date",
		"currency",
		"payment_method",
		"delivery_type",
		"discount",
		"discount_type",
		"salutation",
		"note",
		"client",
		"business",
		"added_by",
		"create_datetime",
		"update_datetime"
	];
	protected $table = "documents";

	// Relation

	function items()
	{
		return $this->hasMany(InvoiceDocumentItem::class, "document_id", "id");
	}

	function meta()
	{
		return $this->hasMany(InvoiceDocumentMeta::class, "document_id", "id");
	}

	function client()
	{
		return $this->belongsTo(InvoiceClient::class, "client", "id");
	}

	function business()
	{
		return $this->belongsTo(InvoiceBusiness::class, "business", "id");
	}

	function payments()
	{
		return $this->hasMany(InvoiceDocumentPayment::class, "document_id", "id");
	}

	static function basicRelation()
	{
		return self::with("items")
			->with("client")
			->with("business.logo", "business.signature")
			->with("meta")
			->with("payments");
	}

	// Query: Get Document

	static function getDocumentByUIDAndDocumentNumber($uid, $documentNumber)
	{
		$result = self::basicRelation()
			->where("uid", $uid)
			->where("document_number", $documentNumber)
			->orderBy("id", "DESC")->first();
		return $result;
	}

	// Query: Get User Document

	static function getuserDocuments($userId){
		$result = self::basicRelation()
			->where("added_by", $userId)
			->orderBy("id","DESC")
			->get();
		return $result;
	}

	static function getUserDocument($userId, $documentId)
	{
		$result = self::basicRelation()
			->where("added_by", $userId)
			->where("id", $documentId)
			->first();
		return $result;
	}

	static function getUserDocumentByType($userId, $documentId, $documentType)
	{
		$result = self::basicRelation()
			->where("added_by", $userId)
			->where("document_type", $documentType)
			->where("id", $documentId)
			->orderBy("id", "DESC")->first();
		return $result;
	}

	static function getUserDocumentsByType($userId, $documentType)
	{
		return self::basicRelation()
			->where("added_by", $userId)
			->where("document_type", $documentType)
			->orderBy("id", "DESC")->get();
	}

	// Query: Save User Document

	static function saveUserDocument($documentId = NULL, $documentType, $userId, $data)
	{
		if ($documentId === NULL) {
			$document = self::addUserDocument($userId, $documentType, $data);
			$documentId = $document->id ?? NULL;
		} else {
			$isUpdated = self::updateUserDocument($documentId, $documentType, $userId, $data);
			if ($isUpdated == 0) return NULL;
		}

		return $documentId;
	}

	static function addUserDocument($userId, $documentType, $data)
	{
		$uid = Str::orderedUuid()->toString();

		$document = self::create([
			"uid" => $uid,
			"document_type" => $documentType,
			"document_number" => $data["documentNumber"],
			"reference_number" => $data["referenceNumber"] ?? NULL,
			"order_number" => $data["orderNumber"] ?? NULl,
			"issue_date" => $data["issueDate"] ?? NULL,
			"due_date" => $data["dueDate"] ?? NULL,
			"due_date" => $data["dueDate"] ?? NULL,
			"currency" => $data["currency"] ?? NULL,
			"payment_method" => $data["paymentMethod"] ?? NULL,
			"delivery_type" => $data["deliveryType"] ?? NULL,
			"discount" => $data["discount"] ?? 0,
			"discount_type" => $data["discountType"] ?? "percentage",
			"salutation" => $data["salutation"] ?? NULL,
			"note" => $data["note"] ?? NULL,
			"client" => $data["client"] ?? NULL,
			"business" => $data["business"] ?? NULL,
			"added_by" => $userId,
			"create_datetime" => DateTime::getDateTime()
		]);
		InvoiceDocumentItem::saveItems($document->id, $data["items"]);
		InvoiceDocumentMeta::saveMeta($document->id, $data["meta"]);
		return $document;
	}

	static function updateUserDocument($documentId = NULL, $documentType, $userId, $data)
	{
		$isUpdated = self::where("id", $documentId)->where("added_by", $userId)->update([
			"document_type" => $documentType,
			"document_number" => $data["documentNumber"],
			"reference_number" => $data["referenceNumber"] ?? NULL,
			"order_number" => $data["orderNumber"] ?? NULl,
			"issue_date" => $data["issueDate"] ?? NULL,
			"due_date" => $data["dueDate"] ?? NULL,
			"due_date" => $data["dueDate"] ?? NULL,
			"currency" => $data["currency"] ?? NULL,
			"payment_method" => $data["paymentMethod"] ?? NULL,
			"delivery_type" => $data["deliveryType"] ?? NULL,
			"discount" => $data["discount"] ?? 0,
			"discount_type" => $data["discountType"] ?? "percentage",
			"salutation" => $data["salutation"] ?? NULL,
			"note" => $data["note"] ?? NULL,
			"client" => $data["client"] ?? NULL,
			"business" => $data["business"] ?? NULL,
			"added_by" => $userId,
			"update_datetime" => DateTime::getDateTime()
		]);
		InvoiceDocumentItem::saveItems($documentId, $data["items"]);
		InvoiceDocumentMeta::saveMeta($documentId, $data["meta"]);
		return $isUpdated;
	}

	// Query: Delete User Document

	static function deleteUserDocument($userId, $documentId)
	{
		return self::where("id", $documentId)->where("added_by", $userId)->delete();
	}

	// Query: Copy User Document

	static function copyDocumentAs($userId, $documentId, $newDocumentType)
	{
		$document = self::getUserDocument($userId, $documentId);
		if ($document === NULL) return NULL;
		$document = $document->toArray();

		$documentData = $document;
		unset($documentData["client"]);
		unset($documentData["business"]);
		unset($documentData["items"]);
		unset($documentData["meta"]);
		unset($documentData["payments"]);
		unset($documentData["id"]);
		unset($documentData["create_datetime"]);
		unset($documentData["update_datetime"]);

		DB::beginTransaction();
		try {
			//1. Document
			$documentData["uid"] = Str::orderedUuid()->toString();
			$documentData["document_type"] = $newDocumentType;
			$documentData["issue_date"] = date("Y-m-d");
			$documentData["due_date"] = NULL;
			$documentData["client"] = $document["client"]["id"];
			$documentData["business"] = $document["business"]["id"];
			$documentData["create_datetime"] = DateTime::getDateTime();
			$newDocument = self::create($documentData);

			//2. Items
			$items = array_map(function ($item) use ($newDocument) {
				unset($item["id"]);
				$item["document_id"] = $newDocument->id;
				return $item;
			}, $document["items"]);
			InvoiceDocumentItem::insert($items);

			//3. Meta
			$meta = array_map(function ($row) use ($newDocument) {
				unset($row["id"]);
				$row["document_id"] = $newDocument->id;
				return $row;
			}, $document["meta"]);

			InvoiceDocumentMeta::deleteMeta($newDocument->id);
			InvoiceDocumentMeta::insert($meta);

			DB::commit();
		} catch (Exception $e) {
			DB::rollBack();
			return NULL;
		}
		
		return $newDocument->id;
	}

	// Query: Rows

	static function getRow($documentId){
		return self::where("id", $documentId)->first();
	}

	static function getUserRow($documentId, $userId){
		return self::where("id", $documentId)->where("added_by", $userId)->first();
	}

	static function getUserRowsByType($userId, $documentType){
		return self::where("added_by", $userId)->where("document_type", $documentType)->get();
	}
}
