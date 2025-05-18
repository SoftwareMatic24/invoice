<?php

namespace App\Classes;

class FileLogger {
	static function write($str = NULL, $absolutePath = NULL){
		if(empty($absolutePath)) return ["status"=>"fail", "msg"=>"Absolute path to file is required."];

		$dateTime = DateTime::getDateTime();
		$content = "[$dateTime] $str\n";

		$file = fopen($absolutePath, "a");
		fwrite($file, $content);
		fclose($file);
	}
}

?>