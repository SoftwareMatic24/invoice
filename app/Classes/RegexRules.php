<?php

namespace App\Classes;


class RegexRules {
	static $alphabetsAndSpaces = '/^[\p{L}\s]+$/u';
	static $alphabetsAndSpaces_description = "Only alphabets and spaces are allowed";

	static $phoneNumber = '/^[0-9\s\+\-]+$/';
	static $phoneNumber_description = "Only digits, space, hyphen or + allowed";
	
	static $atLeastOneAlphabet = '/[a-zA-Z]/';
	static $atLeastOneAlphabet_description = "At least one alphabet is required";

	static $firstAndLastCharsAlphabets = '/^[a-zA-Z].*[a-zA-Z]$/';
	static $firstAndLastCharsAlphabets_description = "The first and last characters must be alphabetic";
}	


?>