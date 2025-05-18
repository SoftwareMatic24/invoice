<?php

namespace App\Plugins\Currency\Controller;


use App\Classes\Constants;
use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Plugins\Currency\Model\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use HTTP;
use Illuminate\Support\Facades\App;

class CurrencyController extends Controller
{

	function saveCurrencyView($currencyId = NULL)
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$allCurrencies = Constants::$currencies;

		$pageData = [
			"tabTitle" => empty($currencyId) ? __('new currency') : __('update currency'),
			"backURL" => Util::prefixedURL($config["slug"] . "/manage"),
			"pageName" => empty($currencyId) ? __('new currency') : __('update currency'),
			"pageSlug" => "currency-save",
			"pluginConfig" => $config,
			"currencyId" => $currencyId,
			"allCurrencies" => $allCurrencies
		];

		return PluginController::loadView(__DIR__, "save-currency.blade.php", $pageData);
	}

	function manageCurrencyView()
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => __('manage currencies'),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __('manage currencies'),
			"pageSlug" => "currency-manage",
			"pluginConfig" => $config
		];

		return PluginController::loadView(__DIR__, "manage-currencies.blade.php", $pageData);
	}

	/**
	 * Currency: Get
	 */

	function currencies()
	{
		return Currency::getCurrencies();
	}

	function currency($currencyId)
	{
		return Currency::getCurrency($currencyId);
	}

	function selectedCurrency()
	{
		$currencyId = $_COOKIE["currency"] ?? NULL;
		$selectedCurrency = NULL;

		if ($currencyId === NULL) $selectedCurrency = $this->primaryCurrency();
		else $selectedCurrency = Currency::getCurrency($currencyId);

		if ($selectedCurrency === NULL) $selectedCurrency = $this->primaryCurrency();
		if ($selectedCurrency instanceof Model) $selectedCurrency = $selectedCurrency->toArray();

		return $selectedCurrency;
	}

	function primaryCurrency()
	{
		$currencies = Currency::getCurrencies();
		$currencies = $currencies->toArray();
		$primaryCurrency = array_filter($currencies, function ($currency) {
			if ($currency["type"] === "primary") return true;
		});
		$primaryCurrency = reset($primaryCurrency);
		return $primaryCurrency;
	}

	/**
	 * Currency: Save
	 */

	function saveCurrency($currencyId = NULL, $data)
	{

		$validator = Validator::make($data, [
			"currency" => "required|max:255",
			"symbol" => "required|max:20",
			"rate" => "required|numeric",
			"type" => "required|in:primary,secondary"
		], [
			'currency.required' => __('currency-field-required'),
			"symbol.required" => __('symbol-field-required'),
			"type.required" => __('type-field-required')
		]);

		if ($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}

		$oldCurrency = Currency::getCurrency($data["currency"]);

		if ($currencyId === NULL) {

			if ($oldCurrency !== NULL) {
				return HTTP::inBoolArray(false, __('request-failed'), __('currency-already-exists'));
			}

			$currency = Currency::addCurrency($data);
			$currencyId = $currency->id ?? NULL;
		} else {
			if ($oldCurrency !== NULl && $oldCurrency["id"] != $currencyId) if ($oldCurrency !== NULL) {
				return HTTP::inBoolArray(false, __('request-failed'), __('currency-already-exists'));
			}
			Currency::updateCurrency($currencyId, $data);
		}

		App::make("clearCachedData");

		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notifiction-description'));
	}


	// Request

	function saveCurrencyRequest(Request $request, $currencyId = NULL)
	{
		$data = $request->post();
		$response =  $this->saveCurrency($currencyId, $data);
		return HTTP::inStringResponse($response);
	}


	/**
	 * Currency: Delete
	 */

	function deleteCurrency($currencyId)
	{

		$currency = $this->currency($currencyId);
		if(empty($currency)) return HTTP::inBoolArray(false, __('not-found-notification-heading'), __('not-found-notification-description'));

		if($currency->type === 'primary') return HTTP::inBoolArray(false, __('request-failed'), __('primary-currency-not-deletable'));

		Currency::deleteCurrency($currencyId);

		App::make("clearCachedData");

		return HTTP::inBoolArray(true, __('delete-notification-heading'), __('delete-notification-description'));
	}

	function deleteCurrencyRequest($currencyId)
	{
		$response = $this->deleteCurrency($currencyId);
		return HTTP::inStringResponse($response);
	}


	/**
	 * Currency: Other
	 */

	function switchCurrency($currencyId, $redirect = false)
	{
		$currency = Currency::getCurrency($currencyId);
		if ($currency === NULL) return false;

		setcookie("currency", $currencyId, 0, "/");

		if(!$redirect) return true;

		return redirect()->back();
	}
}
