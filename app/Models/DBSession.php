<?php

namespace App\Models;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Model;

class DBSession extends Model
{

	public $timestamps = false;
	protected $fillable = [
		"uid",
		"data",
		"expiry_datetime",
		"create_datetime"
	];

	protected $casts = [
		'data' => 'array'
	];

	protected $table = "sessions";



	// Query: Get

	static function getSessionByUID($uid)
	{
		return self::where("uid", $uid)->first();
	}

	// Query: Save

	static function saveSession($uid, $data, $expiryDateTime = NULL)
	{
		$row = self::getSessionByUID($uid);

		if (empty($row)) self::addSession($uid, $data, $expiryDateTime);
		else self::updateSession($uid, $data, $expiryDateTime);

		return true;
	}

	static function addSession($uid, $data, $expiryDateTime = NULL)
	{
		return self::create([
			"uid" => $uid,
			"data" => $data,
			"expiry_datetime" => $expiryDateTime,
			"create_datetime" => DateTime::getDateTime()
		]);
	}

	static function updateSession($uid, $data, $expiryDateTime  = NULL)
	{
		return self::where("uid", $uid)->update([
			"data" => $data,
			"expiry_datetime" => $expiryDateTime
		]);
	}
}
