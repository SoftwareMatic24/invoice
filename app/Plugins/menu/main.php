<?php


use Illuminate\Foundation\AliasLoader;

$loader = AliasLoader::getInstance();
$loader->alias('Menu', \App\Plugins\Menu\Helpers\Menu::class);

?>