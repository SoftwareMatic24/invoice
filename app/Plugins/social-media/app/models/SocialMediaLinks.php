<?php

namespace App\Plugins\SocialMedia\Model;

use Illuminate\Database\Eloquent\Model;

class SocialMediaLinks extends Model {

	public $timestamps = false;
	protected $fillbale = [
		"icon",
		"url",
		"target"
	];


	/**
	 * ===== Query
	 */

	static function getSocialMediaLinks(){
		return self::get();
	}

	static function saveSocialMediaLinks($data){
		self::deleteSocialMediaLinks();
		return self::insert($data);
	}

	static function deleteSocialMediaLinks(){
		return self::where("id","!=",NULL)->delete();
	}

}

?>