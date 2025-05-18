<?php

namespace App\Plugins\QuickInvoice\Helpers;

use App\Plugins\QuickInvoice\Models\InvoiceClient;

class QuickInvoiceClient {

	static function userClients($userId){
		return InvoiceClient::userClients($userId)->toArray();
	}

	static function userClient($clientId, $userId){
		$client = InvoiceClient::userClient($userId, $clientId);
		return !empty($client) ? $client->toArray() : NULL;
	}

}

?>