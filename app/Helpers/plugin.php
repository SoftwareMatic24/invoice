<?php

use App\Models\Plugin;
use Illuminate\Support\Facades\View;

if (!function_exists('sidebarPlugins')) {
	function sidebarPlugins()
	{
		return Plugin::getSidebarPlugins()->toArray();
	}
}

if (!function_exists('pluginFile')) {
	function pluginFile($relativePath, $pluginSlug, $options = NULL)
	{
		$path = pathJoin('app', 'Plugins', $pluginSlug, $relativePath);
		return loadFile($path, $options);
	}
}

if (!function_exists('loadPluginFile')) {
	function loadPluginFile($relativePath, $pluginSlug, $options = NULL)
	{
		echo pluginFile($relativePath, $pluginSlug, $options);
	}
}

if (!function_exists('pluginWidget')) {
	function pluginWidget($pluginSlug, $name, $options = [])
	{
		$file = app_path("Plugins/$pluginSlug/widgets/" . $name . ".blade.php");
		if (file_exists($file)) echo View::file($file, $options)->render();
	}
}

if (!function_exists('pluginIcon')) {
	function pluginIcon($pluginSlug, $iconName, $options = [])
	{

		$classes = $options["classes"] ?? [];
		$style = $options["style"] ?? "";

		$iconFile = app_path("Plugins/$pluginSlug/icons/$iconName.svg");

		$svg = '
			<svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" viewBox="0 0 408 408">
				<g>
					<g xmlns="http://www.w3.org/2000/svg">
						<g>
							<path d="M332,121.921H184.8l-29.28-34.8c-0.985-1.184-2.461-1.848-4-1.8H32.76C14.628,85.453,0,100.189,0,118.321v214.04    c0.022,18.194,14.766,32.938,32.96,32.96H332c18.194-0.022,32.938-14.766,32.96-32.96v-177.48    C364.938,136.687,350.194,121.943,332,121.921z">
							</path>
						</g>
					</g>
					<g xmlns="http://www.w3.org/2000/svg">
						<g>
							<path d="M375.24,79.281H228l-29.28-34.8c-0.985-1.184-2.461-1.848-4-1.8H76c-16.452,0.027-30.364,12.181-32.6,28.48h108.28    c5.678-0.014,11.069,2.492,14.72,6.84l25,29.72H332c26.005,0.044,47.076,21.115,47.12,47.12v167.52    c16.488-2.057,28.867-16.064,28.88-32.68v-177.48C407.957,94.1,393.34,79.413,375.24,79.281z">
							</path>
						</g>
					</g>
					<g xmlns="http://www.w3.org/2000/svg">
					</g>
				</g>
			</svg>
		';

		if (file_exists($iconFile)) $svg = file_get_contents($iconFile);

		$svg = simplexml_load_string($svg);
		$svg["class"] = implode(" ", $classes);
		$svg->addAttribute("style", $style);
		$svg = $svg->asXML();

		return $svg;
	}
}
