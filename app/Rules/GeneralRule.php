<?php

namespace App\Rules;

use Illuminate\Support\Facades\Validator;

class GeneralRule
{

	function __construct()
	{
		Validator::extend("first_name", function ($attribute, $value, $parameters, $validator) {
			$label = $parameters[0] ?? "First Name";

			if ((is_string($value) && empty(trim($value))) || !isset($value)) {
				$validator->errors()->add($attribute, "$label: Field is required.");
				return false;
			} else if (!is_string($value)) {
				$validator->errors()->add($attribute, "$label: Only alphabets are allowed.");
				return false;
			} else if (strlen(trim($value)) > 30) {
				$validator->errors()->add($attribute, "$label: Must not exceed 30 characters.");
				return false;
			} else if (!preg_match('/^[\p{L}\s\'-]+$/u', $value)) {
				$validator->errors()->add($attribute, "$label: Only letters, dashes, spaces, and apostrophes are allowed.");
				return false;
			}
			return true;
		});

		Validator::extend("last_name", function ($attribute, $value, $parameters, $validator) {
			$label = $parameters[0] ?? "Last Name";

			if (empty($value) && $value !== false && $value !== 0) return true;
			else if (!is_string($value)) {
				$validator->errors()->add($attribute, "$label: Only alphabets are allowed.");
				return false;
			} else if (empty(trim($value))) {
				$validator->errors()->add($attribute, "$label: Field is required.");
				return false;
			} else if (strlen(trim($value)) > 30) {
				$validator->errors()->add($attribute, "$label: Must not exceed 30 characters.");
				return false;
			} else if (!preg_match('/^[\p{L}\s\'-]+$/u', $value)) {
				$validator->errors()->add($attribute, "$label: Only letters, dashes, spaces, and apostrophes are allowed.");
				return false;
			}
			return true;
		});

		Validator::extend("phone", function ($attribute, $value, $parameters, $validator) {
			$label = $parameters[0] ?? "Phone";
			$pattern = '/^[+0-9 -]{1,15}$/';

			if (empty($value) && $value !== false && $value !== 0) return true;
			else if (strlen($value) < 5) {
				$validator->errors()->add($attribute, "$label: At least 5 digits are required.");
				return false;
			} else if (strlen($value) > 15) {
				$validator->errors()->add($attribute, "$label: Must not exceed 15 characters.");
				return false;
			} else if (!preg_match($pattern, $value)) {
				$validator->errors()->add($attribute, "$label: Only '+','-', space and numbers are allowed.");
				return false;
			}
			return true;
		});

		Validator::extend("strong_password", function ($attribute, $value, $parameters, $validator) {
			$error = false;
			if (strlen($value) < 8) $error = true;
			else if (strlen($value) > 150) $error = true;
			else if (!preg_match('/[A-Z]/', $value) || !preg_match('/[a-z]/', $value)) $error = true;
			else if (!preg_match('/[a-zA-Z]/', $value) || !preg_match('/[0-9]/', $value)) $error = true;

			if ($error) $validator->errors()->add($attribute, __("strong-password-notification"));
			return !$error;
		});
	}
}
