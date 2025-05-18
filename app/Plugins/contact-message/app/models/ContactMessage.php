<?php

namespace App\Plugins\ContactMessage\App\Models;

require_once __DIR__ . "/ContactMessageDetail.php";

use App\Classes\DateTime;
use ContactMessageDetail;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{


	public $timestamps = false;
	protected $fillable = [
		"create_datetime"
	];


	/**
	 * ===== Relation
	 */

	function detail()
	{
		return $this->hasMany(ContactMessageDetail::class, "contact_message_id", "id");
	}


	/**
	 * ===== Build Relation
	 */

	static function basicRelation()
	{
		$withDetail = function($detail){};
		return self::with(["detail"=>$withDetail]);
	}

	/**
	 * ===== Query
	 */

	static function addContactMessage($data)
	{
		$datetime = DateTime::getDateTime();
		$message = self::create([
			"create_datetime" => $datetime
		]);
		ContactMessageDetail::addDetail($message["id"], $data);
		return $message;
	}

	static function getContactMessages()
	{
		$relation = self::basicRelation();
		return $relation->orderBy('id', 'desc')->get();
	}

	static function deleteContactMessage($id){
		return self::where("id", $id)->delete();
	}

	static function unreadMessageCount(){
		return self::where("read", 0)->count();
	}

	static function markAsRead($messageId) {
		return self::where("id", $messageId)->update(["read"=>1]);
	}

}
