<?php

use Illuminate\Foundation\AliasLoader;



$loader = AliasLoader::getInstance();
$loader->alias('QuickInvoiceClient', \App\Plugins\QuickInvoice\Helpers\QuickInvoiceClient::class);
$loader->alias('QuickInvoiceBusiness', \App\Plugins\QuickInvoice\Helpers\QuickInvoiceBusiness::class);
$loader->alias('QuickInvoiceDocument', \App\Plugins\QuickInvoice\Helpers\QuickInvoiceDocuments::class);
$loader->alias('QuickInvoiceExpense', \App\Plugins\QuickInvoice\Helpers\QuickInvoiceExpense::class);
$loader->alias('QuickInvoiceProduct', \App\Plugins\QuickInvoice\Helpers\QuickInvoiceProduct::class);

?>