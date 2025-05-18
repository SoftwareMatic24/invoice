<?php

namespace App\Plugins\QuickInvoice\Models;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Model;

class InvoiceDocumentField extends Model {
	public $timestamps = false;
	protected $fillable = [
		"name",
		"slug",
		"document_type",
		"business_id",
		"position",
		"create_datetime",
		"update_datetime"
	];
	protected $table = "document_fields";

	// Relation

	function business(){
		return $this->belongsTo(InvoiceBusiness::class, "business_id", "id");
	}

	static function basicRelation(){
		return self::with("business");
	}

	// Query: Get Field

	static function getFieldsByBusiness($businessId){
		return self::where("business_id", $businessId)->get();
	}

	static function getFieldsByBusinessIDs($businessIDs){
		$relation = self::basicRelation();
		return $relation->whereIn("business_id", $businessIDs)->orderBy("id","DESC")->get();
	}

	// Query: Save Field

	static function saveField($fieldId = NULL, $data){
		if($fieldId === NULL) {
			$field = self::addField($data);
			$fieldId = $field->id ?? NULL;
		}
		else {
			$isUpdated = self::updateField($fieldId, $data);
			if($isUpdated == 0) return NULL;
		}
		return $fieldId;
	}

	static function addField($data){
		return self::create([
			"name"=>$data["name"],
			"slug"=>$data["slug"],
			"document_type"=>$data["documentType"],
			"business_id"=>$data["businessId"],
			"position"=>$data["position"],
			"create_datetime"=>DateTime::getDateTime()
		]);
	}

	static function updateField($fieldId, $data){
		return self::where("id", $fieldId)->update([
			"name"=>$data["name"],
			"document_type"=>$data["documentType"],
			"business_id"=>$data["businessId"],
			"position"=>$data["position"],
			"update_datetime"=>DateTime::getDateTime()
		]);
	}

	// Query: Delete Field

	static function deleteField($fieldId){
		return self::where("id", $fieldId)->delete();
	}
}

?>