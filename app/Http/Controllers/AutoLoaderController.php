<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AutoLoaderController extends Controller
{
	function getAutoloadableFiles($dir){
		$files = [];

		$directories = glob($dir . '/*', GLOB_ONLYDIR);
		$files = array_merge($files, glob($dir . '/*.php'));
	
		foreach ($directories as $subDir) {
			$files = array_merge($files, self::getAutoloadableFiles($subDir));
		}
	
		return $files;
	}

	function autoLoad($dir){
		$files = $this->getAutoloadableFiles($dir);
		foreach($files as $file){
			require_once $file;
		}
	}

}
