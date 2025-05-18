<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
	
	function __construct()
	{
	}

	protected $except = [
		"api/rs/mvc",
	];
}
