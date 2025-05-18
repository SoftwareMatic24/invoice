<?php

namespace App\View\Components\Portal;

use App\Plugins\Appearance\Helpers\Appearance;
use Illuminate\View\Component;
use User;

class Sidebar extends Component
{

	public $pageSlug;

	function __construct($pageSlug)
	{
		$this->pageSlug = $pageSlug;
	}

	/**
	 * Get
	 */

	function pageSlug()
	{
		return $this->pageSlug ?? NULL;
	}

	function brandName()
	{
		return Appearance::brandName();
	}

	function brandPortalLogo()
	{
		return Appearance::brandPortalLogo();
	}

	function userName()
	{
		return User::userName();
	}

	function sidebarItems()
	{
		return $this->buildSidebarItems(session('loggedInUser')->toArray());
	}

	function specialStylingPlugin()
	{
		return env("PORTAL_STYLE_PLUGIN") ?? NULL;
	}


	/**
	 * Build
	 */

	function buildSidebarItems(array $authUser)
	{
		$defaultItems = $this->defaultSidebarItems();
		$sidebarPluginItems = $this->sidebarPluginItems($authUser);

		return array_merge($defaultItems, $sidebarPluginItems);
	}

	function defaultSidebarItems()
	{
		return [
			[
				'label' => $this->userName(),
				'href' => [
					'url' => 'void:javascript(0)'
				],
				'leftIcon' => [
					'name' => 'outline-user',
					'class' => 'icon',
					'style' => 'width: 17px;height:17px;'
				],
				'rightIcon' => [
					'name' => 'solid-chevron-right',
					'class' => 'chevron'
				],
				'children' => [
					[
						'label' => __('profile'),
						'href' => [
							'url' => '#',
							'attributes' => ["data-navigation=profile"]
						]
					],
					[
						'label' => __('activity log'),
						'href' => [
							'url' => '#',
							'attributes' => ["data-navigation=activity-log"]
						]
					],
					[
						'label' => __('logout'),
						'href' => [
							'url' => '#',
							'attributes' => ["data-navigation=logout"]
						]
					]
				],
				'rootClass' => 'show-on-lg'
			],
			[
				'type' => 'separator',
				'rootClass' => 'separator show-on-lg'
			],
			[
				'label' => __('dashboard'),
				'href' => [
					'url' => url('/portal/dashboard')
				],
				'leftIcon' => [
					'name' => 'outline-dashboard',
					'class' => 'icon',
					'style' => 'width: 1.8rem;height:1.8rem;'
				],
				'rootClass' => $this->pageSlug() === 'dashboard' ? 'highlight active' : ''
			]
		];
	}

	function sidebarPluginItems(array $authUser)
	{

		$output = [];
		$filteredPlugins = $this->filterSidebarPluginsByAbilities(sidebarPlugins());

		foreach($filteredPlugins as $plugin){

			$options = json_decode($plugin['options'], true);
			
			if ($options['separator'] ?? false){
				$output[] = [
					'type'=>'separator',
					'rootClass'=>'separator'
				];
			}

			$output[] = [
				'label' => ucfirst(__(strtolower($plugin['title']))),
				'href' => $this->buildPluginHref($plugin),
				'leftIcon' => $this->buildPluginLeftIcon($plugin),
				'rightIcon' => $this->buildPluginRightIcon($plugin),
				'children' => $this->buildPluginChildren($plugin),
				'rootClass' => $this->buildPluginRootClass($plugin, $authUser),
				'rootAttributes' => $this->buildPluginRootAttributes($plugin)
			];

		}

		return $output;
	}

	function filterSidebarPluginsByAbilities($plugins)
	{
		$userAbilities = User::authUserAbilities($_COOKIE);
		return array_filter($plugins, function ($plugin) use ($userAbilities) {

			$options = json_decode($plugin['options'], true);

			$parentAbilitiesRequired = $options["abilities"] ?? NULL;
			$parentAbilityRequired = $options["ability"] ?? NULL;

			if (!empty($parentAbilitiesRequired)) {
				$intersect = array_intersect($parentAbilitiesRequired, $userAbilities);

				if (count($intersect) !== count($parentAbilitiesRequired)) return false;
			} else if (!empty($parentAbilityRequired)) {
				$intersect = array_intersect($parentAbilityRequired, $userAbilities);
				if (count($intersect) > 0) return true;
				else return false;
			}

			return true;
		});
	}

