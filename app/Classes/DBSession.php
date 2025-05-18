<?php

namespace App\Classes;

use App\Models\DBSession as ModelsDBSession;
use Illuminate\Support\Str;

class DBSession {

	static function get($key){
		$dbSessionId = $_COOKIE["DB_SESSION_ID"] ?? NULL;
		$row = ModelsDBSession::getSessionByUID($dbSessionId);
		if(empty($row)) return NULL;
		return ($row["data"][$key] ?? NULL);
	}

	static function put($key, $value, $expiryDateTime = NULL){
		
		$dbSessionId = $_COOKIE["DB_SESSION_ID"] ?? NULL;
		if(empty($dbSessionId)) $dbSessionId = Str::uuid();

		$row = ModelsDBSession::getSessionByUID($dbSessionId);


		if(empty($row)) ModelsDBSession::addSession($dbSessionId, [$key=>$value], $expiryDateTime);
		else {
			$newData = $row["data"];
			$newData[$key] = $value;
			ModelsDBSession::updateSession($dbSessionId, $newData, $expiryDateTime);
		}
		
		setcookie("DB_SESSION_ID", $dbSessionId, 0, "/");
		return true;
	}

}

?>