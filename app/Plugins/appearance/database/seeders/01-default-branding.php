<?php

use Illuminate\Support\Facades\DB;

return new class {
	public function run(){

		$defaultSettings = [
			[
				'column_name' => 'sidebarHeaderColor',
				'column_value' => '#2e2e33'
			],
			[
				'column_name' => 'sidebarColor',
				'column_value' => '#333338'
			],
			[
				'column_name' => 'sidebarDropdownColor',
				'column_value' => '#242428'
			],
			[
				'column_name' => 'sidebarTextColor',
				'column_value' => '#adb5bd'
			],
			[
				'column_name' => 'sidebarTextActiveColor',
				'column_value' => '#eaedf0'
			],
			[
				'column_name' => 'pageBackgroundColor',
				'column_value' => '#ffffff'
			],
			[
				'column_name' => 'pageHeaderBackgroundColor',
				'column_value' => '#ffffff'
			],
			[
				'column_name' => 'pageHeaderTextColor',
				'column_value' => '#484f56'
			],
			[
				'column_name' => 'pageHeaderIconColor',
				'column_value' => '#adb5bd'
			],
			[
				'column_name' => 'pageHeaderIconHoverColor',
				'column_value' => '#eaedf0'
			],
			[
				'column_name' => 'buttonBorderRadius',
				'column_value' => 4
			],
			[
				'column_name' => 'primaryButtonColor',
				'column_value' => '#333338'
			],
			[
				'column_name' => 'primaryButtonTextColor',
				'column_value' => '#eaedf0'
			],
			[
				'column_name' => 'primaryButtonHoverColor',
				'column_value' => '#2e2e33'
			],
			[
				'column_name' => 'primaryButtonHoverTextColor',
				'column_value' => '#eaedf0'
			],
			[
				'column_name' => 'dangerButtonColor',
				'column_value' => '#dc3545'
			],
			[
				'column_name' => 'dangerButtonTextColor',
				'column_value' => '#f9fafb'
			],
			[
				'column_name' => 'dangerButtonHoverColor',
				'column_value' => '#b82736'
			],
			[
				'column_name' => 'dangerButtonHoverTextColor',
				'column_value' => '#f9fafb'
			],
			[
				'column_name' => 'tagSuccessBackgroundColor',
				'column_value' => '#198754'
			],
			[
				'column_name' => 'tagSuccessTextColor',
				'column_value' => '#eaedf0'
			],
			[
				'column_name' => 'tagDangerBackgroundColor',
				'column_value' => '#dc3545'
			],
			[
				'column_name' => 'tagDangerTextColor',
				'column_value' => '#eaedf0'
			],
			[
				'column_name' => 'tagWarningBackgroundColor',
				'column_value' => '#fd7e14'
			],
			[
				'column_name' => 'tagWarningTextColor',
				'column_value' => '#eaedf0'
			],
			[
				'column_name' => 'tableFiltersRadius',
				'column_value' => 4
			],
			[
				'column_name' => 'tableFilterbarBackgroundColor',
				'column_value' => '#eaedf0'
			],
			[
				'column_name' => 'tableFilterbarTextColor',
				'column_value' => '#484f56'
			],
			[
				'column_name' => 'tableHeaderBackgroundColor',
				'column_value' => '#eaedf0'
			],
			[
				'column_name' => 'tableHeaderTextColor',
				'column_value' => '#353b41'
			],
			[
				'column_name' => 'statsCardColor',
				'column_value' => '#fd7e14'
			],
			[
				'column_name' => 'brand-logo-size',
				'column_value' => 0
			],
			[
				'column_name' => 'brand-logo-light-size',
				'column_value' => 0
			]
		];
		
		DB::table("default_branding")->insert($defaultSettings);
	}
}

?>
