<?php

namespace App\Http\Controllers;

use App\Services\ResetService;
use HTTP;
use Illuminate\Http\Request;

class ResetController extends Controller
{
	function resetView()
	{

		$activeResets = (new ResetService())->activeResets();

		$pageData = [
			"tabTitle" => __('reset'),
			"backURL" => url('/portal/dashboard'),
			"pageName" => __('reset'),
			"pageSlug" => "reset",
			"resets"=>$activeResets
		];

		return View("portal/reset", $pageData);
	}

	/**
	 * Reset: Request
	 */

	function resetRequest(Request $request){
		$data = $request->post();
		$response = (new ResetService())->doReset($data['id']);
		return HTTP::inStringResponse($response);
	}

	function resetAllRequest(){
		$response = (new ResetService())->doResetAll();
		return HTTP::inStringResponse($response);
	}

	function activeResetsRequest(Request $request){
		return (new ResetService())->activeResets();
	}

	function updateSettingRequest(Request $request){
		$data = $request->post();
		(new ResetService())->updateSetting($data['type'], $data['value']);
		return HTTP::inStringResponse(HTTP::inBoolArray(true, __('update-notification-heading'), __('update-notification-description')));
	}

}
