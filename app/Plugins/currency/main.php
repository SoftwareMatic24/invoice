<?php

use Illuminate\Foundation\AliasLoader;

$loader = AliasLoader::getInstance();
$loader->alias('Currency', App\Plugins\Currency\Helpers\Currency::class);

?>