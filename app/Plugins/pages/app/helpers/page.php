<?php

namespace App\Plugins\Pages\Helpers;

use App\Plugins\Pages\Models\Page as ModelsPage;
use Illuminate\Support\Facades\Cache;
use DOMDocument;

class Page
{

	static $slug = NULL;
	static $meta = NULL;
	static $chainSlug = NULL;
	static $chainResult = NULL;

	/**
	 * Setters
	 */

	static function setSlug($slug)
	{
		self::$slug = $slug;
	}

	static function setChainSlug($slug)
	{
		self::$chainSlug = $slug;
	}

	static function setChainResult($value)
	{
		self::$chainResult = $value;
	}


	/**
	 * Getters
	 */

	static function title($returnType = NULL)
	{
		$page = self::page('value');
		if (empty($page)) return NULL;
		
		$title = NULL;

		if (!isCurrentLanguagePrimary() && self::hasi18n($page)) {
			$pagei18n = self::pagei18n($page, app()->getLocale());
			if (!empty($pagei18n))  $title = $pagei18n['page_title'];
		} else if (isCurrentLanguagePrimary()) $title = $page['page_title'];
		
		if (self::isChain() && $returnType !== 'value') return self::buildChain($title);
		return $title;
	}

	static function description($returnType = NULL)
	{
		$page = self::page('value');
		if (empty($page)) return NULL;

		$description = NULL;

		if (!isCurrentLanguagePrimary() && self::hasi18n($page)) {
			$pagei18n = self::pagei18n($page, app()->getLocale());
			if (!empty($pagei18n))  $description = $pagei18n['description'];
		} else if (isCurrentLanguagePrimary()) $description = $page['description'];

		if (self::isChain() && $returnType !== 'value') return self::buildChain($description);
		return $description;
	}

	static function slug($returnType = NULL)
	{
		$page = self::page('value');
		if (empty($page)) return NULL;

		$slug = $page['slug'];

		if (self::isChain() && $returnType !== 'value') return self::buildChain($slug);
		return $slug;
	}

	static function tabTitle($returnType = NULL)
	{
		$meta = self::meta('value');
		$tabTitle = $meta['tabTitle'] ?? NULL;
		if (self::isChain() && $returnType !== 'value') return self::buildChain($tabTitle);
		return $tabTitle;
	}

	static function metaDescription($returnType = NULL)
	{
		$meta = self::meta('value');
		$description = $meta['metaDescription'] ?? NULL;
		if (self::isChain() && $returnType !== 'value') return self::buildChain($description);
		return $description;
	}

	static function author($returnType = NULL)
	{
		$meta = self::meta('value');
		$author = $meta['metaAuthor'] ?? NULL;
		if (self::isChain() && $returnType !== 'value') return self::buildChain($author);
		return $author;
	}

	static function meta($returnType = NULL)
	{

		$page = self::page('value');
		if (empty($page)) return NULL;

		$meta = NULL;

		if (!isCurrentLanguagePrimary() && self::hasi18n($page)) {
			$pagei18n = self::pagei18n($page, app()->getLocale());
			if (!empty($pagei18n))  $meta = $pagei18n['meta'];
		} else if (isCurrentLanguagePrimary()) $meta = $page['meta'] ?? NULL;

		if (!empty($meta)) $meta = json_decode($meta, true);
		if (!empty(self::$meta)) $meta = self::$meta;

		if (self::isChain() && $returnType !== 'value') return self::buildChain($meta);
		return $meta;
	}

	static function content($returnType = NULL)
	{
		$page = self::page('value');
		if (empty($page)) return NULL;

		$content = NULL;

		if (!isCurrentLanguagePrimary() && self::hasi18n($page)) {
			$pagei18n = self::pagei18n($page, app()->getLocale());
			if (!empty($pagei18n))  $content = $pagei18n['content'];
		} else if (isCurrentLanguagePrimary()) $content = $page['content'];

		if (!empty($content)) $content = json_decode($content, true);

		if (self::isChain() && $returnType !== 'value') return self::buildChain($content);
		return $content;
	}

