<?php

// String

use App\Classes\Util;

if (!function_exists('strContains')) {
	function strContains($needle, $haystack)
	{
		if (strpos($haystack, $needle) !== false) return true;
		return false;
	}
}

if (!function_exists('strContainsAny')) {
	function strContainsAny($str, $arr)
	{
		$match = false;
		foreach ($arr as $value) {
			if (strContains($value, $str)) $match = true;
		}
		return $match;
	}
}

if (!function_exists('fullName')) {
	function fullName($firstName, $lastName)
	{
		$name = [];
		if ($firstName !== NULL) $name[] = $firstName;
		if ($lastName !== NULL) $name[] = $lastName;
		return implode(" ", $name);
	}
}

// Arrays

if (!function_exists('arrayFind')) {
	function arrayFind($needle, $haystack, $key = NULL)
	{

		$match = array_filter($haystack, function ($value) use ($needle, $key) {
			if (empty($key) && $value == $needle) return true;
			else if (!empty($key) && $value[$key] == $needle) return true;
		});

		if (sizeof($match) <= 0) return NULL;
		return reset($match);
	}
}

if (!function_exists('pathJoin')) {
	function pathJoin(...$paths)
	{
		$path = NULL;
		foreach ($paths as $p) {
			$p = rtrim($p, '/\\');
			if (empty($p)) continue;
			$path .= $p . DIRECTORY_SEPARATOR;
		}

		$path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
		$path = rtrim($path, '/\\');
		return $path;
	}
}

if (!function_exists('urlJoin')) {
	function urlJoin(...$paths)
	{
		$paths = array_map(function ($p) {
			return trim($p, '/\\');
		}, $paths);
		$path = pathJoin(...$paths);
		return str_replace('\\', '/', $path);
	}
}


// Files

if (!function_exists('loadFile')) {

	function loadFile($path, $options = NULL)
	{
		$rawContent = $options["rawContent"] ?? false;
		$file = base_path($path);

		if (!file_exists($file)) return NULL;

		$fileExtension = pathinfo($path, PATHINFO_EXTENSION);
		$content = file_get_contents($file);

		if ($fileExtension === "js" && $rawContent === false) $content = "<script>$content</script>";
		else if ($fileExtension === "css" && $rawContent === false) $content = "<style>$content</style>";
		else if ($rawContent === true) return $content;

		return $content;
	}
}

if (!function_exists('getMimeType')) {
	function getMimeType($name)
	{
		$mimeTypes = [
			'jpg'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'png'  => 'image/png',
			'gif'  => 'image/gif',
			'bmp'  => 'image/bmp',
			'webp' => 'image/webp',
			'svg'  => 'image/svg+xml',
			'pdf'  => 'application/pdf',
			'doc'  => 'application/msword',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'xls'  => 'application/vnd.ms-excel',
			'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'ppt'  => 'application/vnd.ms-powerpoint',
			'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'txt'  => 'text/plain',
			'csv'  => 'text/csv',
			'json' => 'application/json',
			'xml'  => 'application/xml',
			'zip'  => 'application/zip',
			'rar'  => 'application/x-rar-compressed',
			'tar'  => 'application/x-tar',
			'gz'   => 'application/gzip',
			'mp3'  => 'audio/mpeg',
			'wav'  => 'audio/wav',
			'mp4'  => 'video/mp4',
			'avi'  => 'video/x-msvideo',
			'mov'  => 'video/quicktime',
			'flv'  => 'video/x-flv',
			'wmv'  => 'video/x-ms-wmv',
			'ogg'  => 'audio/ogg',
			'mkv'  => 'video/x-matroska',
			'html' => 'text/html',
			'css'  => 'text/css',
			'js'   => 'application/javascript',
			'php'  => 'application/x-httpd-php',
		];

		$extension = pathinfo(parse_url($name, PHP_URL_PATH), PATHINFO_EXTENSION);

		return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
	}
}

if(!function_exists('isImageUrl')){
	function isImageUrl($name){
		$mimeType = getMimeType($name);
		return !empty($mimeType) && strContains('image/',$mimeType);
	}
}

if(!function_exists('isVideoUrl')){
	function isVideoUrl($name){
		$mimeType = getMimeType($name);
		return !empty($mimeType) && strContains('video/',$mimeType);
	}
}

if(!function_exists('isPdfUrl')){
	function isPdfUrl($name){
		$mimeType = getMimeType($name);
		return !empty($mimeType) && strContains('application/pdf',$mimeType);
	}
}

if(!function_exists('isTextUrl')){
	function isTextUrl($name){
		$mimeType = getMimeType($name);
		return !empty($mimeType) && strContains('text/plain',$mimeType);
	}
}

// URLs

if (!function_exists('prefixedUrl')) {

	function prefixedUrl($path,)
	{
		return Util::prefixedURL($path);
	}
}

// Exceptions

if (!function_exists('MySQLExceptionMessage')) {
	function MySQLExceptionMessage($errorCode, $attribute = NULL)
	{
		if (!isset($errorCode) || $errorCode === NULL) return ['heading' => 'Error Occured', 'description' => 'Something unexpected occured.'];
		else if ($errorCode == 23000) return empty($attribute) ? ['heading' => 'Duplicate Entry', 'description' => 'Value already exists.'] :  ['heading' => 'Duplicate Entry', 'description' => $attribute . ' already exists.'];
		else return ['heading' => 'Error Occured', 'description' => 'Something unexpected occured.'];
	}
}
