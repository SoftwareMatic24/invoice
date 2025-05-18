<?php

namespace App\Classes;

class ExtProvider {

	static function ipDetails($ip){
		$URL = "http://ip-api.com/json/$ip";
		$curl = curl_init($URL);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		return $response;
	}

}

?>