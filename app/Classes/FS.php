<?php

namespace App\Classes;
use Exception;

class FS
{

	static function writeFile($absolutePath, $data){
		$file = fopen($absolutePath, "a");
		fwrite($file, $data);
		fclose($file);
	}

	static function createFolder($arr){
		$folders = [];
		$error = NULL;

		try {
			foreach($arr as $row){
				$path = $row["path"] ?? NULL;
				$permission = $row["permission"] ?? 0755;
	
				$path = str_replace("\\","/",$path);
				$path = str_replace("//","/",$path);
				$chunks = explode("/", $path);
				
				$accumulator = [];
				foreach($chunks as $chunk){
					try {
						$accumulator[] = $chunk;
						$chunkPath = implode("/",$accumulator);
						if(is_dir($chunkPath) || trim($chunkPath) == "") continue;
						$folders[] = $chunkPath;
						mkdir($chunkPath);
						chmod($chunkPath, $permission);
					}
					catch(Exception $e){}
				}
			}
		}
		catch(Exception $e){
			$error = $e->getMessage();
		}

		return ["folders"=>$folders, "error"=>$error];
	}

	static function deleteFolder($array)
	{
		$error = NULL;

		try {
			foreach ($array as $directory) {
				$path = $directory["path"] ?? NULL;
				if ($path !== NULL && is_dir($path)) {
					self::deleteFilesInFolder($path);
					rmdir($path);
				}
			}
		} catch (Exception $e) {
			$error = $e->getMessage();
		}

		if ($error === NULL) return ["status" => "success", "msg" => "Folder(s) deleted."];
		else return ["status" => "fail", "msg" => $error];
	}

	static function deleteFilesInFolder($absolutePath)
	{
		$files = glob($absolutePath . '/*');
		foreach ($files as $file) {
			if (is_file($file)) {
				unlink($file);
			} elseif (is_dir($file)) {
				self::deleteFilesInFolder($file);
			}
		}
	}
}
