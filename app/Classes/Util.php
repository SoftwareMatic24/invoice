<?php

namespace App\Classes;

use DateTime;

class Util
{

	static function prefixedURL($relativeURL)
	{
		$prefix = self::prefixedRelativeURL($relativeURL);

		return url('/') . $prefix;
	}

	static function prefixedRelativeURL($relativeURL)
	{
		$prefix = config('app.portal_prefix');
		if (strlen($prefix) > 0 && $prefix[0] !== "/" && $prefix[0] !== "\\") $prefix = "/" . $prefix;
		if (strlen($relativeURL) > 0 && $relativeURL[0] !== "/" && $relativeURL[0] !== "\\") $relativeURL = "/" . $relativeURL;

		return $prefix . $relativeURL;
	}

	static function themeURL($postFix, $urlLangCode, $primaryLangCode)
	{
		if (substr($postFix, 0, 1) === "/") $postFix = substr($postFix, 1);
		if (($primaryLangCode ?? NULL) === $urlLangCode) return url("/" . $postFix);
		return url($urlLangCode . "/" . $postFix);
	}

	static function subfolder(){
		$subfolder = env("APP_SUBFOLDER", NULL);
		if(empty($subfolder) || empty(trim($subfolder)) || $subfolder === "\\" || $subfolder === "/") return "";
		return $subfolder;
	}

	static function calculatePercentage($number, $percent)
	{
		$percentage = ($percent / 100) * $number;
		return $percentage;
	}

	static function removeQuotes($str)
	{

		if ($str == NULL || $str === false) $str = "";

		$rectifiedStr = str_replace("\"", "", $str);
		$rectifiedStr = str_replace("'", "", $rectifiedStr);
		return $rectifiedStr;
	}

	static function generateFAQSchemaJsonLd($questions = [])
	{

		if ($questions === NULL || $questions === false) return;

		$schema = "";
		$layouts = [];
		foreach ($questions as $question) {
			$questionText = self::removeQuotes($question["question"]);
			$answerText = self::removeQuotes($question["answer"]);
			$layout = "{";
			$layout .= "\"@type\": \"Question\",";
			$layout .= "\"name\": \"$questionText\",";
			$layout .= "\"acceptedAnswer\": {";
			$layout .= "\"@type\": \"Answer\",";
			$layout .= "\"text\": \"<p>$answerText</p>\"";
			$layout .= "}";
			$layout .= "}";
			$layouts[] = $layout;
		}

		if (count($layouts) > 0) {
			$joinedLayouts = implode(",", $layouts);
			$schema .= "<!-- FAQ Schema JSON LD -->\n";
			$schema .= "	<script type=\"application/ld+json\">";
			$schema .= "{";
			$schema .= "\"@context\": \"https://schema.org\",";
			$schema .= "\"@type\": \"FAQPage\",";
			$schema .= "\"mainEntity\": [$joinedLayouts]";
			$schema .= "}";
			$schema .= "</script>";
		}

		return $schema;
	}

	static function generateSociaMetaTags($meta = NULL)
	{
		if ($meta === NULL) return;

		$og = $meta["og"] ?? NULL;
		$twitter = $meta["twitter"] ?? NULL;

		$ogLayout = self::generateOGMetaTags($og);
		$twitterLayout = self::generateTwitterMetaTags($twitter);

		return $ogLayout . $twitterLayout;
	}

	static function generateOGMetaTags($data)
	{
		if ($data === NULL) return "";

		$title = $data["ogTitle"] ?? "";
		$description = $data["ogDescription"] ?? "";
		$type = $data["ogType"] ?? "";
		$imageURL = (isset($data["ogImageURL"]) && $data["ogImageURL"] !== "" && $data["ogImageURL"] !== NULL) ? url("storage/" . $data["ogImageURL"]) : NULL;
		$url = request()->url();

		$layout = "\n";
		$layout .= "	<!-- OG Meta Tags -->";
		$layout .= "\n";
		$layout .= "	<meta property=\"og:title\" content=\"$title\">\n";
		$layout .= "	<meta property=\"og:description\" content=\"$description\">\n";
		$layout .= "	<meta property=\"og:type\" content=\"$type\">\n";
		$layout .= "	<meta property=\"og:url\" content=\"$url\">\n";
		if ($imageURL !== NULL) $layout .= "	<meta property=\"og:image\" content=\"$imageURL\">\n";


		return $layout;
	}

	static function generateTwitterMetaTags($data)
	{
		if ($data === NULL) return "";

		$title = $data["twitterTitle"] ?? "";
		$description = $data["twitterDescription"] ?? "";
		$card = $data["twitterCard"] ?? "";
		$imageURL = (isset($data["twitterImageURL"]) && $data["twitterImageURL"] !== "" && $data["twitterImageURL"] !== NULL) ? url("storage/" . $data["twitterImageURL"]) : NULL;

		$layout = "\n";
		$layout .= "	<!-- Twitter Meta Tags -->";
		$layout .= "\n";
		$layout .= "	<meta property=\"twitter:title\" content=\"$title\">\n";
		$layout .= "	<meta property=\"twitter:description\" content=\"$description\">\n";
		$layout .= "	<meta property=\"twitter:card\" content=\"$card\">\n";
		if ($imageURL !== NULL) $layout .= "	<meta property=\"twitter:image\" content=\"$imageURL\">\n";


		return $layout;
	}


