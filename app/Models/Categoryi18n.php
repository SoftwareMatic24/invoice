<?php

namespace App\Models;

use App\Classes\DateTime;
use Illuminate\Database\Eloquent\Model;

class Categoryi18n extends Model
{
	protected $fillable = [
		'name',
		'description',
		'svg_icon',
		'image_icon',
		'language_code',
		'category_id',
		'create_datetime',
		'update_datetime'
	];
	protected $table = 'categories_i18n';
	public $timestamps = false;

	
	/**
	 * Query: Save
	 */

	static function saveCategory(int|string $categoryId, array $data){
		$row = self::rowByLangCodeAndCategoryId($data['languageCode'], $categoryId);
		
		if(empty($row)) {
			$c = self::addCategory($categoryId, $data);
			return $c->id ?? NULL;
		}
		else {
			self::updateCategory($categoryId, $data);
			return $row->id;
		}
	}

	static function addCategory(int|string $categoryId, array $data){
		return self::create([
			'name'=>$data['name'],
			'description'=>$data['description'],
			'svg_icon'=>$data['svgIcon'] ?? NULL,
			'image_icon'=>$data['featuredImageId'],
			'language_code'=>$data['languageCode'],
			'category_id'=>$categoryId,
			'create_datetime'=>DateTime::getDateTime()
		]);
	}

	static function updateCategory(int|string $categoryId, array $data){
		return self::where('language_code', $data['languageCode'])->where('category_id', $categoryId)->update([
			'name'=>$data['name'],
			'description'=>$data['description'],
			'svg_icon'=>$data['svgIcon'] ?? NULL,
			'image_icon'=>$data['featuredImageId'],
			'category_id'=>$categoryId,
			'update_datetime'=>DateTime::getDateTime()
		]);
	}

	/**
	 * Query: Row
	 */

	static function rowByLangCodeAndCategoryId(string $langCode, int|string $categoryId){
		return self::where('language_code', $langCode)->where('category_id', $categoryId)->first();
	}

}
