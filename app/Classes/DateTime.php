<?php

namespace App\Classes;

use DateInterval;
use DateTime as OriginalDateTime;
use DateTimeZone;

class DateTime
{
	static public $dateTimeFormat = "Y/m/d h:i:s A";
	static public $dateFormat = "Y/m/d";

	// Format
	static function format($dateTime, $dateTimeFormat = NULL)
	{
		if ($dateTimeFormat === NULL) $dateTimeFormat = self::$dateTimeFormat;
		return date($dateTimeFormat, strtotime($dateTime));
	}

	static function formatDate($date, $format = NULL)
	{
		if ($format === NULL) $format = self::$dateFormat;
		return date($format, strtotime($date));
	}

	static function formatDateFrom($dateStr, $fromFormat, $toFormat)
	{
		$date = OriginalDateTime::createFromFormat($fromFormat, $dateStr);
		return $date->format($toFormat);
	}

	static function formatTime($timeString)
	{
		return date('g:ia', strtotime($timeString));
	}

	static function formatISO8601DateTodmy($dateStr)
	{
		$timestamp = strtotime($dateStr);
		return date("d/m/Y", $timestamp);
	}

	static function formatToHumanReadableDate($dateStr){
		$date = OriginalDateTime::createFromFormat(self::$dateTimeFormat, $dateStr);
		return $date->format('d M, Y');
	}


	static function timeAgoOrDate($dateString1, $dateString2, $daysThreshold)
	{
		$date1 = new OriginalDateTime($dateString1);
		$date2 = new OriginalDateTime($dateString2);

		if ($date1 < $date2) {
			$temp = $date1;
			$date1 = $date2;
			$date2 = $temp;
		}

		$interval = $date1->diff($date2);
		$days = $interval->days;
		$hours = $interval->h + ($days * 24);
		$minutes = $interval->i + ($hours * 60);
		$seconds = $interval->s + ($minutes * 60);

		if ($days < $daysThreshold) {
			if ($seconds < 60) {
				return $seconds . ' seconds ago';
			}
			if ($minutes < 60) {
				return $minutes . ' minutes ago';
			}
			if ($hours < 24) {
				return $hours . ' hours ago';
			}
			if ($days == 1) {
				return '1 day ago';
			}
			return $days . ' days ago';
		} else {
			return $date2->format(self::$dateTimeFormat);
		}
	}





	// Date & Time

	static function getDate($dateFormat = NULL)
	{
		if (empty($dateFormat)) $dateFormat = self::$dateFormat;
		return now()->format($dateFormat);
	}

	static function getDateTime()
	{
		return now()->format(self::$dateTimeFormat);
	}

	static function getDateTimeInTimeZone($timeZone)
	{
		return self::convertTimeZone(self::getDateTime(), config("app.timezone"), $timeZone);
	}

	static function getCurrentTimestampInMilliseconds()
	{
		list($microseconds, $seconds) = explode(' ', microtime());
		return round(($seconds * 1000) + ($microseconds * 1000));
	}

	static function convertTimeZone($dateTimeStr, $fromTimeZone, $toTimeZone)
	{
		$dateTime = OriginalDateTime::createFromFormat(self::$dateTimeFormat, $dateTimeStr);
		
		$dateTime->setTimezone(new DateTimeZone($fromTimeZone));
		$dateTime->setTimezone(new DateTimeZone($toTimeZone));
		return $dateTime->format(self::$dateTimeFormat);
	}

	static function addSeconds($dateTime, $seconds)
	{
		$dateTimeStr = strtotime($dateTime);
		$futureDateTime = $dateTimeStr + ($seconds);
		return date(self::$dateTimeFormat, $futureDateTime);
	}

	static function addMinutes($dateTime, $minutes)
	{
		$seconds = $minutes * 60;
		$dateTimeStr = strtotime($dateTime);
		$futureDateTime = $dateTimeStr + ($seconds);
		return date(self::$dateTimeFormat, $futureDateTime);
	}

	static function addMonths($dateTime, $numberOfMonths)
	{
		$date = OriginalDateTime::createFromFormat(self::$dateTimeFormat, $dateTime);
		$date->add(new DateInterval("P" . "$numberOfMonths" . "M"));
		return $date->format(self::$dateTimeFormat);
	}

	static function diffInMinutes($dateTime1, $dateTime2)
	{
		$dateTime1 = OriginalDateTime::createFromFormat(self::$dateTimeFormat, $dateTime1);
		$dateTime2 = OriginalDateTime::createFromFormat(self::$dateTimeFormat, $dateTime2);
		$interval = $dateTime1->diff($dateTime2);
		$totalMinutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;
		return $totalMinutes;
	}

	static function diffInMonths($startDate, $endDate, $format)
	{
		$start = OriginalDateTime::createFromFormat($format, $startDate);
		$end = OriginalDateTime::createFromFormat($format, $endDate);

		if (!$start || !$end) return NULL;

		$interval = $start->diff($end);
		$months = $interval->y * 12 + $interval->m;
		return $months;
	}

	static function dateTimeLessThan($dateTime1, $dateTime2)
	{
		$dateTime1Str = strtotime($dateTime1);
		$dateTime2Str = strtotime($dateTime2);

		if ($dateTime2Str > $dateTime1Str) return true;
		return false;
	}

	static function hasSameMonth($dateTime, $monthNumber)
	{
		$timestamp = strtotime($dateTime);
		return date("m", $timestamp) == str_pad(floatval($monthNumber), 2, '0', STR_PAD_LEFT);
	}

	static function hasSameYear($dateTime, $year)
	{
		$timestamp = strtotime($dateTime);
		return date("Y", $timestamp) == $year;
	}

	static function lastDayOfMonth($year, $month)
	{
		$date = date_create("$year-$month-01");
		$date->modify('last day of this month');
		return $date->format("d");
	}

	// Other

	static function toMonthName($month)
	{
		$months = [
			"Jan",
			"Feb",
			"Mar",
			"Apr",
			"May",
			"Jun",
			"Jul",
			"Aug",
			"Sept",
			"Oct",
			"Nov",
			"Dec"
		];

		return $months[intval($month) - 1];
	}

	static function yymmddDashedFormat($date)
	{

		$chunks = explode("-", $date);

		$year = $chunks[0];
		$month = $chunks[1];
		$day = $chunks[2];

		$monthName = self::toMonthName($month);

		return $monthName . ' ' . $day . ', ' . $year;
	}

	static function localDateTime($dateTime, $dateOnly = false)
	{
		$fromTz = config("app.timezone");
		$toTz = $_COOKIE["client_timezone"] ?? $fromTz;

		$localDateTime = DateTime::convertTimeZone($dateTime, $fromTz, $toTz);
		return DateTime::format($localDateTime, $dateOnly === true ? self::$dateFormat : self::$dateTimeFormat);
	}
}
