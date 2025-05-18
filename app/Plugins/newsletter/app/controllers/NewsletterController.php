<?php

namespace App\Plugins\Newsletter\Controller;

require_once __DIR__ . "/../models/Newsletter.php";

use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Plugins\Newsletter\Model\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{

	/**
	 * ===== Views
	 */

	function newsletterView()
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => "Newsletter",
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => "Newsletter",
			"pageSlug" => "newsletter",
			"pluginConfig" => $config
		];

		return PluginController::loadView(__DIR__, "newsletter.blade.php", $pageData);
	}

	function addNewsletterView($newsletterId = NULL)
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => "Add Newsletter",
			"backURL" => Util::prefixedURL($config["slug"]),
			"pageName" => "Add Newsletter",
			"pageSlug" => "newsletter",
			"pluginConfig" => $config,
			"newsletterId"=>$newsletterId,
		];

		return PluginController::loadView(__DIR__, "save-newsletter.blade.php", $pageData);
	}


	/**
	 * Methods
	 */

	function getNewsletter(){
		return Newsletter::getAllNewsletter();
	}

	function getOneNewsletter($newsletterId){
		return Newsletter::getNewsletter($newsletterId);
	}

	function saveNewsletter($newsletterId = NULL, $data)
	{
		$validator = Validator::make($data, [
			"email"=>"required|email"
		]);

		if ($validator->fails()) return ["status" => "fail", "msg" => $validator->errors()->all()[0]];

		$message = "You have subscribed to the newsletter.";

		$newsletter = null;
		if($newsletterId === NULL) {
			$old = Newsletter::getNewsletterByEmail($data["email"]);
			if($old != NULL && $old["status"] == "subscribed") return ["status"=>"fail","msg"=>"You have already subscribed to the newsletter."];
			else if($old != NULL && $old["status"] == "unsubscribed") {
				$newsletterId = $old["id"];
				Newsletter::updateNewsletter($newsletterId, $data);
				$newsletter = $old;
			}
			else $newsletter = Newsletter::addNewsletter($data);
			
		}
		else {

			$old = Newsletter::getNewsletterByEmail($data["email"]);
			if($old !== NULL && $old["id"] != $newsletterId) return ["status"=>"fail","msg"=>"You have already subscribed to the newsletter."];
			$newsletter = $newsletter = Newsletter::updateNewsletter($newsletterId, $data);
			$message = "Newsletter updated.";
		}
		
		return ["status"=>"success", "msg"=>$message, "newsletter"=>$newsletter];
	}

	function deleteNewsletter($newsletterId){
		Newsletter::deleteNewsletter($newsletterId);
		return ["status"=>"success", "msg"=>"Newsletter deleted."];
	}

	/**
	 * Request
	 */

	function addNewsletterRequest(Request $request)
	{
		$data = $request->post();
		return $this->saveNewsletter(NULL, $data);
	}

	function updateNewsletterRequest(Request $request, $newsletterId = NULL)
	{
		$data = $request->post();
		return $this->saveNewsletter($newsletterId, $data);
	}

	

}
