<?php

namespace App\Http\Controllers;

use App\Models\Abilities;
use App\Models\Role;
use App\Models\User;
use HTTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{

	// views

	function roles_view(Request $request)
	{

		$user = $request["loggedInUser"];

		$pageData = [
			"pageName" => __("roles"),
			"pageSlug" => "roles",
			"navigation" => "roles",
			"previousPage" => url("/") . "/" . config("app.portal_prefix") . "/dashboard",
			"data" => [
				"user" => $user->toArray()
			]
		];

		return view("role/roles", $pageData);
	}

	function save_role_view(Request $request, $roleId = NULL)
	{

		$user = $request["loggedInUser"];

		$pageData = [
			"pageName" => $roleId == NULL ? ucwords(__("new role")) : __("role"),
			"pageSlug" => "role",
			"navigation" => "roles",
			"previousPage" => url("/") . "/" . config("app.portal_prefix") . "/roles",
			"data" => [
				"user" => $user->toArray(),
				"roleId" => $roleId
			]
		];

		return view("role/save", $pageData);
	}


	// Role: Get

	function allRoles()
	{
		return ["admin", "user"];
	}

	function getRole($roleId)
	{
		return Role::where('id', $roleId)->first();
	}

	function getRoleByTitle($title){
		return Role::where('title', $title)->first();
	}

	function getRoles()
	{
		return Role::with('abilities')->get();
	}

	/**
	 * Role: Save
	 */

	function saveRole(Request $request, $roleId = NULL)
	{
		$data = $request->post();

		$validator = Validator::make($data, [
			"title" => "required|string|max:255"
		],[
			'title.required'=>__('title-field-required')
		]);

		if ($validator->fails()) {
			return HTTP::inStringResponse(HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]));
		}

		$oldRole = $this->getRoleByTitle($data["title"]);

		if($oldRole !== NULL && $roleId == NULL) {
			return ["status"=>"fail", "msg"=> __("role-field-exists")];
		}
		else if($oldRole !== NULL && $roleId !== NULL && $oldRole["id"] != $roleId) {
			return ["status"=>"fail", "msg"=> __("role-field-exists")];
		}

		if ($roleId === NULL) {
			$role = Role::create([
				"title" => strtolower($data["title"])
			]);

			$roleId = $role->id;
		} else {

			Role::where("id", $roleId)->update([
				"title" => strtolower($data["title"])
			]);
		}

		return HTTP::inStringResponse(HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description')));
	}

	/**
	 * Role: Deleet
	 */

	function deleteRole($roleId)
	{

		$role = Role::where('id', $roleId)->first();
		$user = User::where('role_title', $role->title)->first();

		if ($user !== null) {
			return HTTP::inStringResponse(HTTP::inBoolArray(false, __('request-failed'), __("role-has-users-on-delete-notification")));
		}

		Role::where("id", $roleId)->delete();

		return HTTP::inStringResponse(HTTP::inBoolArray(true, __('delete-notification-heading'), __("delete-notification-description")));
		
	}

	/**
	 * Role Ability
	 */

	function removeAbilityRequest(Request $request){

		$data = $request->post();

		$role = $data["role"] ?? "";
		$ability = $data["ability"] ?? "";

		Abilities::where("ability", $ability)->where("role_title", $role)->delete();

		return HTTP::inStringResponse(HTTP::inBoolArray(true, __('update-notification-heading'), __("privilege-remove-from-role-notification-description")));
	}

	
}
