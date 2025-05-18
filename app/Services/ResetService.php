<?php

namespace App\Services;

use App\Models\Reset;
use App\Models\ResetSetting;
use Exception;
use HTTP;
use Illuminate\Support\Facades\DB;

class ResetService
{

	/**
	 * Reset: Get
	 */

	function reset(null|int|string $id)
	{
		$reset = Reset::resetById($id);
		return !empty($reset) ? $reset->toArray() : NULL;
	}

	function activeResets()
	{
		return Reset::activeResets()->toArray();
	}

	function resetSettings()
	{
		$setting =  ResetSetting::settings();
		return !empty($setting) ? $setting->toArray() : NULL;
	}


	// Util

	function hasActiveResets()
	{
		$resets = $this->activeResets();
		if (empty($resets)) return false;
		return true;
	}

	function isNotificationVisible()
	{
		$settings = $this->resetSettings();
		return ($settings['notification_visibility'] ?? NULL) == true;
	}


	// Build

	function buildResetNotificationView()
	{
		$resets = $this->activeResets();
		if (empty($resets)) return '';

		$message = __('reset-project-notification-description');
		$buttonText = __('reset here');
		$resetUrl = url('/portal/reset');
		$iconUrl = asset('assets/icons.svg#cross');

		$html = <<<HTML
			<div class="inline-notification-container">
				<div class="inline-notification">
				<div class="d-flex align-items-center gap-2">
					<span onclick="hideResetNotification()">
						<svg class="icon icon-cross">
							<use xlink:href="$iconUrl" />
						</svg>
					</span>
					<p>$message</p>
					</div>
					<a href="$resetUrl" class="button button-sm button-primary">
					$buttonText
					</a>
				</div>
			</div>
		HTML;

		return $html;
	}

	/**
	 * Reset: Process
	 */

	function doReset(null|int|string $id)
	{
		$reset = $this->reset($id);
		if (empty($reset)) return HTTP::inBoolArray(false, __('request-failed'), __('error-notification-description'));

		if ($reset['status'] != 'active') return HTTP::inBoolArray(false, __('request-failed'), __('error-notification-description'));

		$this->processResets([$reset]);

		return HTTP::inBoolArray(true, __('delete-notification-heading'), __('delete-notification-description'));
	}

	function doResetAll()
	{
		$resets = $this->activeResets();
		$this->processResets($resets);
		return HTTP::inBoolArray(true, __('delete-notification-heading'), __('delete-notification-description'));
	}

	function processResets(array $resets)
	{
		try {
			DB::beginTransaction();
			foreach ($resets as $reset) {
				$this->processResetTables($reset['tables']);
				DB::table('resets')->where('id', $reset['id'])->update(['status' => 'inactive']);
			}
			DB::commit();
		} catch (Exception $e) {
			DB::rollBack();
		}
	}

	function processResetTables(array $tables)
	{
		foreach ($tables as $table) {
			$this->processResetTable($table);
		}
	}

	function processResetTable(array $table)
	{
		if (!empty($table['conditions'])) {
			foreach ($table['conditions'] as $condition) {
				$relation = DB::table($table['name']);
				if ($condition['type'] == 'where') $relation->where($condition['column'], $condition['value']);
				$relation->delete();
			}
		} else {
			$relation = DB::table($table['name']);
			$relation->delete();
		}
	}

	/**
	 * Reset Settings: Save
	 */

	function updateSetting(string $column, mixed $value){
		return ResetSetting::updateSetting($column, $value);
	}


}
