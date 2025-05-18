<?php

use Illuminate\Foundation\AliasLoader;

$loader = AliasLoader::getInstance();
$loader->alias('PaymentMethod', App\Plugins\PaymentMethods\Helpers\PaymentMethod::class);
$loader->alias('Transaction', App\Plugins\PaymentMethods\Helpers\Transaction::class);

?>