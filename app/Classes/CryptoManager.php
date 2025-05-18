<?php

namespace App\Classes;

use Exception;
use Illuminate\Support\Str;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\Random;
use phpseclib3\Crypt\RSA;

class CryptoManager
{

	// Asymmetric

	static function generateKeyPair($outputDir, array $options = [])
	{
		$defaultOptions = [
			"keyFormat"=>"PKCS8",
			"keyBits" => 2048,
			"privateKeyName" => NULL,
			"publicKeyName" => NULL
		];

		$options = array_merge($defaultOptions, $options);

		[
			"keyFormat"=>$keyFormat,
			"keyBits" => $keyBits,
			"privateKeyName" => $privateKeyName,
			"publicKeyName" => $publicKeyName
		] = $options;

		$privateKey = RSA::createKey($keyBits);
		$publicKey = $privateKey->getPublicKey();
		
		FS::createFolder([["path" => $outputDir]]);

		$privateFilePath = self::keyFilePath($outputDir, $privateKeyName);
		$publicFilePath = self::keyFilePath($outputDir, $publicKeyName);

		if(file_exists($privateFilePath) || file_exists($publicFilePath)) return NULL;

		file_put_contents($privateFilePath, $privateKey->toString($keyFormat));
		file_put_contents($publicFilePath, $publicKey->toString($keyFormat));

		return [
			"public"=>basename($publicFilePath),
			"private"=>basename($privateFilePath)
		];
	}

	static function encrypt($publicKeyPath, $password = false, $str, $encode = true){
		try {
			$publicKey = self::loadPublicKeyFile($publicKeyPath, $password);
			if(empty($publicKey)) return NULL;
			$output = $hash = $publicKey->encrypt($str);
			if($encode) $output = base64_encode($hash);
			return $output;
		}
		catch(Exception $e){
			return NULL;
		}
	}
	
	static function decrypt($privateKeyPath, $password = false, $str, $encoded = true){
		try {
			$privateKey = self::loadPublicKeyFile($privateKeyPath, $password);
			if(empty($privateKey)) return NULL;
			$output = $str;
			if($encoded) $output = base64_decode($str);
			$output = $privateKey->decrypt($output, );
			return $output;
		}
		catch(Exception $e){
			return NULL;
		}
	}

	// Symmetric

	static function generateSymmetricKey($outputDir, $blockSize = 16, $fileName = NULL){

		FS::createFolder([["path" => $outputDir]]);

		$keyFilePath = self::keyFilePath($outputDir, $fileName, "bin");
		if(file_exists($keyFilePath)) return NULL;

		file_put_contents($keyFilePath, Random::string($blockSize));

		return ["private"=>basename($keyFilePath)];
	}

	static function symmetricEncrypt($keyPath, $str, $iv = false, $mode = "ecb", $encode = true){
		if(!file_exists($keyPath)) return NULL;

		try {
			$keyContent = file_get_contents($keyPath);
			$cipher = new AES($mode);
			if(!empty($iv)) $cipher->setIV($iv);
			$cipher->setKey($keyContent);
			$output = $cipher->encrypt($str);
			if($encode) $output = base64_encode($output);
			return $output;
		}
		catch(Exception $e){
			return NULL;
		}	
	}

	static function symmetricDecrypt($keyPath, $str, $iv = false, $mode = "ecb", $encoded = true){

		if(!file_exists($keyPath)) return NULL;
		if($encoded) $str = base64_decode($str);

		try {
			$keyContent = file_get_contents($keyPath);
			$cipher = new AES($mode);
			if(!empty($iv)) $cipher->setIV($iv);
			$cipher->setKey($keyContent);
			return $cipher->decrypt($str);
		}
		catch(Exception $e){
			return NULL;
		}	
	}


	// Private

	private static function loadPublicKeyFile($path, $password = false){
		if(!file_exists($path)) return NULL;
		$content = file_get_contents($path);
		return PublicKeyLoader::load($content, $password);
	}

	private static function keyFilePath($outputDir, $keyFileName, $ext = "key")
	{
		if (empty($keyFileName)) $keyFileName = Str::uuid()->toString().".$ext";
		return "$outputDir/$keyFileName";
	}
}
