<?php

use App\Services\ResetService;

class Reset {

	function __construct()
	{
		$this->resetService = new ResetService();
	}

	// Get

	static function settings(){
		return (new Self())->resetService->resetSettings();
	}

	// Util

	static function hasActiveResets(){
		return (new self())->resetService->hasActiveResets();
	}

	static function isNotificationVisible(){
		return (new Self())->resetService->isNotificationVisible();
	}

	// Build

	static function resetNotificationView(){
		return (new self())->resetService->buildResetNotificationView();
	}

}

?>