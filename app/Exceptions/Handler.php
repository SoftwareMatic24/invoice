<?php

namespace App\Exceptions;

use App\Http\Controllers\ThemeController;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class Handler extends ExceptionHandler
{

	protected $levels = [];

	protected $dontReport = [];

	protected $dontFlash = [
		'current_password',
		'password',
		'password_confirmation',
	];

	public function register(): void
	{
		$this->reportable(function (Throwable $e) {});
	}

	public function render($request, Throwable $exception){
		if($exception instanceof HttpExceptionInterface && $exception->getStatusCode() == 503){
			return (new ThemeController())->maintenanceModeView();
		}
		return parent::render($request, $exception);
	}

}
