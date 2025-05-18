<?php

use Illuminate\Foundation\AliasLoader;

$loader = AliasLoader::getInstance();
$loader->alias('NotificationBanner', App\Plugins\NotificationBanner\Helpers\NotificationBanner::class);
?>