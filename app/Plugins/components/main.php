<?php

use Illuminate\Foundation\AliasLoader;

$loader = AliasLoader::getInstance();
$loader->alias('Component', App\Plugins\Components\Helpers\Component::class);

?>