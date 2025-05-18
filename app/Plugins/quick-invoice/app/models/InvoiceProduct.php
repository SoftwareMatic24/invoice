<?php

namespace App\Plugins\QuickInvoice\Models;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Model;

class InvoiceProduct extends Model {
	public $timestamps = false;
	protected $fillable = [
		"title",
		"price",
		"unit",
		"code",
		"type",
		"added_by",
		"create_datetime",
		"update_datetime"
	];
	protected $table = "products";

	// Query: Get User Product

	static function userProduct($userId, $productId){
		return self::where("added_by", $userId)->where("id", $productId)->first();
	}

	static function userProducts($userId){
		return self::where("added_by", $userId)->orderBy("id", "DESC")->get();
	}

	// Query: Save User Product

	static function saveUserProduct($productId = NULL, $addedBy, $data){
		if($productId === NULL){
			$product = self::addUserProduct($addedBy, $data);
			$productId = $product->id ?? NULL;
		}
		else {
			$isUpdated = self::updateUserProduct($productId, $addedBy, $data);
			if(!$isUpdated) return NULL;
		}
		
		return $productId;
	}

	static function addUserProduct($addedBy, $data){
		return self::create([
			"title"=>$data["title"],
			"price"=>$data["price"] ?? 0,
			"unit"=>$data["unit"] ?? NULL,
			"code"=>$data["code"] ?? NULL,
			"type"=>$data["type"] ?? "product",
			"added_by"=>$addedBy,
			"create_datetime"=>DateTime::getDateTime()
		]);
	}

	static function updateUserProduct($productId, $addedBy, $data){
		return self::where("id", $productId)->where("added_by", $addedBy)->update([
			"title"=>$data["title"],
			"price"=>$data["price"] ?? 0,
			"unit"=>$data["unit"] ?? NULL,
			"code"=>$data["code"] ?? NULL,
			"type"=>$data["type"] ?? "product",
			"update_datetime"=>DateTime::getDateTime()
		]);
	}

	// Query: Delete User Product

	static function deleteUserProduct($userId, $productId){
		return self::where("id", $productId)->where("added_by", $userId)->delete();
	}
}

?>