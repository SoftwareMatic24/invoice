<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class ShortCodeController extends Controller
{	
	
	function extractShortCode($str){
		$matches = [];
		preg_match_all('/\[(.*?)\]/', $str, $matches);
		return $matches[0];
	}

	function parseShortCode($str, $other = []){

		$textShortCodes = $other["textShortCodes"] ?? [];
		$linkShortCodes = $other["linkShortCodes"] ?? [];

		$textShortCodeKeys = array_keys($textShortCodes);
		$linkShortCodeKeys = array_keys($linkShortCodes);
		$linkShortCodeKeysText = [];

		foreach($linkShortCodeKeys as $value){
			$linkShortCodeKeysText[] = $this->extractValueBetweenChars($value, "[", ":");
		}

		$parsedStr = $str;
		$shortCodes = $this->extractShortCode($str);

		foreach($shortCodes as $shortCode){
			$shortCodeValue = NULL;

			if(strpos($shortCode,"[website-link:") !== false) $shortCodeValue = $this->parseWebsiteLinkShortCode($shortCode);
			else if(strpos($shortCode,"[website-link]") !== false) $shortCodeValue = url("/");
			else if(strpos($shortCode,"[website-logo-link]") !== false) $shortCodeValue = $this->parseWebsiteLogoLinkShortCode($shortCode);
			else if(strpos($shortCode,"[app-name]") !== false) $shortCodeValue = $this->parseWebsiteNameShortCode($shortCode);
			else if(strpos($shortCode,"[link:") !== false)  $shortCodeValue = $this->parseCustomLinkShortCode($shortCode);
			else if(strpos($shortCode,"[DateTime:Y]") !== false) $shortCodeValue = date("Y");
			else if(in_array($shortCode, $textShortCodeKeys)) $shortCodeValue = $textShortCodes[$shortCode];

			foreach($linkShortCodeKeysText as $index=>$text){
				$link = $linkShortCodes[$linkShortCodeKeys[$index]];
				if(strpos($shortCode,"[$text:") !== false) $shortCodeValue = $this->parseLinkShortCode($shortCode, $link);
			}
			
			if($shortCodeValue !== NULL) $parsedStr =  str_replace($shortCode, $shortCodeValue, $parsedStr);
		}
		
		return $parsedStr;
	}

	function parseShortCodes($arr, $other = []){
		$output = [];

		foreach($arr as $str){
			$output[] = $this->parseShortCode($str, $other);
		}
		return $output;
	}

	function parseWebsiteLinkShortCode($shortCode){
		$link = url("/");
		$text = $this->extractValueBetweenChars($shortCode, "website-link:", "]");
		if($text === NULL) $text = $link;
		return "<a href='$link'>$text</a>";
	}

	function parseCustomLinkShortCode($shortCode){
		$text = $this->extractValueBetweenChars($shortCode, "link:", "->");
		$link = $this->extractValueBetweenChars($shortCode, "->", "]");
		return "<a href='$link'>$text</a>";
	}

	function parseLinkShortCode($shortCode, $link){
		$text = $this->extractValueBetweenChars($shortCode, ":", "]");
		return "<a href='$link'>$text</a>";
	}

	function parseWebsiteNameShortCode($shortCode){
		$settings = Cache::get("settings");
		if($settings["brand-name"]["column_value"] ?? false) return $settings["brand-name"]["column_value"];
		else return config("app.name");
	}

	function parseWebsiteLogoLinkShortCode($shortCode){
		$settings = Cache::get("settings");
		if($settings["brand-logo"]["column_value"] ?? false) return url("storage/".$settings["brand-logo"]["column_value"]);
		else return NULL;
	}

	function extractValueBetweenChars($text, $startChar, $endChar) {
		$pattern = '/' . preg_quote($startChar) . '(.*?)' . preg_quote($endChar) . '/';
		$matches = [];
	
		if (preg_match($pattern, $text, $matches)) {
			return $matches[1];
		}
	
		return NULL;
	}

	function parseEmailTemplateShortCodes($mailTemplate, $other = []){
		$mailContent = $mailTemplate["content"] ?? "";
		$signatureContent = $mailTemplate["signature"]["content"] ?? "";
		
		$mailTemplate["subject"] = $this->parseShortCode($mailTemplate["subject"], $other);
		$mailContent = $this->parseShortCode($mailContent, $other);
		$signatureContent = $this->parseShortCode($signatureContent, $other);
		
		
		$mailTemplate["content"] = $mailContent;
		$mailTemplate["signature"] = $signatureContent;

		return $mailTemplate;
	}

}