	function buildPluginHref($plugin)
	{
		$options = json_decode($plugin['options'], true);
		return [
			'url' => isset($options['onclick']) ? '#' : url('/portal/' . $plugin['slug']),
			'onclick' => $options['onclick'] ?? (isset($options['children']) ? 'return false' : '')
		];
	}

	function buildPluginLeftIcon($plugin)
	{
		$options = json_decode($plugin['options'], true);
		$slug = $plugin['type'] === 'parent' ? $plugin['slug'] : $options['childOf'];
		$icon = $options['icon'] ?? 'none';

		$slug = empty($this->specialStylingPlugin()) ? $slug : $this->specialStylingPlugin();
		$svgIcon = pluginIcon($slug, $icon, ['classes' => ['icon'], 'style' => $options['icon-style'] ?? '']);

		return ['type' => 'raw', 'content' => $svgIcon];
	}

	function buildPluginRightIcon($plugin)
	{
		$options = json_decode($plugin['options'], true);
		if (empty($options['children'])) return NULL;

		return [
			'name' => 'solid-chevron-right',
			'class' => 'chevron'
		];
	}

	function buildPluginChildren($plugin)
	{
		$options = json_decode($plugin['options'], true);
		if (empty($options['children'])) return NULL;

		$userAbilities = User::authUserAbilities($_COOKIE);
		$children = $options['children'];

		$children = array_filter($children, function ($child) use($userAbilities) {

			$childAbilitiesRequired = $child['abilities'] ?? null;

			if (!empty($childAbilitiesRequired)) {
				
				$intersect = array_intersect($childAbilitiesRequired, $userAbilities);
				
				if (count($intersect) !== count($childAbilitiesRequired)) return false;
				
			}

			return true;
		});

		return array_map(function ($child) use ($options, $plugin) {
			$childURL = !empty($options['childOf']) ? url('/portal/' . $options['childOf'] . '/' . $child['slug']) : url('/portal/' . $plugin['slug'] . '/' . $child['slug']);
			return [
				'label' => ucfirst(__(strtolower($child['title']))),
				'href' => [
					'url' => $childURL
				],
				'rootAttributes' => [
					'data-plugin-generic=' . $child['slug'] . '--count'
				],
				'rootClass' => $plugin['slug'] . '-' . $child['slug'] . ' ' . ($child['classes'] ?? '') . ' ' . (($child['navigationSlug'] ?? NULL) === $this->pageSlug() ? 'active highlight' : '')
			];
		}, $children);
	}

	function buildPluginRootClass(array $plugin, array $authUser)
	{
		
		$options = json_decode($plugin['options'], true);
		$parentNavigationSlug = $options['navigationSlug'] ?? "";

		$classes = $options["classes"] ?? [];
		if ($parentNavigationSlug === $this->pageSlug()) $classes[] = 'active highlight';

		if (isset($options['children'])) {
			foreach ($options['children'] as $child) {
				if (($child['navigationSlug'] ?? 'NULL') === $this->pageSlug()) $classes[] = "active highlight";
				// $activeChildSlug = $child['slug'];
			}
		}

		if(isset($options['hideFromRoles']) && in_array($authUser['role_title'], $options['hideFromRoles'])) $classes[] = 'hide';

		return implode(' ', $classes);
	}

	function buildPluginRootAttributes($plugin)
	{
		return [
			'data-plugin=' . $plugin['slug'],
			'data-plugin-generic=' . $plugin['slug'] . '--count'
		];
	}

	/**
	 * Main
	 */

	function render()
	{
		return view('components.portal.sidebar');
	}
}
