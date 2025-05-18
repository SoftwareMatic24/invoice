<?php

namespace App\Http\Controllers;

use App\Models\Lock;
use Illuminate\Http\Request;

class LockController extends Controller
{

	function lockStatus($lockSlug){
		return Lock::lockStatus($lockSlug);
	}

}
