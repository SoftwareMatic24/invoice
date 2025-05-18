<?php

namespace App\Plugins\QuickInvoice\Models;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Model;

class InvoiceExpenseCategory extends Model {
	public $timestamps = false;
	protected $fillable = [
		"name",
		"added_by",
		"create_datetime",
		"update_datetime"
	];
	protected $table = "expense_categories";

	// Query: Get User Category

	static function userCategory($userId, $categoryId){
		return self::where("added_by",$userId)->where("id", $categoryId)->first();
	}

	static function userCategories($userId){
		return self::where("added_by", $userId)->orderBy("id","DESC")->get();
	}

	// Query: Save User Category

	static function saveUserCategory($categoryId, $addedBy, $data){
		if($categoryId === NULL) {
			$category = self::addUserCategory($categoryId, $addedBy, $data);
			$categoryId = $category->id ?? NULL;
		}
		else {
			$isUpdated = self::updateUserCategory($categoryId, $addedBy, $data);
			if($isUpdated === false) return NULL;
		}
		return $categoryId;
	}

	static function addUserCategory($categoryId, $addedBy, $data){
		return self::create([
			"name"=>$data["name"],
			"added_by"=>$addedBy,
			"create_datetime"=>DateTime::getDateTime()
		]);
	}

	static function updateUserCategory($categoryId, $addedBy, $data){
		return self::where("added_by", $addedBy)->where("id", $categoryId)->update([
			"name"=>$data["name"],
			"update_datetime"=>DateTime::getDateTime()
		]);
	}

	// Query: Delete User Category

	static function deleteUserCategory($categoryId, $addedBy){
		return self::where("added_by", $addedBy)->where("id", $categoryId)->delete();
	}

}

?>