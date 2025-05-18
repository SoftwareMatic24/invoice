<?php

namespace App\Plugins\MediaCenter\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MediaCenterFolder extends Model {

	public $timestamps = false;
	
	protected $fillable = [
		"title",
		"user_id"
	];

	// Relation

	 function media(){
		return $this->hasMany(Media::class, "folder_id", "id");
	 }

	 function folderAccessibilities(){
		return $this->hasMany(MediaCenterFolderAccessibility::class, "media_center_folder_id", "id");
	 }


	//  Build

	 static function accessibilityForUserRoleRelation($userRole){
		return self::with("media")->whereHas("folderAccessibilities", function($query) use($userRole){
			$query->where("role_title", $userRole);
		});
	 }


	// Query

	 static function getAllFoldersAndMedia(){
		return self::with("media")->orderByDesc("id")->get();
	 }

	 static function getFolder($folderId){
		return self::with("folderAccessibilities")->where("id", $folderId)->first();
	 }

	 static function getUserFoldersAndMedia($userId){

		$sharedFolders = self::getUserSharedFolders($userId);

		$userFolders = self::with("media")->where("user_id", $userId)->get();

		$folders = array_merge($sharedFolders->toArray(), $userFolders->toArray());
		return $folders;
	 }

	 static function getUserFolder($userId, $folderId){
		return self::where("user_id",$userId)->where("id", $folderId)->first();
	 }

	 static function getUserFolderByTitle($userId, $folderName){
		return self::where("user_id",$userId)->where("title", $folderName)->first();
	 }

	 static function getUserSharedFolders($userId){
		
		$user = User::getUserById($userId);
		$userRole = $user["role_title"];
		$accessibilityRelation = self::accessibilityForUserRoleRelation($userRole);
		$folders = $accessibilityRelation->get();

		foreach($folders as $folder){
			$folder["shared"] = true;
		}
		return $folders;
	 }

	 static function addFolder($folderName, $userId = NULL){
		return self::create([
			"title"=>$folderName,
			"user_id"=>$userId
		]);
	 }

	 static function updateFolder($folderId, $folderName){
		return self::where("id", $folderId)->update([
			"title"=>$folderName
		]);
	 }

	 static function deleteFolder($folderId){
		return self::where("id", $folderId)->delete();
	 }

};
