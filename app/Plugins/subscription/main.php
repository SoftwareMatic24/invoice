<?php

use Illuminate\Foundation\AliasLoader;

$loader = AliasLoader::getInstance();
$loader->alias('Subscription', \App\Plugins\Subscription\Helpers\Subscription::class);


?>