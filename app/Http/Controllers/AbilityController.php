<?php

namespace App\Http\Controllers;

use App\Models\Abilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPSTORM_META\map;

class AbilityController extends Controller
{



	/**
	 * ===== Abilities
	 */

	function general()
	{

		$roleController = new RoleController();
		$roles = $roleController->getRoles();

		return [
			"abilities" => $this->abilities(),
			"roles" => $roles
		];
	}

	function abilities()
	{

		return [
			"admin" => ["admin"],
			"user" => ["user"]
		];
	}

	function deleteAbility($abilityId)
	{

		Abilities::where("id", $abilityId)->delete();

		return Response()->json([
			"status" => "success",
			"msg" => "Privilege deleted"
		]);
	}

	function saveAbility(Request $request)
	{

		$data = $request->post();

		$validator = Validator::make($data, [
			"role" => "required",
			"abilities" => "required"
		]);


		if ($validator->fails()) {
			return Response()->json([
				"status" => "fail",
				"msg" => $validator->errors()->all()[0]
			]);
		}


		$currentRoleAbilities = Abilities::where("role_title", $data["role"])->get();

		$abilities = [];

		foreach ($data["abilities"] as $ability) {

			$match = false;

			foreach ($currentRoleAbilities as $cAbility) {
				if ($cAbility["ability"] == trim($ability)) $match = true;
			}

			if ($match === false) {
				Abilities::create([
					"ability" => $ability,
					"role_title" => $data["role"]
				]);
			}
		}




		return Response()->json([
			"status" => "success",
			"msg" => "Privileges saved"
		]);
	}
}
