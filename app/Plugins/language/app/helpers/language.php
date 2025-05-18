<?php

use App\Plugins\Language\Models\Language;
use Illuminate\Support\Facades\Cache;

if(!function_exists('languages')){
	function languages(){
		return Cache::get("languages");
	}
}

if(!function_exists('language')){
	function language($code = NULL){
		if(empty($code)) $code = app()->getLocale();
		return arrayFind($code, languages(), 'code');
	}
}

if(!function_exists('dbLanguages')){
	function dbLanguages(){
		return Language::getLanguages()->toArray();
	}
}

if(!function_exists('dbLanguage')){
	function dbLanguage($code){
		$language = Language::getLanguageByCode($code);
		return empty($language) ? NULL : $language->toArray();
	}
}

if(!function_exists('primaryLanguage')){
	function primaryLanguage(){
		return Cache::get('primaryLanguage');
	}
}

if(!function_exists('langURL')){
	function langURL($url){

		$primaryLanguage = Cache::get('primaryLanguage');
		$urlLangCode = app()->getLocale();
	
		if (substr($url, 0, 1) === '/') $url = substr($url, 1);
		if (($primaryLanguage['code'] ?? NULL) === $urlLangCode) return url('/' . $url);
		return url($urlLangCode . '/' . $url);
	}
}

if(!function_exists('isCurrentLanguagePrimary')){
	function isCurrentLanguagePrimary(){
		$primaryLanguage = Cache::get('primaryLanguage');
		if(($primaryLanguage['code'] ?? NULL) == app()->getLocale()) return true;
		return false;
	}
}


?>