<?php

namespace App\Http\Controllers;

use App\Classes\DateTime;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
	/**
	 * Activity Logs
	 */
	
	function getFormattedActivityLogLikeSlug($slug){
		$logs = ActivityLog::getLogsLikeSlug($slug)->toArray();
		return $this->formatActivityLogDetails($logs);
	}

	function addActivityLog($title, $slug, $userId, $data){
		ActivityLog::addActivityLog($title, $slug, $userId, $data);
		return ["status"=>"success", "msg"=>"Activity log added"];
	}

	function writeLog($text, $fileName = "custom.log"){
		$dateTime = DateTime::getDateTime();

		$fileAbsolutePath = base_path("/storage/logs/$fileName");

		$line = "[$dateTime] $text\n";

		$file = fopen($fileAbsolutePath, "a");
		fwrite($file, $line);
		fclose($file);
	}

	// Util

	function formatActivityLogDetails(array $logs){

		return array_map(function($log){
			$temp = $log;
			$details = [];
			foreach($temp["detail"] as $row){
				$details[$row["column_name"]] = $row["column_value"];
			}
			$temp["detail"] = $details;
			return $temp;
		}, $logs);

		return $logs;
	}

}
