<?php

namespace App\Http\Controllers;

use App\Classes\DateTime;
use App\Models\Action;
use App\Models\UserSetting;
use App\Plugins\AffiliateHive\Model\AffiliateHiveReferral;
use App\Plugins\MediaCenter\Models\MediaCenterFolder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PluginBridgeController extends Controller
{
	function bridge($hookName = NULL, $data)
	{

		// TODO: make dynamic for each plugin
		$hookNames = [

			"ADD_USER" => function ($data) {
				$passwordMailOther = [];

			
				if (class_exists(\App\Plugins\AffiliateHive\Controller\AffiliateHiveController::class)) {
					require_once __DIR__ . "/../../Plugins/affiliate-hive/app/models/AffiliateHiveReferral.php";
					$affiliateHiveSlug = request()->cookie('affiliate-hive-slug');
					if ($affiliateHiveSlug === NULL) $affiliateHiveSlug = $data["affiliateHiveSlug"] ?? NULL;


					if (isset($affiliateHiveSlug) && $affiliateHiveSlug !== NULL) {
						$lastDashPosition = strrpos($affiliateHiveSlug, '-');
						$afterLastDash = substr($affiliateHiveSlug, $lastDashPosition + 1);

						AffiliateHiveReferral::addUser([
							"firstName" => $data["first_name"],
							"lastName" => $data["last_name"],
							"email" => $data["email"],
							"affiliateId" => $afterLastDash
						]);
					}
				}

				if (class_exists(\App\Plugins\MediaCenter\Controllers\MediaCenterController::class)) {

					require_once __DIR__ . "/../../Plugins/media-center/app/models/MediaCenterFolder.php";

					MediaCenterFolder::addFolder("Unorganized", $data["id"]);
					$avatarsFolder = MediaCenterFolder::addFolder("Avatars", $data["id"]);

					$media = [
						[
							"url" => "avatars/avatar-1.png",
							"type" => "image/png",
							"folder_id" => $avatarsFolder["id"],
							"options" => json_encode([
								"size" => "28.9 KB"
							]),
							"user_id" => $data["id"],
							"create_datetime" => DateTime::getDateTime()
						],
						[
							"url" => "avatars/avatar-2.png",
							"type" => "image/png",
							"folder_id" => $avatarsFolder["id"],
							"options" => json_encode([
								"size" => "32.4 KB"
							]),
							"user_id" => $data["id"],
							"create_datetime" => DateTime::getDateTime()
						],
						[
							"url" => "avatars/avatar-3.png",
							"type" => "image/png",
							"folder_id" => $avatarsFolder["id"],
							"options" => json_encode([
								"size" => "31.7 KB"
							]),
							"user_id" => $data["id"],
							"create_datetime" => DateTime::getDateTime()
						],
						[
							"url" => "avatars/avatar-4.png",
							"type" => "image/png",
							"folder_id" => $avatarsFolder["id"],
							"options" => json_encode([
								"size" => "28.0 KB"
							]),
							"user_id" => $data["id"],
							"create_datetime" => DateTime::getDateTime()
						],
					];

					DB::table("media")->insert($media);
				}

				if (class_exists(\App\Plugins\DigiCheck\Controller\OrganizationController::class)) {

					$loginURL = url("/portal/client");
					if ($data["roleTitle"] === "admin") $loginURL = url("/portal/login");

					$instance = new (\App\Plugins\DigiCheck\Controller\OrganizationController::class);
					$organizationReference = $instance->organization($data["additionalDetails"]["organization"] ?? NULL);
					$reference = "";

					if ($organizationReference !== NULL) {
						$reference = $organizationReference["reference"];
						$passwordMailOther["withPassword"] = [
							"headers" => ["Organisation Reference"],
							"values" => [$reference]
						];
					}

					$passwordMailOther["loginURL"] = $loginURL;

					Action::addAction([
						"slug" => "FORCE_UPDATE_PASSWORD",
						"uid" => $data["id"]
					]);


					//Add Settings
					$userSettings = [
						"new-application-mail",
						"awaiting-action-mail",
						"application-comment-mail",
						"awaiting-payment-mail",
						"awaiting-id-check-mail",
						"application-processing-mail",
						"application-complete-mail",
						"awaiting-action-reminder-mail",
						"awaiting-payment-reminder-mail",
						"awaiting-id-check-reminder-mail"
					];

					foreach($userSettings as $setting){
						UserSetting::saveUserSetting($setting, 1, $data["id"]);
					}
				}
				
				return [
					"passwordMailOther"=>$passwordMailOther
				];
			},
			"AUTH_SUCCESSFUL" => function ($data) {
				$user = $data["user"] ?? NULL;
				$data = $data["data"] ?? NULL;

				if (class_exists(\App\Plugins\DigiCheck\Controller\DigiCheckController::class)) {
					if ($user === NULL) return;
					$reference = $data["reference"] ?? NULL;
					if ($user["role_title"] !== "admin" && $reference === NULL) return ["status" => "fail", "msg" => __("invalid-credentials-notification")];
					return NULL;
				}
			},
			"LOGOUT"=>function(){
				if(class_exists(\App\Plugins\Ecommerce\Controller\EcommerceController::class)){
					Session::put("sellerMode", "selling");
				}
			}
		];

		$hook = $hookNames[$hookName] ?? NULL;
		if ($hook === NULL) return;

		return $hook($data);
	}
}
