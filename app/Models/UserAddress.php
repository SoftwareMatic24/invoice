<?php

namespace App\Models;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
	use HasFactory;

	protected $fillable = [
		'line_1',
		'line_2',
		'town_city',
		'state_province',
		'post_code',
		'country',
		'user_id',
		'create_datetime',
		'update_datetime'
	];

	public $timestamps = false;

	// Query: Get

	static function userAddress($userId){
		return self::where('user_id', $userId)->first();
	}

	// Query: Save

	static function saveAddress($userId, $address){

		$record = self::userAddress($userId);
		if(empty($record)){
			$address = self::addAddress($userId, $address);
			return $address->id;
		}
		else {
			self::updateAddress($userId, $address);
			return $record->id;
		}
	}

	static function addAddress($userId, $address){
		return self::create([
			'line_1'=>$address['addressLine1'] ?? NULL,
			'line_2'=>$address['addressLine2'] ?? NULL,
			'town_city'=>$address['townCity'] ?? NULL,
			'state_province'=>$address['provinceState'] ?? NULL,
			'post_code'=>$address['postCode'] ?? NULL,
			'country'=>$address['country'] ?? NULL,
			'user_id'=>$userId,
			'create_datetime'=>DateTime::getDateTime()
		]);
	}

	static function updateAddress($userId, $address){
		return self::where('user_id', $userId)->update([
			'line_1'=>$address['addressLine1'] ?? NULL,
			'line_2'=>$address['addressLine2'] ?? NULL,
			'town_city'=>$address['townCity'] ?? NULL,
			'state_province'=>$address['provinceState'] ?? NULL,
			'post_code'=>$address['postCode'] ?? NULL,
			'country'=>$address['country'] ?? NULL,
			'update_datetime'=>DateTime::getDateTime()
		]);
	}


}
