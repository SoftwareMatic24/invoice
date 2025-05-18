<?php

use Illuminate\Foundation\AliasLoader;

$loader = AliasLoader::getInstance();
$loader->alias('ContactMessage', App\plugins\ContactMessage\Helpers\ContactMessage::class);

?>