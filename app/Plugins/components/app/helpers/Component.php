<?php

namespace App\Plugins\Components\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Component {
	
	static function component($slug){
		$langCode = app()->getLocale();
		$components = Cache::get('components');
		
		$match = array_filter($components, function($component) use($slug) {
			if($component['slug'] === $slug) return true;
			return false;
		});

		if(empty($match)) return NULL;
		$match = reset($match);
		$component = self::formatComponent($match);
		
		$lanFDataSections = $component['f_data_sections'] ?? [];

		if(!isCurrentLanguagePrimary()) $lanFDataSections = $component['f_data_sections_i18n'][$langCode] ?? [];
		$component['lang_f_data_sections'] = $lanFDataSections;

		return $component;
	}

	static function components(){
		$output = [];
		$components = Cache::get('components');
		foreach($components as $component){
			$output[$component['slug']] = self::formatComponent($component);
		}
		return $output;
	}

	static function componentSections($slug){
		$component = self::component($slug);
		if(empty($component) || $component['visibility'] != 'visible') return [];
		return $component['lang_f_data_sections'] ?? [];
	}

	// Formatters

	static function formatComponent($component){
		if(empty($component)) return NULL;
		if($component instanceof Model) $component = $component->toArray();
		$component['f_data_sections'] = self::formatDataSections($component['data_sections']);
		$component['f_data_sections_i18n'] = self::formatDataSectionsi18n($component['data_sectionsi18n']);
		return $component;
	}

	static function formatDataSectionsi18n($dataSections){
		$output = [];
		$groupSections = [];

		foreach($dataSections as $section){
			$languageCode = $section['language_code'];
			$groupSections[$languageCode][] = $section;
		}

		foreach($groupSections as $groupSection){
			$languageCode = $groupSection[0]['language_code'];
			$output[$languageCode] = self::formatDataSections($groupSection);
		}

		return $output;
	}

	static function formatDataSections($dataSections){
		$sections = [];
		foreach($dataSections as $section){

			$groups = [];
			foreach($section['data'] as $row){
				$groupId = $row['component_group_id'];
				$groups[$groupId][] = $row;
			}

			$groups = array_values($groups);

			$subGroups = [];
			foreach($groups as $group){
				$subGroups[$group[0]['group']['name']] = self::groupUntilColumn($group); 
			}
			
			$sections[] = $subGroups;
		}
		return $sections;
	}

	// Util

	static function groupUntilColumn($arr)
	{
		if (count($arr) <= 0) return [];
		
		$mediaTypes = ["image", "video"];

		$delimiter = $arr[0]['label'];
		$group = [];
		
		

		foreach ($arr as $obj) {
			$hasMedia = false;

			foreach($obj['group']['schema'] as $s){
				if($obj['label'] == $s['label'] && in_array($s['type'], $mediaTypes)) $hasMedia = true;
			}

			if ($obj['label'] === $delimiter) $group[] = [];

			if ($hasMedia) {
				$group[count($group) - 1][$obj['label']] = $obj['media'];
				$group[count($group) - 1]['_schema'] = $obj;
			} else {
				$group[count($group) - 1][$obj['label']] = $obj['value'];
				$group[count($group) - 1]['_schema'] = $obj;
			}
		}
		
		return $group;
	}

	static function isVisible($slug){
		$component = self::component($slug);
		if(empty($component) || $component['visibility'] != 'visible') return false;
		return true;
	}

}

?>