	static function getBrowserName($userAgent)
	{
		$browser = "";
		if (preg_match('/MSIE/i', $userAgent) && !preg_match('/Opera/i', $userAgent)) {
			$browser = 'Internet Explorer';
		} elseif (preg_match('/Edge|Edg/i', $userAgent)) {
			$browser = 'Microsoft Edge';
		} elseif (preg_match('/Firefox/i', $userAgent)) {
			$browser = 'Mozilla Firefox';
		} elseif (preg_match('/Chrome/i', $userAgent) && !preg_match('/Edg/i', $userAgent)) {
			$browser = 'Google Chrome';
		} elseif (preg_match('/Safari/i', $userAgent) && !preg_match('/Chrome/i', $userAgent)) {
			$browser = 'Safari';
		} elseif (preg_match('/Opera|OPR/i', $userAgent)) {
			$browser = 'Opera';
		} elseif (preg_match('/Netscape/i', $userAgent)) {
			$browser = 'Netscape';
		} else {
			$browser = 'Unknown';
		}

		return $browser;
	}

	static function getDeviceType($userAgent)
	{
		$deviceType = "";
		if (preg_match('/mobile|android|iphone|ipod|blackberry|iemobile|opera mini/i', $userAgent)) {
			$deviceType = 'mobile';
		} elseif (preg_match('/ipad/i', $userAgent)) {
			$deviceType = 'tablet';
		} else {
			$deviceType = 'desktop';
		}
		return $deviceType;
	}

	static function batchArray($array, $batchSize)
	{
		$batches = [];
		$batch = [];

		foreach ($array as $item) {
			$batch[] = $item;

			if (count($batch) == $batchSize) {
				$batches[] = $batch;
				$batch = [];
			}
		}

		if (!empty($batch)) {
			$batches[] = $batch;
		}

		return $batches;
	}

	static function convertToYouTubeEmbedLink($url)
	{
		$youtubePattern = '/^(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})$/';

		if (preg_match($youtubePattern, $url, $matches)) {
			$videoId = $matches[1];
			$embedLink = "https://www.youtube.com/embed/$videoId";

			return $embedLink;
		}

		$embedPattern = '/^(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/embed\/)([^"&?\/\s]{11})$/';
		if (preg_match($embedPattern, $url, $matches)) {
			return $url;
		}

		return $url;
	}

	static function fullName($firstName, $lastName)
	{
		$name = [];
		if ($firstName !== NULL) $name[] = $firstName;
		if ($lastName !== NULL) $name[] = $lastName;
		return implode(" ", $name);
	}

	static function firstLastNames($fullName)
	{

		$output = [
			"firstName" => NULL,
			"lastName" => NULL
		];

		if (!is_string($fullName)) $fullName = "";
		$fullName = trim($fullName);

		$chunks = explode(" ", $fullName);
		if (sizeof($chunks) >= 1) $output["firstName"] = array_shift($chunks);
		if (sizeof($chunks) >= 1) $output["lastName"] = implode("-", $chunks);

		return $output;
	}

	static function capitalizeAll($string, $smartCapitalize = false)
	{
		$ignore = ['and', 'for', 'by', 'the', 'was', 'is', 'a'];

		$output = [];
		if ($string == null || $string == false || !isset($string)) $string = '';
		$chunks = explode(' ', $string);

		foreach ($chunks as $wordIndex => $word) {
			if (in_array(strtolower($word), $ignore) && $smartCapitalize === false) {
				$output[] = ucfirst($word);
			} elseif ($wordIndex !== 0 && in_array(strtolower($word), $ignore) && $smartCapitalize === true) {
				$output[] = strtolower($word);
			} else {
				$output[] = ucfirst($word);
			}
		}
		return implode(' ', $output);
	}

	static function cookieString(array $cookies): string
	{
		$cookieParts = [];
		foreach ($cookies as $key => $value) {
			$cookieParts[] = urlencode($key) . '=' . urlencode($value);
		}
		return implode('; ', $cookieParts);
	}

	static function excerpt($str, $length = 30)
	{
		$str = strip_tags($str);
		if (strlen($str) > $length) return substr($str, 0, $length) . "...";
		return $str;
	}

	static function hasDuplicates($array)
	{
		$counts = array_count_values($array);
		foreach ($counts as $value => $count) {
			if ($count > 1) return true;
		}
		return false;
	}

	static function strLengthInRange($str, $start = 0, $end)
	{
		if (!is_string($str)) return false;

		if (strlen($str) >= $start && strlen($str) <= $end) return true;
		return false;
	}

	static function calculateAge($nowStr, $birthDateStr, $format)
	{

		$birthDate = DateTime::createFromFormat($format, $birthDateStr);
		$currentDate = DateTime::createFromFormat($format, $nowStr);

		if (!$birthDate || !$currentDate) return NULL;

		$ageInterval = $currentDate->diff($birthDate);

		return [
			'years' => $ageInterval->y,
			'months' => $ageInterval->m,
			'days' => $ageInterval->d,
		];
	}
	
	
}