	static function page($returnType = NULL)
	{
		$pages = self::pages();
		$slug = self::$chainSlug ?? self::$slug;
		$page = arrayFind($slug, $pages, "slug");
		if (self::isChain() && $returnType !== 'value') return self::buildChain($page);
		return $page;
	}

	static function pagei18n($page, $langCode)
	{
		return arrayFind($langCode, $page['pagei18n'] ?? [], 'language_code');
	}

	static function pages()
	{
		return Cache::get('publishedPages');
	}

	static function allPages(){
		return ModelsPage::getPages();
	}


	/**
	 * Chain
	 */

	static function get($slug)
	{
		self::setChainSlug($slug);
		return new self;
	}

	static function buildChain($value = NULL)
	{
		self::setChainResult($value);
		return new self;
	}

	static function done()
	{
		self::resetChain();
		return self::$chainResult;
	}

	/**
	 * Resets
	 */

	static function resetChain()
	{
		self::setChainSlug(NULL);
	}

	/**
	 * Methods
	 */

	static function setMeta($meta = NULL, $default = NULL)
	{
		if (empty($meta)) $meta = NULL;
		else if (is_string($meta)) $meta = json_decode($meta, true);

		if(empty($meta)) $meta = $default;
		
		self::$meta = $meta;
	}

	static function buildContent($content, $attributes)
	{	
		if(!is_array($content)) $content = [];

	
		return array_map(function ($contentSection) use ($attributes) {
			return self::buildContentSection($contentSection, $attributes);
		}, $content ?? []);
	}

	static function buildContentSection($contentSection, $attributes)
	{

		$title = $contentSection['title'];
		$content = $contentSection['content'] ?? NULL;
		
		if (empty($content)) return '';

		$rElements = self::strRootElements($content);
		$dom = $rElements['dom'];
		$elements = $rElements['elements'];

		$newContent = [];
		foreach ($elements as $element) {
			$newContent[] = self::depthFirstTraversal($dom, $element, $attributes);
		}

		return [
			'title'=>$title,
			'content'=>implode('', $newContent)
		];
	}

	static function depthFirstTraversal($dom, $element, $attributes, $depth = 0, $output = '')
	{
		if ($element->hasChildNodes()) {
			foreach ($element->childNodes as $child) {
				if ($child->nodeType === XML_ELEMENT_NODE) {
					$output .= self::depthFirstTraversal($dom, $child, $attributes, $depth + 1);
				}
			}
		}

		if ($element->nodeType !== XML_ELEMENT_NODE) return '';

		$tagName = $element->nodeName;
		return !isset($attributes[$tagName])
			? $dom->saveHTML($element)
			: self::attachElementLevelAttributes($dom, $element, $attributes[$tagName], $depth);
	}

	static function attachElementLevelAttributes($dom, $element, $elementLevelAttributes, $depth)
	{
		$key = isset($elementLevelAttributes["level-$depth"]) ? "level-$depth" : "default";
		$elementAttributes = $elementLevelAttributes[$key] ?? [];
		return self::attachElementAttributes($dom, $element, $elementAttributes);
	}

	static function attachElementAttributes($dom, $element, $elementAttributes) {

		if($element->nodeName === 'p' && trim($element->textContent) == "\u{00A0}") return '';

		foreach($elementAttributes as $attributeName=>$attributeValue){
			$element->setAttribute($attributeName, $attributeValue);
		}
		return $dom->saveHTML($element);
	}

	static function strRootElements($str)
	{
		$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($str, LIBXML_NOERROR | LIBXML_NOWARNING);
		libxml_clear_errors();
		return [
			'elements' => $dom->getElementsByTagName('body')->item(0)->childNodes,
			'dom' => $dom
		];
	}


	/**
	 * Utils
	 */

	static function isChain()
	{
		return !empty(self::$chainSlug);
	}

	static function hasi18n($page)
	{
		if ($page['pagei18n'] ?? NULL) return true;
		return false;
	}
}
