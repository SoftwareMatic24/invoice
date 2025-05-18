<?php

use App\Models\Category as ModelsCategory;
use App\Models\CategoryClassification;
use App\Models\Categoryi18n;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Category
{

	static function categories()
	{
		$categories = ModelsCategory::getCategories()->toArray();
		$categories = self::buildCategoryHierarchy($categories);
		return self::formatCategories($categories);
	}

	static function linearCategories(){
		return  ModelsCategory::getCategories()->toArray();
	}

	static function categoryById(null|int|string $id){
		$category = ModelsCategory::category($id);
		$category = !empty($category) ? $category->toArray() : NULL; 
		return self::formatCategory($category);
	}

	static function category($slug, $classification_slug = NULL)
	{
		$category = ModelsCategory::getCategoryBySlugAndClassification($slug, $classification_slug);
		if (empty($category)) return NULL;
		$category = $category->toArray();
		return self::formatCategory($category);
	}

	static function classificationCategories($classification_slug = NULL)
	{
		$categories = ModelsCategory::getCategoryByClassifcaition($classification_slug)->toArray();
		$categories = self::buildCategoryHierarchy($categories);
		return self::formatCategories($categories);
	}

	static function classificationFeatureCategories($classification_slug = NULL, $featured = true, $column = 'featured')
	{
		$categories = ModelsCategory::classificationFeatureCategories($classification_slug, $featured, $column)->toArray();
		$categories = self::buildCategoryHierarchy($categories);
		return self::formatCategories($categories);
	}

	static function classifications(){
		return CategoryClassification::rows()->toArray();
	}

	// Formatters

	static function formatCategories(array $categories)
	{
		return array_map(function ($category) {
			return self::formatCategory($category);
		}, $categories);
	}

	static function formatCategory(null|array $category)
	{

		$output = $category;
		$match = NULL;

		if(empty($category)) return $output;

		if (!isCurrentLanguagePrimary()) {
			$categoriesi18n = $category['categoryi18n'] ?? [];
			foreach ($categoriesi18n as $row) {
				if ($row['language_code'] == app()->getLocale()) $match = $row;
			}
		}

		if (!empty($match)) {
			$output['lang_category'] = [
				'name' => $match['name'],
				'description' => $match['description'],
				'svg_icon' => $match['svg_icon'],
				'image_icon' => $match['image_icon'],
			];
		} else {
			$output['lang_category'] = [
				'name' => $category['name'],
				'description' => $category['description'],
				'svg_icon' => $category['svg_icon'],
				'image_icon' => $category['image_icon'],
			];
		}


		$output['child_categories'] = self::formatCategories($output['child_categories'] ?? []);

		return $output;
	}

	static function buildCategoryHierarchy(array $categories)
	{

		$categoriesById = [];
		foreach ($categories as $category) {
			$category['child_categories'] = [];
			$categoriesById[$category['id']] = $category;
		}

		$hierarchy = [];
		foreach ($categoriesById as $id => &$category) {
			if (isset($category['parent_category_id']) && $category['parent_category_id']) {

				if (isset($categoriesById[$category['parent_category_id']])) {
					$categoriesById[$category['parent_category_id']]['child_categories'][] = &$category;
				} else {

					$hierarchy[] = &$category;
				}
			} else {
				$hierarchy[] = &$category;
			}
		}

		return $hierarchy;
	}


	// Checks

	static function hasi18n($category)
	{
		return !empty($category['categoryi18n'] ?? []);
	}

	/**
	 * Category: Save
	 */

	static function saveCategory(null|int|string $categoryId, int|string $user_id, array $data){

		if(empty($data['slug'])) $data['slug'] = Str::slug($data['name'] ?? '');
		
		$rules = [
			'name'=>'required|string|max:255',
			'slug'=>'required|regex:/^[a-zA-Z0-9\-:\/]+$/',
			'classificationSlug'=>'required|string',
			'desscrition'=>'nullable|string',
			'parentCategoryId'=>'nullable|integer',
			'featured1'=>'required|in:true,false',
			'featured2'=>'required|in:true,false',
			'featuredImageId'=>'nullable|integer'
		];

		
		$validator = Validator::make($data, $rules);

		if($validator->fails()){
			return HTTP::inBoolArray(false, __('action-required'), $validator->errors()->all()[0]);
		}

		$category = Category::category($data['slug'], $data['classificationSlug']);
		if(!empty($category) && $categoryId != $category['id']){
			return HTTP::inBoolArray(false, __('action-required'), __('slug-field-exists'));
		}

		if(!empty($data['languageCode']) && primaryLanguage()['code'] != $data['languageCode']){
			Categoryi18n::saveCategory($categoryId, $data);
		}
		else {
			ModelsCategory::saveCategory($categoryId, $user_id, $data);
		}

		return HTTP::inBoolArray(true, __('save-notification-heading'), __('save-notification-description'));
	}

	/**
	 * Category: Delete
	 */

	static function deleteCategory(int|string $categoryId){
		return ModelsCategory::deleteCategory($categoryId);
	}

}
