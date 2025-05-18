<?php

namespace App\Models;

use App\Classes\DateTime;
use App\Plugins\MediaCenter\Models\Media;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

	protected $fillable = [
		'name',
		'slug',
		'description',
		'svg_icon',
		'image_icon',
		'featured',
		'featured_2',
		'user_id',
		'category_classification_slug',
		'parent_category_id',
		'create_datetime',
		'update_datetime'
	];
	public $timestamps = false;

	function classification(){
		return $this->belongsTo(CategoryClassification::class, 'category_classification_slug', 'slug');
	}

	function childCategoris(){
		return $this->hasMany(Category::class, 'parent_category_id', 'id');
	}

	function categoryi18n(){
		return $this->hasMany(Categoryi18n::class, 'category_id', 'id');
	}

	function imageIcon(){
		return $this->belongsTo(Media::class, 'image_icon', 'id');
	}
	
	static function basicRelation(){
		return self::with('categoryi18n')
		->with('classification')
		->with('imageIcon');
	}

	// Query: Get

	static function category(null|int|string $id){
		return self::basicRelation()->where('id', $id)->first();
	}

	static function getCategoryBySlugAndClassification($slug, $classification_slug){
		return self::basicRelation()->where('slug', $slug)->where('category_classification_slug', $classification_slug)->first();
	}

	static function getCategoryByClassifcaition($classification_slug){
		return self::basicRelation()->where('category_classification_slug', $classification_slug)->orderBy('id', 'DESC')->get();
	}

	static function classificationFeatureCategories($classification_slug, $featured, $column){
		return self::basicRelation()
			->orderBy('id', 'DESC')
			->where('category_classification_slug', $classification_slug)
			->where($column, $featured)
			->get();
	}

	static function getCategories(){
		return self::basicRelation()->orderBy('id', 'DESC')->get();
	}

	// Query: Save

	static function saveCategory(null|int|string $categoryId, int|string $user_id, array $data){
		if(empty($categoryId)) {
			$c = self::addCategory($user_id, $data);
			return $c->id ?? NULL;
		}
		else {
			self::updateCategory($categoryId, $user_id, $data);
			return $categoryId;
		}
	}

	static function addCategory(int|string $user_id, array $data){
		return self::create([
			'name'=>$data['name'],
			'slug'=>$data['slug'],
			'description'=>$data['description'],
			'svg_icon'=>$data['svgIcon'] ?? NULL,
			'image_icon'=>$data['featuredImageId'],
			'featured'=>$data['featured1'] == 'true' ? true : false,
			'featured_2'=>$data['featured2'] == 'true' ? true : false,
			'user_id'=>$user_id,
			'category_classification_slug'=>$data['classificationSlug'],
			'parent_category_id'=>$data['parentCategoryId'],
			'create_datetime'=>DateTime::getDateTime()
		]);
	}

	static function updateCategory(null|int|string $categoryId, int|string $user_id, array $data){
		return self::where('id', $categoryId)->update([
			'name'=>$data['name'],
			'slug'=>$data['slug'],
			'description'=>$data['description'],
			'svg_icon'=>$data['svgIcon'] ?? NULL,
			'image_icon'=>$data['featuredImageId'],
			'featured'=>$data['featured1'] == 'true' ? true : false,
			'featured_2'=>$data['featured2'] == 'true' ? true : false,
			'user_id'=>$user_id,
			'category_classification_slug'=>$data['classificationSlug'],
			'parent_category_id'=>$data['parentCategoryId'],
			'update_datetime'=>DateTime::getDateTime()
		]);
	}

	// Query: Delete

	static function deleteCategory($categoryId){
		return self::where('id', $categoryId)->delete();
	}

}
