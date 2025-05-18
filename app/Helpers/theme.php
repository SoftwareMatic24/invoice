<?php

use Illuminate\Support\Facades\Cache;

if(!function_exists('theme')){
	function theme($url){
		$activeTheme = Cache::get('activeTheme');
		$themeURL = sprintf('%s/%s/%s', 'themes', $activeTheme['slug'], $url);
		return url($themeURL);
	}
}

if(!function_exists('activeTheme')){
	function activeTheme(){
		return Cache::get('activeTheme');
	}
}

if(!function_exists('themeSlug')){
	function themeSlug(){
		return activeTheme()['slug'];
	}
}

if(!function_exists('themeFile')){
	function themeFile($relativePath, $themeSlug = NULL, $options = NULL){
		if(empty($themeSlug)) $themeSlug = themeSlug();	
		$path = pathJoin('resources', 'views', 'themes', $themeSlug, $relativePath);
		return loadFile($path, $options);
	}
}

if(!function_exists('loadThemeFile')){
	function loadThemeFile($relativePath, $themeSlug = NULL, $options = NULL){
		echo themeFile($relativePath, $themeSlug, $options);
	}
}

if(!function_exists('isPage')){
	function isPage($slug, $slugs){
		if(in_array($slug, $slugs)) return true;
		return false;
	}
}

?>