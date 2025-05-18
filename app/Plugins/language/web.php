<?php

use App\Plugins\Language\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

Route::get('/language/switch/{code}', [LanguageController::class, 'switchLanguageRequest']);

?>