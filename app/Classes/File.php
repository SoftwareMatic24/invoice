<?php

namespace App\Classes;
use Exception;

class File
{

	static function guessMediaByContentType($contentType)
	{

		$contentType = strtolower($contentType);

		if ($contentType === "image/jpeg") {
			return [
				"type" => "image",
				"extension" => "jpeg"
			];
		} else if ($contentType === "image/png") {
			return [
				"type" => "image",
				"extension" => "png"
			];
		} else if ($contentType === "image/gif") {
			return [
				"type" => "image",
				"extension" => "gif"
			];
		} else if ($contentType === "image/bmp") {
			return [
				"type" => "image",
				"extension" => "bmp"
			];
		} else if ($contentType === "image/svg+xml") {
			return [
				"type" => "svg",
				"extension" => "svg"
			];
		} else if ($contentType === "image/webp") {
			return [
				"type" => "image",
				"extension" => "webp"
			];
		} else if ($contentType === "image/tiff") {
			return [
				"type" => "image",
				"extension" => "tiff"
			];
		} else if ($contentType === "image/x-icon") {
			return [
				"type" => "image",
				"extension" => "ico"
			];
		} else return NULL;
	}

	// Write
	static function uploadDataFile($filePath,$dataFile){
		try {
			file_put_contents($filePath, $dataFile);
			return true;
		}
		catch(Exception $e){
			return false;
		}
	}

	// Read
	static function readFile($absolutePath, $download = false, $options = []){
		$chunkSize = (1024 * 1024) * 10; // 0.5mb
		$fileSize = filesize($absolutePath);
		$contentDisposition = $download === true ? "attachment" : "inline";

		$start = 0;
		$end = $fileSize - 1;

		if (isset($_SERVER['HTTP_RANGE'])) {
			list(, $range) = explode('=', $_SERVER['HTTP_RANGE']);
			list($start, $end) = explode('-', $range);
			$start = intval($start);
			$end = $end ? intval($end) : $fileSize - 1;
		}

		function getMimeType($absoluteURL){
			return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $absoluteURL);
		}

		function setHeaders($absolutePath, $contentDisposition, $fileSize, $start, $end, $download){
			$basename = basename($absolutePath);

			header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0');
			header('Pragma: no-cache');
			header('Content-Disposition: ' . $contentDisposition . '; filename="' . $basename . '";');
			header('Content-Type: ' . getMimeType($absolutePath));
			header('Content-Length: ' . ($end - $start) + 1);
			header('Accept-Range: bytes');
			if($download){
				header('HTTP/1.1 200 OK');
			}
			else {
				header('HTTP/1.1 206 Partial Content');
				header('Content-Range: bytes ' . $start . '-' . ($end) . '/' . $fileSize);
			}
			
		}
		
		setHeaders($absolutePath, $contentDisposition, $fileSize, $start, $end, $download);
	
		$handle = fopen($absolutePath, 'rb');
		fseek($handle, $start);

		while (!feof($handle) && ($pos = ftell($handle)) <= $end) {
			$chunkSize = min($chunkSize, $end - $pos + 1);
			echo fread($handle, $chunkSize);
			flush();
		}

		fclose($handle);
	}
}
