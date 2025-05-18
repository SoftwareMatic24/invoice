<?php

use App\Plugins\Setting\Controller\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get("/sitemap", [SitemapController::class, "generateSitemapRequest"]);

?>