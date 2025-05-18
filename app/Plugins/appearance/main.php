<?php

use Illuminate\Foundation\AliasLoader;

$loader = AliasLoader::getInstance();
$loader->alias('Appearance', App\Plugins\Appearance\Helpers\Appearance::class);

?>