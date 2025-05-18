<?php

namespace App\Plugins\Component\Controller;

use App\Classes\Util;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PluginController;
use App\Plugins\Components\Helpers\Component as HelpersComponent;
use App\Plugins\Components\Model\Component as ModelComponent;
use App\Plugins\Components\Model\ComponentDataSection as ModelComponentDataSection;
use App\Plugins\Components\Model\ComponentDataSectionData as ModelComponentDataSectionData;
use App\Plugins\Components\Model\ComponentDataSectionDatai18n as ModelComponentDataSectionDatai18n;
use App\Plugins\Components\Model\ComponentDataSectioni18n as ModelComponentDataSectioni18n;
use HTTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ComponentController extends Controller
{

	// View

	function saveComponentView($componentSlug = NULL)
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$languages = Cache::get("languages");

		$pageData = [
			"tabTitle" => $componentSlug ? ucwords(__("edit component")) : ucwords(__("add new component")),
			"backURL" => Util::prefixedURL("/components"),
			"pageName" => $componentSlug ? ucwords(__("edit component")) : ucwords(__("add new component")),
			"pageSlug" => "components",
			"pluginConfig" => $config,
			"componentSlug" => $componentSlug,
			"languages" => json_encode($languages)
		];

		return PluginController::loadView(__DIR__, "save-component.blade.php", $pageData);
	}

	function componentsView()
	{
		$config = PluginController::getPluginConfig(__DIR__);

		$pageData = [
			"tabTitle" => __("components"),
			"backURL" => Util::prefixedURL("dashboard"),
			"pageName" => __("components"),
			"pageSlug" => "components",
			"pluginConfig" => $config,
		];

		return PluginController::loadView(__DIR__, "components.blade.php", $pageData);
	}

	/**
	 * Component: Get
	 */

	function component($componentSlug)
	{
		return HelpersComponent::component($componentSlug);
	}

	function components()
	{
		return ModelComponent::getComponents();
	}

	function findBySlugs($slugs)
	{
		if (sizeof($slugs) <= 0) return [];
		return ModelComponent::findComponentsBySlugs($slugs);
	}

	/**
	 * Component: Save
	 */

	function saveComponent($data, $componentSlug)
	{

		$validator = Validator::make($data, [
			"title" => "required|max:255",
			"visibility" => "required|max:255"
		], [
			'title.required'=>__('title-field-required'),
			'visibility.required'=>__('visibility-field-required')
		]);

		if ($validator->fails()) {
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}

		$languageCode = $data['languageCode'] ?? NULL;
		$primaryLanguage = primaryLanguage();

		ModelComponent::updateComponent($componentSlug, $data);

		if ($languageCode == $primaryLanguage['code']) {

			ModelComponentDataSection::deleteSectionBySlug($componentSlug);

			foreach ($data['sections'] ?? [] as $sectionIndex => $section) {
				$dataSection = ModelComponentDataSection::addSection($componentSlug, ($sectionIndex + 1));
				foreach ($section as $groups) {
					foreach ($groups as $subGroups) {
						foreach ($subGroups as $field) {
							$value = $field['value'];
							if (is_array($field['value'])) $value = $field['value']['id'];
							ModelComponentDataSectionData::addSectionData($field['label'], $value, $field['groupId'], $dataSection->id);
						}
					}
				}
			}
		} else {

			ModelComponentDataSectioni18n::deleteSectionBySlugAndLanguage($componentSlug, $languageCode);

			foreach ($data['sections'] ?? [] as $sectionIndex => $section) {
				$dataSection = ModelComponentDataSectioni18n::addSection($componentSlug, $languageCode, ($sectionIndex + 1));
				foreach ($section as $groups) {
					foreach ($groups as $subGroups) {
						foreach ($subGroups as $field) {
							$value = $field['value'];
							if (is_array($field['value'])) $value = $field['value']['id'];
							ModelComponentDataSectionDatai18n::addSectionData($field['label'], $value, $field['groupId'], $dataSection->id);
						}
					}
				}
			}
		}

		Cache::forget("components");

		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	//  Util

	function groupUntilColumn($arr)
	{
		if (count($arr) <= 0) {
			return [];
		}

		$mediaColumnNames = ["image", "image1", "image2", "image3", "logo", "picture", "video", "thumbnail", "thumbnail-video", "full-video"];

		$delimiter = $arr[0]['column_name'];
		$group = [];

		foreach ($arr as $obj) {
			if ($obj['column_name'] === $delimiter) {
				$group[] = [];
			}

			if (isset($obj['media']) && in_array($obj["column_name"], $mediaColumnNames)) {
				$group[count($group) - 1][$obj['column_name']] = $obj['media'];
			} else {
				$group[count($group) - 1][$obj['column_name']] = $obj['column_value'];
			}
		}

		return $group;
	}

	// Request

	function findBySlugsRequest(Request $request)
	{
		$data = $request->post();
		return $this->findBySlugs($data["slugs"] ?? []);
	}

	function saveComponentRequest(Request $request, $componentSlug = NULL)
	{
		$data = $request->post();
		$response = $this->saveComponent($data, $componentSlug);
		return HTTP::inStringResponse($response);
	}

	/**
	 * Component: Delete
	 */

	function deleteComponent($componentId)
	{
		$component = ModelComponent::getComponent($componentId);

		if (empty($component) || $component["persistence"] == "permanent") {
			return HTTP::inBoolArray(false, __('request-failed'), __('permanent-component-no-delete-notification-description'));
		}

		ModelComponent::deleteComponent($componentId);

		Cache::forget("components");

		return HTTP::inBoolArray(true, __('delete-notification-heading'), __('delete-notification-description'));
	}

	// Request

	function deleteComponentRequest($componentId){
		$response = $this->deleteComponent($componentId);
		return HTTP::inStringResponse($response);
	}

}
