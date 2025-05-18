<?php

namespace App\Plugins\Subscription\Model;

use Illuminate\Database\Eloquent\Model;

class SubscriptionSubscribableRole extends Model
{

	public $timestamps = false;

	protected $fillable = [
		'role_title'
	];

	// Query: Get

	static function getRoles()
	{
		return self::get();
	}
}
