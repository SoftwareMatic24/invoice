<?php

namespace App\Http\Controllers;

use App\Classes\DateTime;
use App\Models\CronJob;
use Illuminate\Http\Request;

class CronJobController extends Controller
{	

	/**
	 * Cron Job
	 */

	function updateCronJobStatus($slug, $status){
		CronJob::updateStatus($slug, $status);
	}

	// Util

	function canRunCronJob($slug){
		if($this->isCronJobActive($slug) && $this->isTimeToRunCronJob($slug)) return true;
		return false;
	}

	function isCronJobActive($slug){
		$cronJob = CronJob::getCronJob($slug);
		if(empty($cronJob) || $cronJob->status !== "active") return false;
		return true;
	}

	function isTimeToRunCronJob($slug){
		$cronJob = CronJob::getCronJob($slug);
		if(empty($cronJob) || $cronJob->status !== "active") return false;

		$lastRunDateTime = $cronJob["last_run_date_time"];
		$runEverySeconds = $cronJob["run_every_seconds"];

		if(empty($lastRunDateTime) || empty($runEverySeconds)) return true;

		$nextRunDateTime = DateTime::addSeconds($lastRunDateTime, $runEverySeconds);
		$now = DateTime::getDateTime();

		$canRun = DateTime::dateTimeLessThan($nextRunDateTime, $now);

		if($canRun) return true;
		return false;
	}

}
