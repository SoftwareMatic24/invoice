<?php

namespace App\Plugins\Subscription\Model;

use App\Classes\DateTime;
use Exception;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPackageClassification extends Model
{

	protected $fillable = [
		'name',
		'slug',
		'create_datetime',
		'update_datetime'
	];

	public $timestamps = false;

	// Query: Get

	static function classifications()
	{
		return self::orderBy('id', 'DESC')->get();
	}

	static function classificationBySlug($slug)
	{
		return self::where('slug', $slug)->first();
	}

	// Query: Save

	static function saveClassification($name, $slug, $newSlug)
	{

		$record = self::classificationBySlug($slug);
		if (empty($record)) return self::addClassification($name, $newSlug);
		return self::updateClassification($name, $slug, $newSlug);
	}

	static function addClassification($name, $newSlug)
	{
		return self::create([
			'name' => $name,
			'slug' => $newSlug,
			'create_datetime'=>DateTime::getDateTime()
		]);
	}

	static function updateClassification($name, $slug, $newSlug)
	{
		return self::where('slug', $slug)
			->update([
				'name' => $name,
				'slug' => $newSlug,
				'update_datetime'=>DateTime::getDateTime()
			]);
	}


	// Query: Delete

	static function deleteClassificationBySlug($slug)
	{
		return self::where('slug', $slug)->delete();
	}
}
