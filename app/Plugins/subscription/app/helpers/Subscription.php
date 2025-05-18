<?php

namespace App\Plugins\Subscription\Helpers;

use App\Plugins\Subscription\Model\SubscriptionPackage;
use App\Plugins\Subscription\Model\SubscriptionPackageClassification;
use App\Plugins\Subscription\Model\SubscriptionSubscribableRole;
use App\Plugins\Subscription\Model\SubscriptionSubscribers;

class Subscription
{


	/**
	 * Package
	 */

	static function package($id)
	{
		$package = SubscriptionPackage::getPackage($id);
		return !empty($package) ? $package->toArray() : NULL;
	}

	static function packages()
	{
		return SubscriptionPackage::getPackages()->toArray();
	}
	
	/**
	 * Classification
	 */

	static function classifications()
	{
		return SubscriptionPackageClassification::classifications()->toArray();
	}

	static function classificationBySlug($slug)
	{
		$classification = SubscriptionPackageClassification::classificationBySlug($slug);
		return empty($classification) ? NULL : $classification->toArray();
	}

	/**
	 * Subscribers
	 */

	static function subscriberByUserId($userId){
		$subscriber = SubscriptionSubscribers::getSubscriber($userId);
		return !empty($subscriber) ? $subscriber->toArray() : NULL;
	}

	static function subscribableRoles(){
		$roles = SubscriptionSubscribableRole::getRoles()->toArray();
		return array_column($roles, 'role_title');
	}

}
