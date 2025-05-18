<?php

use Illuminate\Foundation\AliasLoader;

$loader = AliasLoader::getInstance();
$loader->alias('Page', App\Plugins\Pages\Helpers\Page::class);

?>