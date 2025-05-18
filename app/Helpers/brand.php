<?php

use Illuminate\Support\Facades\Cache;

class Brand {

	static function brandingAll(){
		return Cache::get('branding');
	}

	static function branding($column_name){
		$branding = self::brandingAll();
		return $branding[$column_name] ?? NULL;
	}

}

?>