<?php

namespace App\Plugins\PaymentMethods\Helpers;

use App\Plugins\PaymentMethods\Model\Transaction as ModelTransaction;

class Transaction
{

	/**
	 * Transactions: Get
	 */

	static function transactions() {
		return ModelTransaction::allTransactions()->toArray();
	}

	static function transactionsByStatus(string $status){
		return ModelTransaction::trnsactionsByStatus($status)->toArray();
	}

	static function transactionTotalByStatus(string $status){
		$transations = self::transactionsByStatus($status);
		return array_reduce($transations, function($acc, $transaction){
			$acc += $transaction['product_amount'];
			return $acc;
		}, 0);
	}
	
	static function transactionByUid(string $uid){
		$transaction = ModelTransaction::transactionByUid($uid);
		return !empty($transaction) ? $transaction->toArray() : NULL;
	}

	/**
	 * Transactions: Save
	 */

	static function addTransaction(array $transaction){
		return ModelTransaction::addTransaction($transaction);
	}
	

}
