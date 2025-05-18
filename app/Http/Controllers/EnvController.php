<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EnvController extends Controller
{

	private $classification = [
		"MAIL" => [
			"MAIL_MAILER",
			"MAIL_HOST",
			"MAIL_PORT",
			"MAIL_USERNAME",
			"MAIL_ENCRYPTION",
			"MAIL_FROM_ADDRESS",
			"MAIL_FROM_NAME"
		]
	];


	/**
	 * ===== Mail
	 */

	function getMailData()
	{
		return $this->getParticularData("MAIL");
	}

	function updateMailDataRequest(Request $request)
	{

		$data = $request->post();

		$this->updateMailData($data);

		return Response()->json([
			"status" => "success",
			"msg" => "Mail details updated"
		]);
	}

	function updateMailData($data)
	{

		$envData = $this->readEnvFile();

		$password = $data["MAIL_PASSWORD"] ?? "";

		if ($password == "") unset($data["MAIL_PASSWORD"]);

		$error = NULL;

		foreach ($data as $key => $value) {

			$matches = NULL;
			preg_match("/[$]{.*}/i", $value, $matches);

			if ($matches != NULl && sizeof($matches) > 0) {
				$matchedValue = $matches[0];
				$matchedValue = str_replace(["$", "{", "}"], "", $matchedValue);
				if ($matchedValue !== "APP_NAME") $error = $value . " value is not allowed";
			}

			$value = str_replace("\"", "", $value);

			$envData[$key] = $value;
		}

		if ($error !== NULL) {

			return Response()->json([
				"status" => "fail",
				"msg" => $error
			]);
		}

		$this->writeEnvFile($envData);
	}

	function resetMailData()
	{

		$defaultMailData = $this->getParticularData("MAIL", "DEFAULT_");
		$defaultMailPassword = $this->readEnvFile()["DEFAULT_MAIL_PASSWORD"];

		$mailData = [];
		foreach ($defaultMailData as $key => $value) {

			$newKey = str_replace("DEFAULT_", "", $key);
			$mailData[$newKey] = $value;
		}

		$mailData["MAIL_PASSWORD"] = $defaultMailPassword;

		$this->updateMailData($mailData);

		return Response()->json([
			"status" => "success",
			"msg" => "Mail data is reset.",
			"reload" => true
		]);
	}


	/**
	 * ===== Secret
	 */


	function updateSecretKey($secret)
	{

		$data = ["APP_SECRET" => $secret];

		$envData = $this->readEnvFile();


		$error = NULL;

		foreach ($data as $key => $value) {

			$matches = NULL;
			preg_match("/[$]{.*}/i", $value, $matches);

			if ($matches != NULl && sizeof($matches) > 0) {
				$matchedValue = $matches[0];
				$matchedValue = str_replace(["$", "{", "}"], "", $matchedValue);
				if ($matchedValue !== "APP_NAME") $error = $value . " value is not allowed";
			}

			$value = str_replace("\"", "", $value);

			$envData[$key] = $value;
		}

		if ($error !== NULL) {

			return Response()->json([
				"status" => "fail",
				"msg" => $error
			]);
		}

		$this->writeEnvFile($envData);
	}

	function updateAppIdAndSecretKey($appId, $secret)
	{

		$data = ["APP_SECRET" => $secret, "APP_ID" => $appId];

		$envData = $this->readEnvFile();

		$error = NULL;

		foreach ($data as $key => $value) {

			$matches = NULL;
			preg_match("/[$]{.*}/i", $value, $matches);

			if ($matches != NULl && sizeof($matches) > 0) {
				$matchedValue = $matches[0];
				$matchedValue = str_replace(["$", "{", "}"], "", $matchedValue);
				if ($matchedValue !== "APP_NAME") $error = $value . " value is not allowed";
			}

			$value = str_replace("\"", "", $value);

			$envData[$key] = $value;
		}

		if ($error !== NULL) {

			return Response()->json([
				"status" => "fail",
				"msg" => $error
			]);
		}

		$this->writeEnvFile($envData);
	}
	function updateLC($lc)
	{
		$data = ["APP_LC"=>$lc];

		$envData = $this->readEnvFile();

		$error = NULL;

		foreach ($data as $key => $value) {

			$matches = NULL;
			preg_match("/[$]{.*}/i", $value, $matches);

			if ($matches != NULl && sizeof($matches) > 0) {
				$matchedValue = $matches[0];
				$matchedValue = str_replace(["$", "{", "}"], "", $matchedValue);
				if ($matchedValue !== "APP_NAME") $error = $value . " value is not allowed";
			}

			$value = str_replace("\"", "", $value);

			$envData[$key] = $value;
		}

		if ($error !== NULL) {

			return Response()->json([
				"status" => "fail",
				"msg" => $error
			]);
		}

		$this->writeEnvFile($envData);
	}


	/**
	 * ===== Logic
	 */

	function getParticularData($classificationGroup, $prefix = "")
	{

		$requiredData = [];

		$requiredAttributes = $this->classification[$classificationGroup] ?? NULL;
		if ($requiredAttributes === NULL) return $requiredData;

		if ($prefix != "") {
			$newRequiredAttributes = [];
			foreach ($requiredAttributes as $key => $value) {
				$newRequiredAttributes[] = $prefix . $value;
			}
			$requiredAttributes = $newRequiredAttributes;
		}

		$fileData = $this->readEnvFile();

		foreach ($fileData as $key => $value) {
			if (in_array($key, $requiredAttributes)) $requiredData[$key] = $value;
		}

		return $requiredData;
	}

	function readEnvFile($name = ".env")
	{

		$fileData = [];

		$basePath = base_path($name);

		if (!file_exists($basePath)) {
			return [
				"status" => "fail",
				"msg" => ErrorController::getMessage(SlugController::$FILE_NOT_FOUND)
			];
		}

		$file = fopen($basePath, "r");

		$lineIndex = 0;
		while (!feof($file)) {
			$line = fgets($file);
			$firstChar = $line[0] ?? NULL;

			if ($firstChar !== NULL && $firstChar !== " " && $firstChar !== "#") {

				$chunks = explode("=", $line, 2);

				$key = $chunks[0] ?? NULL;
				$value = $chunks[1] ?? NULL;

				$value = ltrim($value, "\"");
				$value = rtrim($value, "\"\n");

				if ($firstChar == "\n") $fileData[$lineIndex] = "##NEWLINE##";
				else $fileData[$key] = $value;
			}

			$lineIndex++;
		}

		fclose($file);

		return $fileData;
	}

	function writeEnvFile($data, $name = ".env")
	{

		$basePath = base_path($name);

		if (!file_exists($basePath)) {
			return [
				"status" => "fail",
				"msg" => ErrorController::getMessage(SlugController::$FILE_NOT_FOUND)
			];
		}

		$file = fopen($basePath, "w");

		$newFileStr = "";
		foreach ($data as $key => $value) {

			$originalValue = $value;

			$line = $key . "=" . "\"" . $value . "\"";

			if ($originalValue === "##NEWLINE##") $line = "\n";
			else $line .= "\n";


			$newFileStr .= $line;
		}

		fwrite($file, $newFileStr);
		fclose($file);
	}
}
