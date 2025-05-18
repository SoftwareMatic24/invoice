<?php

use Illuminate\Foundation\AliasLoader;

$loader = AliasLoader::getInstance();
$loader->alias('SocialMedia', App\Plugins\SocialMedia\Helpers\SocialMedia::class);

?>