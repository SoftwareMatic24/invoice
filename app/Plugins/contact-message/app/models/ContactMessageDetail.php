<?php

require_once __DIR__."/ContactMessage.php";

use App\Plugins\ContactMessage\App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Model;

class ContactMessageDetail extends Model {

	public $timestamps = false;
	protected $fillable = [
		"column_name",
		"column_value",
		"contact_message_id"
	];

	/**
	 * ===== Relation
	 */

	 function message(){
		return $this->belongsTo(ContactMessage::class, "contact_message_id", "id");
	 }


	/**
	 * ===== Query
	 */

	static function addDetail($contactMessageId, $data){

		$dataToInsert = [];

		foreach($data as $column=>$value){
			$dataToInsert[] = [
				"column_name"=>$column,
				"column_value"=>$value,
				"contact_message_id"=>$contactMessageId
			];
		}

		self::insert($dataToInsert);

	}

}
