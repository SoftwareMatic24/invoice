<?php

namespace App\Plugins\MediaCenter\Controllers;

use App\Classes\DateTime as MyDateTime;
use App\Classes\FS;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Plugins\MediaCenter\Models\Media;
use App\Plugins\MediaCenter\Models\MediaCenterFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class MediaCenterController extends Controller
{

	// Methods

	function resizeImage($file, $options)
	{
		$newWidth = $options["width"] ?? NULL;
		$relativeOutputDir = $options["output"] ?? NULL;

		if ($newWidth === NULL) return ["status" => "fail", "msg" => __('please-specify-width')];
		else if ($relativeOutputDir === NULL) ["status" => "fail", "msg" => __('output-dir-not-specified')];
		
		if (!function_exists('imagecreatefromjpeg')) return ["status" => "fail", "msg" => __('gd-library-not-installed')];

		$imageType = exif_imagetype($file->getPathname());

		if (!in_array($imageType, [IMAGETYPE_JPEG, IMAGETYPE_PNG])) return ["status" => "fail", "msg" => __('only-x-images-can-be-resized')];

		if ($imageType == IMAGETYPE_JPEG) $image = imagecreatefromjpeg($file->getPathname());
		else if ($imageType == IMAGETYPE_PNG) $image = imagecreatefrompng($file->getPathname());

		$width = imagesx($image);
		$height = imagesy($image);

		$newHeight = ($newWidth / $width) * $height;

		$newImage = imagecreatetruecolor($newWidth, $newHeight);

		imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

	
		$outputPath = storage_path($relativeOutputDir);

		if ($imageType == IMAGETYPE_JPEG) imagejpeg($newImage, $outputPath);
		else if ($imageType == IMAGETYPE_PNG) imagepng($newImage, $outputPath);

		imagedestroy($image);
		imagedestroy($newImage);

		return ["status"=>"success", "msg"=>__('image-resized')];
	}

	function getUserFoldersAndMedia($userId){
		return MediaCenterFolder::getUserFoldersAndMedia($userId);
	}


	// Requests

	function foldersAndMediaRequest(Request $request)
	{
		$userId = $request->user()->id;
		return $this->getUserFoldersAndMedia($userId);
	}

	function saveFolderReuqest(Request $request)
	{
		$data = $request->post();
		$userId = $request->user()->id;

		$validator = Validator::make(
			$data,
			[
				"folderName" => "required|regex:/^[a-zA-Z0-9\s_&\-]+$/"
			],
			[
				"folderName.regex" => "Only Alphanumeric, (-, _ ,&) or space is allowed."
			]
		);

		if ($validator->fails()) {
			return [
				"status" => "fail",
				"msg" => $validator->errors()->all()[0]
			];
		}

		$folder = MediaCenterFolder::getUserFolderByTitle($userId, $data["folderName"]);

		if($folder !== NULL && isset($data["id"])) {
			return [
				"status" => "fail",
				"msg" => __("folder-name-no-change-notification")
			];
		}

		if($folder !== NULL) {
			return [
				"status" => "fail",
				"msg" => __("folder-already-exists-notification")
			];
		}


		if ($data["id"] ?? false) {

			$canUpdate = $this->userCanUpdateFolder($userId, $data["id"]);
			if($canUpdate === false) return ["status"=>"fail", "msg"=> __("unauthorized")];

			MediaCenterFolder::updateFolder($data["id"], $data["folderName"]);
		} else {
			MediaCenterFolder::addFolder($data["folderName"], $userId);
		}

		return [
			"status" => "success",
			"msg" => __("folder-save-notification")
		];
	}

	function deleteFolderReuqest(Request $request, $folderId){

		$userId = $request->user()->id;

		$canUpdate = $this->userCanUpdateFolder($userId, $folderId);
		if($canUpdate === false) return ["status"=>"fail", "msg"=>__("unauthorized")];

		$media = Media::getFolderMedia($folderId);
		if(sizeof($media) > 0) return ["status"=>"fail", "msg"=> __("non-empty-folder-can-not-be-deleted")];

		MediaCenterFolder::deleteFolder($folderId);

		return ["status"=>"success", "msg"=> __("delete-folder-notification")];
	}

	function uploadFileRequest(Request $request)
	{
		$file = $request->file("file");
		$folderId = $request->input("folderId");
		$fileSize = $request->input("fileSize");
		$folder = $request->input("folder");
		$storage = $request->input("storage");
		$userId = $request->user()->id;
		$date = Date("d-m-Y");

		
		$data = [
			"file" => $file ?? NULL,
			"folderId" => $folderId ?? NULL,
			"storage"=>$storage ?? NULL
		];

		$validator = Validator::make($data, [
			"file" => "required|file",
			"folderId" => "required",
			"storage"=>"required:in:public,private"
		]);

		if ($validator->fails()) {
			return [
				"status" => "fail",
				"msg" => $validator->errors()->all()[0]
			];
		}

		$canUpdate = $this->userCanUpdateFolder($userId, $folderId);
		if($canUpdate === false) return ["status"=>"fail", "msg"=> __("unauthorized")];

		$fileNameOnly = $file->getClientOriginalName();
		$fileExtension = $file->getClientOriginalExtension();
		$fileNameOnly = str_replace(".$fileExtension", "", $fileNameOnly);
		$fileNameOnly = Str::slug($fileNameOnly);

		$fileName = $fileNameOnly . "." . $fileExtension;

		$uploadDir = "$storage/$userId/$date";
		if(!empty($folder)) $uploadDir = "$storage/$folder/$userId/$date";

		//save original file
		$mediaData = [
			"url" => str_replace("$storage/", "", $uploadDir) . "/$fileName",
			"type" => $file->getMimeType(),
			"options" => [
				"size"=>$fileSize
			],
			"user_id" => $userId,
			"folder_id" => $folderId,
			"private"=>$storage === "private" ? true : false,
			"create_datetime" => MyDateTime::getDateTime()
		];
		

		FS::createFolder(["path"=>storage_path("app/".$uploadDir)]);
		$file->storeAs($uploadDir, $fileName);

		chmod(storage_path("app/public/".$userId), 455);
		chmod(storage_path("app/".$uploadDir), 455);

		$imageValidator = Validator::make($data, [
			"file" => "image|mimes:jpeg,jpg,png"
		]);

		// save thumbnail
		if (!$imageValidator->fails()) {
			$thumbnailResponse = $this->resizeImage($file, ["width"=>215, "output"=>"app/$uploadDir/thumbnail-$fileName"]);
			if($thumbnailResponse["status"] === "success") $mediaData["thumbnail"] = str_replace("$storage/", "", $uploadDir) . "/thumbnail-$fileName";
		}

		Media::addMedia($mediaData);
		return ["status" => "success", "msg" => __("file-upload-notification")];
	}

	function saveMediaRequest(Request $request){

		$data = $request->post();
		$userId = $request->user()->id;
		$userRole = $request->user()->role_title;

		$validator = Validator::make($data, [
			"id"=>"required",
			"name"=>"required|min:3|regex:/^[a-zA-Z0-9-]+$/"
		], [
			"name.regex"=>__("invalid-file-name-notification")
		]);

		if ($validator->fails()) {
			return [
				"status" => "fail",
				"msg" => $validator->errors()->all()[0]
			];
		}

		Cache::clear();

		$mediaId = $data["id"];
		
		$media = Media::getMedia($mediaId);
		if($media === null) return ["status"=>"fail", "msg"=> __("file-not-found-notification")];

		$canUpdate = $this->userCanUpdateFolder($userId, $media["folder_id"]);
		if($canUpdate === false) return ["status"=>"fail", "msg"=> __("unauthorized")];


		$mediaURL = $media["url"];
		$mediaThumbnail = $media["thumbnail"];
		$mediaExtension  = pathinfo($mediaURL, PATHINFO_EXTENSION);
		$mediaFileName = basename($mediaURL);
		$mediaFileNameOnly = pathinfo(basename($mediaURL), PATHINFO_FILENAME);

		$options = $media["options"];
		if($options === NULL) $options = [];
		else $options = json_decode($options, true);

		if(strpos($media["type"], "image/") !== false){
			$exceptions = ["id", "name"];
			foreach($data as $key=>$value){
				if(!in_array($key, $exceptions)) $options[$key] = $value !== null ? $value : "";
			}
			Media::updateOptions($mediaId, $options);
		}
		else if(strpos($media["type"], "video/") !== false){
			$exceptions = ["id", "name"];
			foreach($data as $key=>$value){
				if(!in_array($key, $exceptions)) $options[$key] = $value !== null ? $value : "";
			}
			Media::updateOptions($mediaId, $options);
		}

		// rename file
		if($mediaFileNameOnly !== $data["name"]) {
			$newMediaURL = str_replace(basename($mediaURL), $data["name"].".$mediaExtension", $mediaURL);
			if(Storage::move("public/".$mediaURL, "public/".$newMediaURL)){
				Media::updateColumn($media["id"], "url", $newMediaURL);
			}
		}

		return ["status"=> "success", "msg"=> __("media-file-update-notification")];
	}

	function moveMediaRequest(Request $request){
		
		$data = $request->post();
		$userId = $request->user()->id;
		$userRole = $request->user()->role_title;

		$fromFolderId = $data["fromFolderId"] ?? NULL;
		$toFolderId = $data["toFolderId"] ?? NULL;
		$mediaIds = $data["mediaIds"] ?? [];
		
		$validator = Validator::make($data, [
			"toFolderId"=>"required"
		]);

		if ($validator->fails()) {
			return [
				"status" => "fail",
				"msg" => $validator->errors()->all()[0]
			];
		}

		if(sizeof($mediaIds) <= 0 && ($fromFolderId == NULL || $fromFolderId == "" || $fromFolderId == "-1")) {
			return ["status"=>"fail", "msg"=> __("from-folder-required-notification")];
		}
		
		$canUpdate = $this->userCanUpdateFolder($userId, $toFolderId);
		if($canUpdate === false) return ["status"=>"fail", "msg"=> __("unauthorized")];

		$canUpdate = $this->userCanUpdateFolder($userId, $fromFolderId);
		if($canUpdate === false && $fromFolderId !== NULL && $fromFolderId !== "" && $fromFolderId !== "-1") return ["status"=>"fail", "msg"=>__("unauthorized")];


		$canUpdateMedia = true;
		$mediaArr = Media::getMediaByIds($mediaIds);

		foreach($mediaArr as $media) {			
			$bool = $this->userCanUpdateFolder($userId, $media["folder_id"]);
			if($bool === false) $canUpdateMedia = false;
		}

		if($canUpdateMedia === false && sizeof($mediaArr) > 0) return ["status"=>"fail", "msg"=> __("can-not-move-shared-folder-media-notification")];

		if($toFolderId == "-1") return ["status"=>"fail", "msg"=> __("invalid-destination-folder-notification")];
		else if($fromFolderId == "-1") return ["status"=>"fail", "msg"=> __("invalid-source-folder-notification")];

		Media::changeMediaFolder($fromFolderId, $toFolderId, $mediaIds);

		return ["status"=>"success", "msg"=> __("media-items-moved-to-the-folder-notification")];
	}

	function deleteMediaRequest(Request $request){

		$userId = $request->user()->id;
		$userRole = $request->user()->role_title;

		$data = $request->post();
		$ids = $data["ids"] ?? [];

		if(sizeof($ids) <= 0) return ["status"=>"fail", "msg"=> __("select-at-least-one-file-notification")];

		$canUpdate = true;
		$mediaArr = Media::getMediaByIds($ids);

		foreach($mediaArr as $media) {
			$bool = $this->userCanUpdateFolder($userId, $media["folder_id"]);
			if($bool === false) $canUpdate = false;
		}

		if($canUpdate === false) return ["status"=>"fail", "msg"=> __("unauthorized")];

		Media::deleteMedia($ids);

		return ["status"=>"success", "msg"=> __("selected-media-delete-notification")];
	}

	
	// validations

	function userCanUpdateFolder($userId, $folderId){

		$user = User::getUserById($userId);
		$folder = MediaCenterFolder::getFolder($folderId);

		$sharedAccess = false;

		if($folder === NULL || $user === NULL) return false;

		foreach($folder->toArray()["folder_accessibilities"] as $accessibility){
			if($accessibility["role_title"] == $user["role_title"]) $sharedAccess = true;
		}
		
		if($sharedAccess === true) return true;
		else if($user["role_title"] === "admin") return true;
		else if($folder["user_id"] == $userId) return true;

		return false;
	}

}
