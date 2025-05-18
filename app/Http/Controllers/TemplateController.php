<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TemplateController extends Controller
{
	static function IMG_TAG_TEMPLATE($options = []){
		$attributes = [];

		$imageURL = $options["imageURL"] ?? NULL;
		$classes = $options["classes"] ?? "";
		$width = $options["width"] ?? NULL;
		$height = $options["height"] ?? NULL;
		$lazyLoading = $options["lazyLoading"] ?? false;
		$media = $options["media"] ?? NULL;
		$mediaOptions = $media["options"] ?? NULL;

	
		if($mediaOptions !== NULL) {
			$mediaOptions = json_decode($mediaOptions, true);
			
			$title = $mediaOptions["title"] ?? "";
			$alt = $mediaOptions["alt"] ?? "";

			$attributes[] = "title='$title'";
			$attributes[] = "alt='$alt'";
		}	
	
		
		if($lazyLoading === true) {
			$classes .= " lazy";
			$attributes[] = "data-src='$imageURL'";
			$attributes[] = "src='".config("app.url")."/assets/10x10-transparent.png'";
		}
		else $attributes[] = "src='$imageURL'";
		
		$attributes[] = "class='$classes'";

		if($width !== NULL) $attributes[] = "width='$width'";
		if($height !== NULL) $attributes[] = "height='$height'";

		
		$attributesStr = join(" ", $attributes);
		return "<img $attributesStr />";
	}
}
