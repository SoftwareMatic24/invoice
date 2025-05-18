<?php

namespace App\Classes;

class HTMLParser {

	// parsers

	static function tagsById($htmlContent, $id){
		$pattern = "/<[^>]*id=[\"']" . preg_quote($id, '/') . "[\"'][^>]*>(.*?)<\/[^>]*>/is";
		if (preg_match_all($pattern, $htmlContent, $matches)) return $matches[0];
		else return [];
	}

	static function tagsByAttributeValue($htmlContent, $attribute, $value){
		$pattern = "/<[^>]*$attribute=[\"']" . preg_quote($value, '/') . "[\"'][^>]*>(.*?)<\/[^>]*>/is";
		if (preg_match_all($pattern, $htmlContent, $matches)) return $matches[0];
		else return [];
	}

	static function tagsByAttribyteValueMultilevel($htmlContent, $attribute, $value){
		$pattern = "/<([^>]+)\s$attribute\s*=\s*[\"']" . preg_quote($value, '/') . "[\"'].*?>(.*?)<\/\\1>/s";
		if (preg_match_all($pattern, $htmlContent, $matches)) return $matches[0];
		else return [];
	}

	static function table($htmlContent = ""){
		$pattern = '/<table\b[^>]*>(.*?)<\/table>/s';
		if (preg_match_all($pattern, $htmlContent, $matches)) return $matches[0];
		else return [];
	}

	static function tableTH($htmlContent = ""){
		$pattern = '/<th[^>]*>(.*?)<\/th>/s';
		if (preg_match_all($pattern, $htmlContent, $matches)) return $matches[1];
		else return [];
	}

	static function tableTD($htmlContent = ""){
		$pattern = '/<td[^>]*>(.*?)<\/td>/s';
		if (preg_match_all($pattern, $htmlContent, $matches)) return $matches[1];
		else return [];
	}

	static function input($htmlContent = ""){
		$pattern = '/<input.*?value=["\'](.*?)["\'].*?>/';
		if (preg_match_all($pattern, $htmlContent, $matches)) return $matches[1];
		else return [];
	}

	static function inputWithTag($htmlContent = ""){
		$pattern = '/<input.*?>/';
		if (preg_match_all($pattern, $htmlContent, $matches)) return $matches[0];
		else return [];
	}

	static function p($htmlContent = "", $options = []){
		$trim = $options["trim"] ?? false;
		$output = [];

		function doTrim($output){
			return array_map(function($content){
				return trim($content);
			}, $output);
		}

		$pattern = '/<p[^>]*>(.*?)<\/p>/s';
		if (preg_match_all($pattern, $htmlContent, $matches)) $output = $matches[0];
		else return [];
		
		if($trim === true) $output = doTrim($output);

		return $output;
	}

	static function span($htmlContent = ""){
		$output = [];
		$pattern = '/<span[^>]*>(.*?)<\/span>/s';
		if (preg_match_all($pattern, $htmlContent, $matches)) $output = $matches[0];
		else return [];
		return $output;
	}

	static function getAttribute($htmlContent, $attributeName){
		$pattern = "/\s$attributeName=['\"](.*?)['\"]/i";
		if (preg_match_all($pattern, $htmlContent, $matches)) return $matches[1];
		else return [];
	}
	
	// filters
	
	static function filterByText($arr, $text = ""){
		$output = [];
		
		$output = array_filter($arr, function($value) use($text, $output) {
			if(is_string($value) && strpos($value, $text) !== false) return true;
		});

		return array_values($output);
	}
	

	// checks

	static function hasText($htmlContent = "", $text = ""){
		if(strpos($htmlContent, $text) !== false) return true;
		return false;
	}

}
