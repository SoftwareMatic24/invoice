<?php

use Illuminate\Foundation\AliasLoader;

$loader = AliasLoader::getInstance();
$loader->alias('Setting', App\Plugins\Setting\Helpers\Setting::class);

?>