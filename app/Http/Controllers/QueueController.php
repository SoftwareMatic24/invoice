<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class QueueController extends Controller
{
	function processEmailQueue($queue = "email"){
		$command = "queue:work --queue=$queue --max-time=57";
		Artisan::call($command);
	}
}
